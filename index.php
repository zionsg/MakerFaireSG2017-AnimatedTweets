<?php $config = include 'config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

  <head>
    <!-- Meta data -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title -->
    <title>MakerFaire SG 2017</title>

    <!-- Styles -->
    <link rel="stylesheet" href="node_modules/animate.css/animate.min.css">
  </head>

  <body>
    <div id="canvas">Tweets will come in after 2 seconds...</div>

    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="public/js/utils.js"></script>
    <script>
      var endpointUrl = '<?php echo $config['endpoint_url']; ?>';
      var tweets = []; // growing list of tweets
      var lastIdStr = ''; // keep track of id_str of latest tweet
      var getTweets = function () {
          utils.getTweets(lastIdStr, function (isSuccess, statusCode, responseData) {
              lastIdStr = responseData.last_id_str;
              tweets = tweets.concat(responseData.tweets);
          });
      };

      // Make 1st call and get tweets every 10s (10000ms)
      getTweets();
      var getTweetsInterval = window.setInterval('getTweets()', 10000);

      var $canvas = $('#canvas');
      var animateClass = 'animated bounce';
      var currTweetIndex = 0;
      var tweetCnt;
      var currTweet;
      var animateTweet = function () {
          tweetCnt = tweets.length;

          if (currTweetIndex > tweetCnt - 1) {
              currTweetIndex = 0;
          }

          currTweet = tweets[currTweetIndex] || null;
          currTweetIndex++;
          if (null == currTweet) {
              return false;
          }

          // Animate
          $canvas.removeClass().html(currTweet).addClass(animateClass)
              .one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                  $(this).removeClass();
              });

          // Send tweet to endpoint
          utils.sendTweet(endpointUrl, currTweet, function (isSuccess, statusCode, responseData) {});
      };

      // Make 1st call and animate a tweet every 2s (2000ms)
      animateTweet();
      var animateTweetInterval = window.setInterval('animateTweet()', 2000);
    </script>


  </body>
</html>