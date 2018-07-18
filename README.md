# Chatterbot

## Requirments

- \> PHP 7.1
- MySQL/MariaDB
- [Composer](https://getcomposer.org/)

## Installation

Run `composer install` to install the dependencies.

Edit both `phinx.yml` and `app/config/config.php` to connect to your database.

Then run `./bin/phinx migrate` to create the tables in your database.

Now you should be done, you can use your own webserver or PHP `cd public; php -S localhost:8000` to access to the backend.

## Discord bot

Create a discord bot account: [https://discordapp.com/](https://discordapp.com/developers/applications/me).

Then add the bot token/name in app/config/config.php

To start the discord bot run `php discord.php`