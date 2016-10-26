angular
    .module('skyeng-test')

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
    });