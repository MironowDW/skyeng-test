angular
    .module('skyeng-test')

    /**
     * Контроллер ввода имени пользователя и старта теста
     */
    .controller('TestStartController', function (
        $location,
        $q,
        UserResource,
        TestResource,
        StepResource,
        UserStorage
    ) {
        var vm = this;

        vm.username = '';
        vm.hasUser = UserStorage.hasUser();
        vm.user = UserStorage.get();

        vm.start = start;

        /**
         * Начать тест
         */
        function start() {
            if (!isValidUsername()) {
                alert('Не валидное имя пользователя');
                return;
            }

            createUser(this.username)
                .then(createTest)
                .then(createStep)
                .then(startStep);
        }

        function isValidUsername() {
            return vm.hasUser || vm.form.$valid;
        }

        /**
         * Создать пользователя
         */
        function createUser(username) {
            if (vm.hasUser) {
                return $q.when(vm.user);
            }

            return UserResource.save({username: username}).$promise.then(function (user) {
                UserStorage.set(user);
                return user;
            })
        }

        /**
         * Создать тест
         */
        function createTest(user) {
            return TestResource.save({accessToken: user.accessToken}).$promise;
        }

        /**
         * Создаем новый шаг в тесте
         */
        function createStep(test) {
            return StepResource.save({testId: test.id}).$promise;
        }

        /**
         * Редиректим на шаг
         */
        function startStep(step) {
            $location.path('/test/' + step.testId + '/step/' + step.id);
        }
    });