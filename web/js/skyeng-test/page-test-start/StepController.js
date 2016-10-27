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
        AccessTokenStorage,
        Attempt_STATUS_FAIL,
        Attempt_STATUS_SUCCESS,
        Test_STATUS_SUCCESS,
        Test_STATUS_FAIL,
        Attempt_MAX
    ) {
        var vm = this;

        vm.rating = 0;
        vm.isEndTest = false;
        vm.canStartNewStep = false;
        vm.canAttempt = true;

        vm.attempts = Attempt_MAX;

        // Получаем данные шага
        vm.step = StepResource.get({stepId: $routeParams.stepId, accessToken: AccessTokenStorage.get()});

        /**
         * Обработка выбора перевода
         */
        vm.attempt = function (stepWordId) {
            if (!vm.canAttempt) {
                return;
            }

            vm.canAttempt = false;

            AttemptResource
                .save({
                    stepWordId: stepWordId,
                    stepId: vm.step.id,
                    accessToken: AccessTokenStorage.get()
                }).$promise
                .then(function (attempt) {
                    vm.attempts--;
                    vm.rating = attempt.rating;

                    // Выбрали правильный вариант
                    if (attempt.status == Attempt_STATUS_SUCCESS) {
                        // Вместе с попыткой завершился и тест
                        if (attempt.test.status == Test_STATUS_SUCCESS) {
                            vm.isEndTest = true;
                        } else {
                            vm.canStartNewStep = true;
                        }
                    }

                    // Выбрали не правильный вариант
                    if (attempt.status == Attempt_STATUS_FAIL) {
                        // Использовано максимально количество попыток, тест завершился
                        if (attempt.test.status == Test_STATUS_FAIL) {
                            vm.isEndTest = true;
                        } else {
                            // Остались еще попытки
                            vm.canAttempt = true;
                        }
                    }
                });
        };

        vm.nextStep = function () {
            StepResource.save({
                testId: vm.step.testId,
                accessToken: AccessTokenStorage.get()
            }).$promise.then(function (step) {
                $location.path('/test/' + step.testId + '/step/' + step.id);
            });
        };
    });