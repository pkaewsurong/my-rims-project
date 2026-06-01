# RIMS - Pure PHP Version

This project has been completely migrated from Laravel to a Pure PHP structure. All Laravel dependencies (`vendor`, `app`, `routes`, `artisan`) have been permanently removed.

## Project Structure
- `config/`: Contains database connection settings (`database.php`)
- `includes/`: Contains helper variables and functions (`functions.php`)
- `public/`: The web root and router index (`index.php`, `uploads/`)
- `src/`: Contains MVC Controllers processing logic (`ProjectController`, `AuthController`...)
- `views/`: Contains the pure PHP/HTML template files.

## How to run
1. Ensure your Apache/Nginx web server is pointing to the `public/` folder OR run the built-in server:
```
php -S localhost:8080 -t public
```
2. Navigate to `http://localhost:8080/`

## Note
As this is a pure PHP implementation, we are using manual SQL statements utilizing PHP Data Objects (`PDO`). Sessions are handled via standard `$_SESSION`.
