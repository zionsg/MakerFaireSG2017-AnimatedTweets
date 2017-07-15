# MakerFaire SG 2017 - Animated Tweets

## Requirements
- PHP >= 7.0
- [Composer](https://getcomposer.org/)

## Installation
- Clone this repo.
- Run `composer install` to install PHP dependencies.
- Copy `config/autoload/credentials.local.php.dist` to `config/autoload/credentials.local.php` and update values accordingly.
- Copy `config/autoload/local.php.dist` to `config/autoload/local.php` and update values accordingly.
- Run `nohup php ./app/index.php &` in terminal. `nohup` ensures that the script continues to run after you exit the shell. The `&` will run it in the background and return the user to the prompt. Output and errors will be sent to `nohup.out` in the same directory.
