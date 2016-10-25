angular
    .module('skyeng-test', ['ngRoute', 'ngResource'])

    .config(function($routeProvider) {
        $routeProvider
            // TODO Использовать $state?
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
    .controller('StepController', function ($routeParams, StepResource, WordResource, AccessTokenStorage) {
        var vm = this;

        vm.variants = [];
        vm.word = null;

        StepResource
            .get({stepId: $routeParams.stepId, accessToken: AccessTokenStorage.get()}).$promise
            .then(function (step) {
                return WordResource.query({stepId: step.id}).$promise;
            })
            .then(function (words) {
                for (var i in words) {
                    if (words[i].isBase) {
                        vm.word = words[i];
                    } else {
                        vm.variants.push(words[i]);
                    }
                }
            });
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

    .factory('WordResource', function ($resource) {
        return $resource('/api/word');
    })
;