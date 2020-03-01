# proxy_tz
##### A php/js test project with cURL & guzzle 

![presentation.gif](https://github.com/GooseGame/proxy_tz/blob/master/presentation.gif)

## Some info
> this is a web app with native js on client and php on server.
* Server can dowload coupons from [here](coupons.com).
(site can be unavilible if you live in countries like Russia, so i used a cURL proxy connection)
* Then, parse info and put it into database (I'm using MySQL, but if you want to make it on another DB, you can just rewrite DBConnector.php)
* Server-side scripts can be easy automate via cron by execute (path/to/php path/to/project/src/updateDBInfo.php) from cron or make .bat (or what you have in your system) and also execute it by cron.

* Frontend contains some native js with AJAX connection to server.

## Requirements
#### libraries:
All what you need is written in composer.json file, so just install composer to project and use json file.

#### Extensions:
Don't forget to uncomment some extensions from your php.ini file:
* curl
* openssl

#### Database
You can easy import my DB schema by importDB.sql file.

