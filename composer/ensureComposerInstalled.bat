@echo off
set file=composer.phar
if exist "%file%" (
    echo %file% found - thus Composer has been installed already. Skipping installation.
) else (
    echo %file% not found - thus Composer has not yet been installed. Installing...
    call installComposer.bat
)