angular
    .module('skyeng-test', ['ngRoute', 'ngResource'])

    .config(function($routeProvider) {
        $routeProvider
            .when('/test/start', {
                templateUrl: '/js/skyeng-test/page-test-start/template.html',
                controller: 'TestStartController',
                controllerAs: 'TestStart'
            })
            .when('/test/:testId/step/:stepId', {
                templateUrl: '/js/skyeng-test/page-step/template.html',
                controller: 'StepController',
                controllerAs: 'Step'
            })

            .otherwise('/test/start');
    })

    .factory('UserStorage', function () {
        return {
            get: function () {
                return JSON.parse(window.localStorage.getItem('user'));
            },
            set: function (user) {
                window.localStorage.setItem('user', JSON.stringify(user));
            },
            hasUser: function () {
                return !!this.get();
            },
            getAccessToken: function () {
                return this.get().accessToken;
            }
        };
    })

    /**
     * Вставляем в каждый запрос токен, если он есть
     */
    .config(function ($httpProvider) {
        $httpProvider.interceptors.push(function(UserStorage) {
            return {
                request: function (config) {
                    if (!UserStorage.hasUser()) {
                        return config;
                    }

                    if (config.method == 'POST') {
                        if (!config.data) {
                            config.data = {};
                        }
                        config.data.accessToken = UserStorage.getAccessToken();
                    }

                    if (config.method == 'GET') {
                        if (!config.params) {
                            config.params = {};
                        }
                        config.params.accessToken = UserStorage.getAccessToken();
                    }

                    return config;
                }
            };
        });
    })
;