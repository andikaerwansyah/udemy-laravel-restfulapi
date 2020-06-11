## Nginx 502 Bad Gateway On Vagrant VM

1. Open xdebug.ini file

`sudo nano /etc/php/7.3/mods-available/xdebug.ini`

2. prefix all lines with ;

;zend_extension=xdebug.so
;xdebug.remote_enable = 1
;xdebug.remote_connect_back = 1
;xdebug.remote_port = 9000
;xdebug.max_nesting_level = 512

3. Restart Nginx

`sudo service nginx restart`

4. Restart PHP FPM
`sudo service php7.3-fpm restart`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
