/**
 * Utility functions
 */

var utils = (function () {
    // Self reference - all public vars/methods will be stored in here and returned as public interface
    var self = {};

    /**
     * Get tweets
     *
     * @param  string endpoint
     * @param  object requestParams
     * @param  callable responseCallback Takes in (isSuccess, statusCode, responseData) and returns void
     * @return void
     */
    self.getTweets = function (endpointUrl, responseCallback) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: endpointUrl,
            data: []
        }).done(function (data, textStatus, jqXHR) {
            var isSuccess = true,
                statusCode = jqXHR.status,
                responseData = data;

            console.log(statusCode, responseData);
            responseCallback(isSuccess, statusCode, responseData);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            var isSuccess = false,
                statusCode = jqXHR.status,
                responseData = jqXHR.responseJSON;

            console.log(statusCode, responseData);
            responseCallback(isSuccess, statusCode, responseData);
        });
    };

    // Return public interface
    return self;
})();
