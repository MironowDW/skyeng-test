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
;