# GeoTweet

GeoTweet is a open source demo, and is built to showcase the use of Twitter Tweets Search
with Google Maps GeoCoding API as well as Google Maps Javascript API.

Author: Duc Nguyen <ducntq@gmail.com>

## Features

+ Utilize Google Maps Javascript API to display tweets' locations
+ Utilize Twitter REST API to search for tweets contains city name and
in radius of 50km from the center of a city
+ Utilize Google Maps GeoCoding API to search for city and its coordinates
+ Tweets and cities will be stored in database for caching purpose. Tweets will be refetched after
1 hour duration, while cities won't because cities are unlike to change
+ Responsive layout by using Twitter Bootstrap 3
+ Powered by Laravel 5
+ OOP javascript code
+ SASS (SCSS style) for stylesheets
+ Bower for frontend's package management, Gulp for streaming build
+ Composer for PHP's package management
+ Homestead as Vagrant box for developing environment

## Coding standards

Written code is strictly enforced by PSR-1, PSR-2, and PSR-4 standards. Meanwhile,
Javascript code is strictly enforced by [AirBNB's Javascript Style Guide](https://github.com/airbnb/javascript).

## Deploy GeoTweet

### Requirements

+ Node.js (recommended version >= 1.10.?)
+ PHP >= 5.4
+ PHP Mcrypt extension
+ PHP OpenSSL extension
+ PHP Mbstring extension
+ PHP Tokenizer extension
+ MySQL or MariaDB
+ Composer
+ Npm
+ Gulp & bower globally installed

### Installation

After cloning ``geotweet`` from github.com, edit configuration files in ``config`` directory: ``database.php``, 
``ttwitter.php``, ``geocoder.php``. You will need to obtain API keys for Twitter API & Google Maps API from:

+ [Twitter Apps](https://apps.twitter.com)
+ [Google Developers Console](https://console.developers.google.com/)

Open command line or terminal, change working directory to ``geotweet`` and run the following commands:

``bash
composer install --no-dev # install php packages
npm install # install nodejs packages
bower install # install frontend packages
DISABLE_NOTIFIER=true gulp --production # minify and combine assets
php artisan migrate
``

Then you're ready to go.