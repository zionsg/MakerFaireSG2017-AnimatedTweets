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
    }

    /**
     * Call Twitter API to get tweets
     *
     * Additional query params will be added: q, since_id.
     * Using id_str instead of id cos integer id may be too big for PHP to handle properly.
     *
     * @link   https://dev.twitter.com/rest/reference/get/search/tweets
     * @return string JSON-encoded string
     *     {
     *         "last_id_str": "456",
     *         "tweets": [
     *             {"id_str":"123", "text": "Hello"},
     *             {"id_str":"456", "text": "World"}
     *         ]
     *     }
     */
    public function run()
    {
        // Get request param & add to query string
        $lastIdStr = isset($_GET['last_id_str']) ? $_GET['last_id_str'] : '';
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
        foreach ($data['statuses'] as $tweet) {
            $idStr = $tweet['id_str'];
            $text = $tweet['text'];
            if (preg_match($this->censoredWordsRegex, $text)) {
                continue;
            }

            $lastIdStr = ($idStr > $lastIdStr) ? $idStr : $lastIdStr;
            $tweets[$idStr] = $text;
        }
        ksort($tweets); // oldest to newest
        $tweets = array_values($tweets);

        // Return JSON response
        return $this->response([
            'last_id_str' => $lastIdStr,
            'tweets' => $tweets,
        ]);
    }

    /**
     * Return JSON response
     *
     * @param  array $data
     * @return void
     */
    protected function response(array $data, $responseCode = 200)
    {
        $response = json_encode($data);
        header_remove();
        http_response_code($responseCode);
        header('Content-Type: application/json; charset=utf8');
        echo $response;
        exit;
    }
}
