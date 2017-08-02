# MakerFaire SG 2017 - Animated Tweets

This web app is used to pull tweets from Twitter for a specific hashtag and post them to the Interactive Wall via a [NodeJS server](https://github.com/zionsg/MakerFaireSG2017-SwipeToSend/blob/master/server.js).

## Requirements
- PHP >= 7.0
- [Composer](https://getcomposer.org/)

## Installation
- Clone this repo.
- Run `composer install` to install PHP dependencies.
- Copy `config/autoload/credentials.local.php.dist` to `config/autoload/credentials.local.php` and update values accordingly.
- Copy `config/autoload/local.php.dist` to `config/autoload/local.php` and update values accordingly.
- Run `nohup php ./app/index.php &` in terminal. `nohup` ensures that the script continues to run after you exit the shell. The `&` will run it in the background and return the user to the prompt. Output and errors will be sent to `nohup.out` in the same directory.

## Troubleshooting
- In the event that `composer install` fails due to certain PHP extensions missing, the following commands can be run:

  ```
  sudo apt-get install composer

  sudo apt-get install zip

  sudo apt-get install php7.0-simplexml

  sudo phpenmod simplexml

  sudo apt-get install php7.0-curl

  sudo phpenmod curl

  sudo apt-get install php7.0-mbstring

  sudo phpenmod mbstring

  sudo service apache2 restart
  ```
