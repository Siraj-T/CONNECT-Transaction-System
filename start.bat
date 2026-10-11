@echo off
echo ========================================================
echo Starting Financial Transaction System Development Servers
echo ========================================================
echo.
echo 1. Launching Laravel Web Server on http://127.0.0.1:8000 ...
start "Laravel HTTP Server" cmd /k "php artisan serve"
echo.
echo 2. Launching Laravel Reverb WebSocket Server on port 8080 ...
start "Laravel Reverb WebSocket" cmd /k "php artisan reverb:start --host=127.0.0.1 --port=8080"
echo.
echo All development servers are running!
echo Access the application at: http://127.0.0.1:8000
echo ========================================================
