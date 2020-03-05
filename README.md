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

##### PHP version - 7.4.2 (Maybe you can run it in older versions, but I didn't test that)

#### libraries:
All what you need is written in composer.json file, so just install composer to project and use json file.

#### Extensions:
Don't forget to uncomment some extensions from your php.ini file:
* curl
* openssl

#### Database
You can easy import my DB schema by importDB.sql file.

#### Configs
I am using app.ini to keep some server info. In app.ini you can manage your own settings for DB, proxy, user agent, etc.
##### Dont forget to change it! 

#### Server
I am using nginx, here is my example config.

```
  worker_processes  2;

  events {
      worker_connections  1024;
  }

  http {
      include       mime.types;
      default_type  application/octet-stream;
      sendfile        on;
      keepalive_timeout  65;

      server {
          listen       80;
          server_name  localhost;
          root   src;

          location / {
              index  index.html index.htm;
          }

          location ~ \.php$ {
              include        fastcgi_params;
              try_files $uri =404;
              fastcgi_pass   127.0.0.1:9123;
              fastcgi_index  index.php;
              fastcgi_param  SCRIPT_FILENAME  /$document_root$fastcgi_script_name;
              include fastcgi.conf;
          }

          location css {
              root css;
          }

          location js {
                  root js;
              }

          error_page   500 502 503 504  /50x.html;
          location = /50x.html {
              root   src;
          }
      }
  }
```  
