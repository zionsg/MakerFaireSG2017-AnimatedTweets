<?php

namespace App;

use TwitterAPIExchange;

class Application
{
    protected $settings;
    protected $url;
    protected $query;
    protected $method;
    protected $censoredWordsRegex;

    public function __construct(array $config)
    {
        $this->settings = $config['settings'];
        $this->url = $config['url'];
        $this->query = $config['query'];
        $this->method = $config['method'];

        $censoredWords = implode('|', $config['censored_words']);
        $this->censoredWordsRegex = "/(${censoredWords})/i";
    }

    /**
     * Call Twitter API to get tweets
     *
     * @return string JSON-encoded array of censored tweets
     */
    public function run()
    {
        $twitter = new TwitterAPIExchange($this->settings);
        $result = $twitter->setGetfield($this->query)
            ->buildOauth($this->url, $this->method)
            ->performRequest();

        $data = [];
        $tweets = json_decode($result, true);
        foreach ($tweets['statuses'] as $tweet) {
            $id = $tweet['id'];
            $text = $tweet['text'];
            if (preg_match($this->censoredWordsRegex, $text)) {
                continue;
            }

            $data[] = $text;
        }

        // Return JSON response
        header_remove();
        http_response_code(200);
        header('Content-Type: application/json; charset=utf8');
        echo json_encode($data);
    }
}
