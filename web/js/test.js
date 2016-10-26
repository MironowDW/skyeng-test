angular
    .module('skyeng-test', ['ngRoute', 'ngResource'])

    .config(function($routeProvider) {
        $routeProvider
            .when('/test/start', {
                templateUrl: 'template/test/start',
                controller: 'TestStartController',
                controllerAs: 'TestStart'
            })
            .when('/test/:testId/step/:stepId', {
                templateUrl: 'template/test/step',
                controller: 'StepController',
                controllerAs: 'Step'
            })

            .otherwise('/test/start');
    })

    /**
     * Контроллер ввода имени пользователя и старта теста
     */
    .controller('TestStartController', function ($location, UserResource, TestResource, StepResource, AccessTokenStorage) {
        this.username = '';

        this.start = function() {
            if (!this.form.$valid) {
                alert('Не валидное имя пользователя');
                return;
            }

            // TODO Переписать на сервис
            // TODO Вынести accessToken глобально

            // Сохраняем пользователя
            UserResource
                .save({username: this.username}).$promise
                // Создаем новый тест
                .then(function (user) {
                    AccessTokenStorage.set(user.accessToken);
                    return TestResource.save({accessToken: user.accessToken}).$promise;
                })
                // Создаем новый шаг в тесте
                .then(function (test) {
                    return StepResource.save({testId: test.id, accessToken: AccessTokenStorage.get()}).$promise;
                })
                // Шаг создан, редиректим на выполнение
                .then(function (step) {
                    $location.path('/test/' + step.testId + '/step/' + step.id);
                });
        };
    })

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
    })

    .factory('AccessTokenStorage', function () {
        var accessToken = null;

        return {
            get: function () {
                return !accessToken
                    ? window.localStorage.getItem('accessToken')
                    : accessToken;
            },
            set: function (_accessToken) {
                window.localStorage.setItem('accessToken', _accessToken);
                accessToken = _accessToken;
            }
        };
    })


    .factory('UserResource', function ($resource) {
        return $resource('/api/user');
    })

    .factory('TestResource', function ($resource) {
        return $resource('/api/test/:testId', {testId: '@testId'});
    })

    .factory('StepResource', function ($resource) {
        return $resource('/api/step/:stepId', {stepId: '@stepId'});
    })

    .factory('AttemptResource', function ($resource) {
        return $resource('/api/attempt/:attemptId', {attemptId: '@attemptId'});
    })


    .constant('Test_STATUS_SUCCESS', 1)
    .constant('Test_STATUS_FAIL', 2)

    .constant('Step_STATUS_SUCCESS', 1)
    .constant('Step_STATUS_FAIL', 2)

    .constant('Attempt_STATUS_SUCCESS', 0)
    .constant('Attempt_STATUS_FAIL', 1)

    .constant('Attempt_MAX', 3)
;