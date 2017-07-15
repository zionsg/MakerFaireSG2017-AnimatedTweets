/**
 * Utility functions
 */

var utils = (function () {
    // Self reference - all public vars/methods will be stored in here and returned as public interface
    var self = {};

    var endpointUrl = 'app/';

    /**
     * Get tweets
     *
     * @param  string lastIdStr id_str of last tweet
     * @param  callable responseCallback Takes in (isSuccess, statusCode, responseData) and returns void
     * @return void
     */
    self.getTweets = function (lastIdStr, responseCallback) {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: endpointUrl,
            data: {
                last_id_str: lastIdStr
            }
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

    /**
     * Send tweet to endpoint
     *
     * @param  string endpointUrl
     * @param  string tweet
     * @param  callable responseCallback Takes in (isSuccess, statusCode, responseData) and returns void
     * @return void
     */
    self.sendTweet = function (endpointUrl, tweet, responseCallback) {
        $.ajax({
            type: 'POST',
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
            url: endpointUrl,
            data: JSON.stringify({
                tweet: tweet
            })
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
