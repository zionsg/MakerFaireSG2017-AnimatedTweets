<?php

namespace App;

use TwitterAPIExchange;

class Application
{
    protected $settings;
    protected $url;
    protected $queryString;
    protected $query;
    protected $method;
    protected $censoredWordsRegex;
    protected $endpointUrl;
    protected $delaySeconds;

    protected $lastIdStr = '';
    protected $tweets = [];

    public function __construct(array $config)
    {
        $twitterConfig = $config['twitter'];
        $this->settings = $twitterConfig['settings'];
        $this->url = $twitterConfig['url'];
        $this->queryString = $twitterConfig['query_string'];
        $this->query = urlencode($twitterConfig['query']);
        $this->method = $twitterConfig['method'];

        $censoredWords = implode('|', $config['censored_words']);
        $this->censoredWordsRegex = "/(${censoredWords})/i";

        $this->endpointUrl = $config['endpoint_url'];
        $this->delaySeconds = $config['delay_seconds'];
    }

    /**
     * Run continuously - get tweets, send to endpoint url
     *
     * @return void
     */
    public function run()
    {
        $currTweetIndex = 0;

        echo "Tweets will be sent to {$this->endpointUrl} every {$this->delaySeconds} seconds.\n\n";
        while (true) {
            // Get tweets
            $this->getTweets();
            $tweetCnt = count($this->tweets);

            // Get current tweet
            $currTweet = $this->tweets[$currTweetIndex] ?? null;
            $currTweetIndex++;
            if (null === $currTweet) {
                continue;
            }

            // Send tweet to endpoint
            $this->call($this->endpointUrl, ['tweet' => $currTweet]);
            echo "{$currTweet}\n\n";

            // Delay
            @shell_exec("sleep {$this->delaySeconds}");
        }
    }

    /**
     * Call Twitter API to get tweets
     *
     * Additional query params will be added: q, since_id.
     * Using id_str instead of id cos integer id may be too big for PHP to handle properly.
     *
     * @link   https://dev.twitter.com/rest/reference/get/search/tweets
     * @return void
     */
    public function getTweets()
    {
        // Construct query string
        $lastIdStr = $this->lastIdStr;
        $queryString = sprintf(
            '%s&q=%s&since_id=%s',
            $this->queryString,
            $this->query,
            $lastIdStr
        );

        // Call Twitter API
        $twitter = new TwitterAPIExchange($this->settings);
        $result = $twitter->setGetfield($queryString)
            ->buildOauth($this->url, $this->method)
            ->performRequest();

        // Process
        $tweets = [];
        $data = json_decode($result, true);
        foreach (($data['statuses'] ?? []) as $tweet) {
            $idStr = $tweet['id_str'] ?? '';
            $text = $tweet['text'] ?? '';
            if (preg_match($this->censoredWordsRegex, $text)) {
                continue;
            }

            $lastIdStr = ($idStr > $lastIdStr) ? $idStr : $lastIdStr;
            $tweets[$idStr] = $text;
        }
        ksort($tweets); // oldest to newest

        // Update class vars
        $this->lastIdStr = $lastIdStr;
        foreach ($tweets as $idStr => $tweet) {
            $this->tweets[] = $tweet;
        }
    }

    /**
     * Send cURL request to external API
     *
     * @param  string $url
     * @param  array $data
     * @return array ['code' => <HTTP response code>, 'response' => <response data>]
     */
    protected function call($url, array $data)
    {
        if (! $url) {
            return [
                'code' => null,
                'response' => null,
            ];
        }

        $curlHandler = curl_init();
        curl_setopt_array($curlHandler, [
            CURLOPT_RETURNTRANSFER => true, // return value instead of output to browser
            CURLOPT_HEADER => false, // do not include headers in return value
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);
        $apiResponse = curl_exec($curlHandler);
        $curlInfo = curl_getinfo($curlHandler);
        $apiCode = $curlInfo['http_code'];
        curl_close($curlHandler);

        return [
            'code' => $apiCode,
            'response' => $apiResponse,
        ];
    }
}
