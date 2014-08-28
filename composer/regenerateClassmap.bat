@echo off
echo Regenerating autoload classmap, with Composer.
php composer.phar --no-dev -o dump-autoload