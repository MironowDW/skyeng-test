angular
    .module('skyeng-test', ['ngRoute', 'ngResource'])

    .config(function($routeProvider) {
        $routeProvider
            .when('/test/start', {
                templateUrl: 'template/test/start',
                controller: 'TestStartController',
                controllerAs: 'TestStart'
            })

            .otherwise('/test/start');
    })

    /**
     * Контроллер ввода имени пользователя и старта теста
     */
    .controller('TestStartController', function (UserResource) {
        this.username = '';

        this.start = function() {
            if (!this.form.$valid) {
                alert('Не валидное имя пользователя');
                return;
            }

            UserResource.save({username: this.username});
        };
    })

    .factory('UserResource', function ($resource) {
        return $resource('/api/user');
    })
;