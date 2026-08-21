# Short Links Yii2 Application

Yii2 version of the Short Links application, originally built with Laravel 12.

## Requirements

- PHP >= 8.1
- Composer
- SQLite, MySQL, or PostgreSQL database

## Installation

### 1. Clone and Install Dependencies

```bash
cd short-yii2
composer install
```

### 2. Configure Environment

Copy `.env` file and adjust settings if needed:

```bash
cp .env.example .env  # Or use the provided .env file
```

Edit `.env` to configure:
- Database connection (SQLite by default)
- Short link code length
- Admin email

### 3. Run Migrations

Create the database tables:

```bash
# For web server setup, point document root to /web directory
# Then run migrations via console:
php yii migrate --interactive=0
```

Or manually:
```bash
./yii migrate --interactive=0
```

### 4. Set Permissions

Ensure the following directories are writable by the web server:

```bash
chmod -R 777 runtime/
chmod -R 777 web/assets/
```

### 5. Web Server Configuration

#### Apache

Enable mod_rewrite and use the provided `.htaccess` in the `web/` directory.

Document root should point to: `/path/to/short-yii2/web`

#### Nginx

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/short-yii2/web;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Usage

### Creating Short Links

1. Register an account at `/register`
2. Login at `/login`
3. Create a short link via the form

### Accessing Short Links

Short links are accessible at: `/{code}` (e.g., `http://localhost/abc123`)

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Home page |
| GET | `/login` | Login form |
| POST | `/login` | Process login |
| GET | `/register` | Registration form |
| POST | `/register` | Process registration |
| POST | `/logout` | Logout |
| GET | `/{code}` | Redirect to original URL |
| POST | `/short-link/create` | Create new short link |

## Testing

Run PHPUnit tests:

```bash
vendor/bin/phpunit
```

Or Codeception tests:

```bash
vendor/bin/codecept run
```

## Fixtures

Load test fixtures:

```bash
./yii fixture/load User
./yii fixture/load Link
```

## Directory Structure

```
short-yii2/
├── assets/          # Asset bundles
├── commands/        # Console commands
├── components/      # Custom components
├── config/          # Configuration files
│   ├── db.php       # Database config
│   ├── web.php      # Web app config
│   ├── console.php  # Console app config
│   ├── params.php   # Parameters
│   └── routes.php   # URL rules
├── contracts/       # Interfaces
├── controllers/     # Controllers
├── fixtures/        # Test fixtures
├── migrations/      # Database migrations
├── models/          # ActiveRecord models
├── repositories/    # Repository classes
├── runtime/         # Runtime files (logs, cache)
├── services/        # Service classes
├── tests/           # Tests
├── views/           # View templates
├── web/             # Web root (public files)
├── .env             # Environment variables
├── .gitignore       # Git ignore rules
├── composer.json    # Composer dependencies
├── yii              # Console entry script
└── README.md        # This file
```

## Configuration Options

### Database

Default is SQLite. To use MySQL or PostgreSQL, edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=short_links
DB_USERNAME=root
DB_PASSWORD=secret
```

### Short Link Settings

```env
SHORT_LINK_CODE_LENGTH=6
SHORT_LINKS_CHECKER=app\\services\\CodeCheckers\\EloquentCodeUniquenessChecker
```

Available checkers:
- `app\services\CodeCheckers\EloquentCodeUniquenessChecker` - Database check
- `app\services\CodeCheckers\CacheCodeUniquenessChecker` - Cache check

## CI/CD Deployment

For production deployment:

1. Set `APP_ENV=prod` and `APP_DEBUG=false` in `.env`
2. Run `composer install --no-dev --optimize-autoloader`
3. Run migrations: `./yii migrate --interactive=0`
4. Set proper permissions on `runtime/` and `web/assets/`
5. Configure web server (see above)

## License

MIT
