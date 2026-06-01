@echo off
echo Starting PHP Development Server on http://localhost:8080
echo Document Root: public folder
echo Press Ctrl + C to stop the server.
C:\xampp\php\php.exe -S localhost:8080 -t public public/index.php
pause
