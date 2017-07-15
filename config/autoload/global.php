<?php

return [
    // Twitter API settings
    'twitter' => [
        'settings' => [
            'oauth_access_token' => 'YOUR_OAUTH_ACCESS_TOKEN',
            'oauth_access_token_secret' => 'YOUR_OAUTH_ACCESS_TOKEN_SECRET',
            'consumer_key' => 'YOUR_CONSUMER_KEY',
            'consumer_secret' => 'YOUR_CONSUMER_SECRET',
        ],

        'url' => 'TWITTER_API_URL',
        'query' => 'QUERY_STRING_PARAMS',
        'method' => 'GET',
    ],

    // List of words to censor
    'censored_words' => [],

    // Endpoint to send tweet to
    'endpoint_url' => 'http://127.0.0.1:3000',

    // Delay between sending of each tweet
    'delay_seconds' => 2,
];
