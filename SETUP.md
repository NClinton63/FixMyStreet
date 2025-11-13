# FixMyStreet.cm - Setup Guide

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0 or higher
- Git

## Installation Steps

### 1. Clone the Repository (if not already done)

```bash
git clone <repository-url>
cd FixMyStreet
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Configure Environment

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure MySQL Database

Edit your `.env` file and update the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fixmystreet
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 5. Create MySQL Database

Login to MySQL and create the database:

```bash
mysql -u root -p
```

Then run:

```sql
CREATE DATABASE fixmystreet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Migrations and Seeders

```bash
# Run migrations to create tables
php artisan migrate

# Seed the database with default categories
php artisan db:seed
```

### 7. Create Storage Link

```bash
# Create symbolic link for public storage
php artisan storage:link
```

### 8. Create Filament Admin User

```bash
# Create an admin user for Filament panel
php artisan make:filament-user
```

Follow the prompts to create your admin account:
- Name: Admin User
- Email: admin@fixmystreet.cm
- Password: (choose a secure password)

### 9. Build Frontend Assets

```bash
# Development mode (with hot reload)
npm run dev

# OR Production build
npm run build
```

### 10. Start the Development Server

In a new terminal:

```bash
php artisan serve
```

The application will be available at: `http://localhost:8000`

The admin panel will be available at: `http://localhost:8000/admin`

## Default Credentials

After running the seeder, you can login to the admin panel with:
- **Email**: admin@fixmystreet.cm
- **Password**: (the one you set during `make:filament-user`)

## Project Structure

```
FixMyStreet/
├── app/
│   ├── Filament/         # Filament admin resources
│   ├── Http/
│   │   └── Controllers/  # Application controllers
│   └── Models/           # Eloquent models
├── database/
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/   # Vue components
│   │   ├── Layouts/      # Vue layouts
│   │   └── Pages/        # Inertia pages
│   └── views/            # Blade templates
├── routes/
│   └── web.php           # Web routes
└── public/               # Public assets
```

## Key Features

### Public Features
- **Interactive Map**: View all reported issues on a map
- **Report Issues**: Submit new issues with photos and location
- **Filter by Category**: Filter issues by type (roads, waste, lighting, etc.)
- **Filter by Status**: View pending, in-progress, or resolved issues

### Admin Features (Filament Panel)
- **Issue Management**: View, verify, and update issue status
- **Category Management**: Manage issue categories
- **Photo Preview**: View uploaded photos
- **Status Updates**: Mark issues as pending, in-progress, resolved, or rejected
- **Admin Notes**: Add internal notes to issues

## API Endpoints

The application provides public API endpoints:

- `GET /api/issues` - Get all verified issues
- `GET /api/issues/{id}` - Get single issue details
- `GET /api/categories` - Get all active categories

## Troubleshooting

### Database Connection Error

If you get a database connection error:
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Check your `.env` database credentials
3. Ensure the database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### Storage Link Error

If images don't display:
```bash
php artisan storage:link
```

### Permission Issues

If you encounter permission errors:
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache

If you make configuration changes:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Development Workflow

### Running in Development

Terminal 1 - Backend:
```bash
php artisan serve
```

Terminal 2 - Frontend (with hot reload):
```bash
npm run dev
```

### Building for Production

```bash
# Build optimized assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Next Steps

1. Customize categories in the admin panel
2. Configure email settings for notifications (optional)
3. Set up file storage (local or S3)
4. Deploy to production server

## Support

For issues or questions, please refer to the documentation or create an issue in the repository.
