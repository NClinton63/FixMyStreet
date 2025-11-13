# 🏘️ FixMyStreet.cm

A modern civic engagement platform that empowers citizens to report and track neighborhood issues like potholes, broken lights, waste management problems, and more. Built with Laravel, Vue 3, Inertia.js, and Filament.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-green)
![Filament](https://img.shields.io/badge/Filament-3.x-orange)
![License](https://img.shields.io/badge/license-MIT-blue)

## ✨ Features

### Public Features
- **Interactive Map**: View all reported issues on an interactive Leaflet map
- **Issue Reporting**: Submit issues with photos, descriptions, and automatic geolocation
- **Category Filtering**: Filter issues by type (roads, waste, lighting, water, electricity, etc.)
- **Status Tracking**: Track issue status (Pending, In Progress, Resolved)
- **Mobile Responsive**: Fully responsive design for mobile and desktop

### Admin Features (Filament Panel)
- **Secure Admin Dashboard**: Powered by Filament v3
- **Issue Verification**: Review and verify submitted issues
- **Status Management**: Update issue status and add admin notes
- **Category Management**: Create and manage issue categories with colors and icons
- **Photo Management**: View and manage uploaded photos
- **Statistics**: Track issue counts by status and category
- **Advanced Filtering**: Filter and search issues efficiently

### API Features
- **RESTful API**: Public API endpoints for external integrations
- **JSON Responses**: Well-structured JSON responses
- **Category Listing**: Get all active categories
- **Issue Retrieval**: Fetch verified issues

## Tech Stack

- **Backend**: Laravel 12.x
- **Frontend**: Vue 3 + Inertia.js
- **Admin Panel**: Filament 3.x
- **Database**: MySQL 8.0+
- **Maps**: Leaflet.js
- **Styling**: Tailwind CSS
- **Build Tool**: Vite

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ & NPM
- MySQL 8.0 or higher
- Git

## Quick Start

### 1. Clone the Repository

```bash
git clone <repository-url>
cd FixMyStreet
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fixmystreet
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database:

```bash
mysql -u root -p
CREATE DATABASE fixmystreet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 5. Run Migrations & Seeders

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 6. Create Admin User

```bash
php artisan make:filament-user
```

### 7. Build & Run

```bash
# Terminal 1: Start Laravel
php artisan serve

# Terminal 2: Build frontend assets
npm run dev
```

Visit:
- **Public Site**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

## Documentation

For detailed setup instructions, see [SETUP.md](SETUP.md)

## Project Structure

```
FixMyStreet/
├── app/
│   ├── Filament/              # Filament admin resources
│   │   └── Resources/
│   ├── Http/
│   │   └── Controllers/       # Application controllers
│   └── Models/                # Eloquent models
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/        # Vue components
│   │   ├── Layouts/           # Vue layouts
│   │   └── Pages/             # Inertia pages
│   ├── views/                 # Blade templates
│   └── css/                   # Stylesheets
├── routes/
│   └── web.php                # Web routes
└── public/                    # Public assets
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/issues` | Get all verified issues |
| GET | `/api/issues/{id}` | Get single issue details |
| GET | `/api/categories` | Get all active categories |

## Default Categories

The seeder creates 8 default categories:
- Road Issues
- Waste Management
- Street Lighting
- Water & Drainage
- Electricity
- Public Safety
- Parks & Recreation
- Other

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# FixMyStreet
