angular
    .module('skyeng-test')

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
;