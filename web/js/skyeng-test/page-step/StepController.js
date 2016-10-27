angular
    .module('skyeng-test')

    /**
     * Контроллер выполнения шага
     */
    .controller('StepController', function (
        $routeParams,
        $location,
        StepResource,
        AttemptResource,
        Attempt_STATUS_FAIL,
        Attempt_STATUS_SUCCESS,
        Test_STATUS_SUCCESS,
        Test_STATUS_FAIL,
        Test_STATUS_NEW,
        Attempt_MAX,
        Step_STATUS_NEW
    ) {
        var vm = this;

        vm.test = null;
        vm.step = null;
        vm.lastAttempt = null;

        vm.canAttempt = true;
        // Количество оставшихся попыток
        vm.attempts = Attempt_MAX;

        vm.canStartNewStep = canStartNewStep;
        vm.isEndTest = isEndTest;
        vm.attempt = attempt;
        vm.nextStep = nextStep;

        // Получаем данные шага и проверяем статус
        StepResource.get({stepId: $routeParams.stepId}).$promise.then(function (step) {
            checkStatus(step);

            vm.step = step;
            vm.test = step.test;
        });

        /**
         * Обработка выбора перевода
         */
        function attempt (stepWordId) {
            if (!vm.canAttempt) {
                return;
            }

            vm.canAttempt = false;

            AttemptResource
                .save({stepWordId: stepWordId, stepId: vm.step.id}).$promise
                .then(function (attempt) {
                    vm.attempts--;
                    vm.rating = attempt.test.rating;

                    // Обновляем состояние объектов
                    vm.lastAttempt = attempt;
                    vm.test = attempt.test;

                    // Если ичего не провалено - можем попробовать еще
                    if (attempt.step.status == Step_STATUS_NEW && vm.test.status == Test_STATUS_NEW) {
                        vm.canAttempt = true;
                    }
                });
        }

        /**
         * Можно перейти к следующему шагу, если есть успешная попытка и тест не завершился
         */
        function canStartNewStep() {
            if (!vm.lastAttempt) {
                return false;
            }

            if (vm.lastAttempt.status != Attempt_STATUS_SUCCESS) {
                return false;
            }

            if (isEndTest()) {
                return false;
            }

            return true;
        }

        function isEndTest() {
            return vm.test && vm.test.status != Test_STATUS_NEW;
        }

        /**
         * Перейти к следующему шагу
         */
        function nextStep () {
            StepResource.save({testId: vm.step.testId}).$promise.then(goNext);
        }

        function goNext(step) {
            $location.path('/test/' + step.testId + '/step/' + step.id);
        }

        function checkStatus(step) {
            if (step.status != Step_STATUS_NEW) {
                alert('Шаг завершен!');
                $location.path('/test/star');
            }
        }
    });