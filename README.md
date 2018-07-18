# Chatterbot

## Requirments

- \> PHP 7.1
- MySQL/MariaDB
- [Composer](https://getcomposer.org/)

## Installation

Run `composer install` to install the dependencies.

Then copy/past app/config/config.php.dist to app/config/config.php and phinx.yml.dist to phinx.yml

Edit both config files to connect to your database.

Then run `./bin/phinx migrate` to create the tables in your database.

Now you should be done, you can use your own webserver or PHP `cd public; php -S localhost:8000` to access to the backend.

## Discord bot

Create a discord bot account: [https://discordapp.com/](https://discordapp.com/).

Then add the bot token/name in app/config/config.php

To start the discord bot run `php discord.php`