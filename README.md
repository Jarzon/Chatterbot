# Chatterbot

## Requirments

- PHP 7
- PDO with SQLite enabled
- [Composer](https://getcomposer.org/)

## Installation

Run `composer install` to install the dependencies.

Edit both `phinx.yml` and `app/config/config.php` to connect to your database.

Then run `./bin/phinx migrate` to create the tables in the database.

## Discord bot

Create a discord bot account: [https://discordapp.com/](https://discordapp.com/developers/applications/me).

Then add the bot token/name in app/config/config.php

To start the discord bot run `php discord.php`

## Bot commands

If you want to exec a bot command in a chanel you have to mention the bot eg. `@botName add_question hello`

- add_question 
- add_response