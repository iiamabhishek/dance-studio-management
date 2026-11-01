@echo off
cd /d C:\gymie
echo Starting Gymie at http://localhost:8000 ...
php artisan serve --host=0.0.0.0 --port=8000
