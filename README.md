# Love & Styles — Rental Management System (RTW Attires).

A comprehensive **Rental Management System (RMS)** for **Love & Styles**, a company specializing in rental-ready fashion attires. This system automates the entire rental lifecycle from reservation to return, including inventory management, customer tracking, payment processing, and business rule enforcement.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

## 📋 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [System Architecture](#-system-architecture)
- [Database Schema](#-database-schema)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [API Documentation](#-api-documentation)
- [Business Rules](#-business-rules)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🎯 Core Functionality
- **Customer Management**: Complete customer profiles with measurements and contact information
- **Inventory Management**: Track rental items with status, availability, and condition
- **Reservation System**: Book items in advance with flexible date ranges
- **Rental Processing**: Handle item release, tracking, and return management
- **Payment Processing**: Multiple payment methods with invoice generation
- **Reporting & Analytics**: Comprehensive reports on rentals, payments, and customer behavior

### 🔧 Advanced Features
- **Business Rule Enforcement**: Automated enforcement of rental policies
- **Overdue Management**: Automatic penalty calculation and customer restrictions
- **Status Tracking**: Real-time status updates for all entities
- **Responsive Design**: Mobile-friendly interface with modern UI/UX
- **Data Validation**: Comprehensive input validation and error handling
- **Audit Trail**: Complete history tracking for all transactions

### 📊 Dashboard & Reports
- **Customer Reports**: Detailed customer rental history and statistics
- **Inventory Reports**: Item utilization and availability tracking
- **Financial Reports**: Payment summaries and revenue analytics
- **Operational Reports**: Rental performance and efficiency metrics

## 🛠 Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+ / MariaDB 10.6+
- **Authentication**: Laravel Sanctum
- **Validation**: Laravel Form Requests
- **Testing**: PHPUnit

### Frontend
- **Templates**: Blade templating engine
- **Styling**: TailwindCSS 3.x
- **JavaScript**: Alpine.js for dynamic interactions
- **Icons**: Heroicons
- **Build Tool**: Vite

### Development Tools
- **Package Manager**: Composer (PHP), NPM (Node.js)
- **Version Control**: Git
- **Code Quality**: PHP CS Fixer, ESLint
- **Documentation**: Markdown

## 🏗 System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Blade +      │◄──►│   (Laravel)     │◄──►│   (MySQL)       │
│   TailwindCSS)  │    │                 │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │                       │                       │
    ┌────▼────┐              ┌────▼────┐              ┌────▼────┐
    │ Alpine.js│              │ Controllers│              │ Models │
    │         │              │           │              │        │
    └─────────┘              └───────────┘              └─────────┘
```

## 🗄 Database Schema

### Core Entities

#### Customers
- **Primary Key**: `customer_id`
- **Fields**: `full_name`, `email`, `contact_number`, `address`, `measurements`
- **Relationships**: One-to-many with Reservations, Rentals, Payments

#### Inventory
- **Primary Key**: `item_id`
- **Fields**: `item_name`, `description`, `size`, `color`, `rental_price`, `status`
- **Relationships**: One-to-many with Reservations, Rentals

#### Reservations
- **Primary Key**: `reservation_id`
- **Fields**: `customer_id`, `item_id`, `reservation_date`, `start_date`, `end_date`
- **Relationships**: Many-to-one with Customer, Inventory; One-to-one with Rental

#### Rentals
- **Primary Key**: `rental_id`
- **Fields**: `reservation_id`, `released_date`, `due_date`, `return_date`, `penalty_fee`
- **Relationships**: One-to-one with Reservation; One-to-many with Payments

#### Payments
- **Primary Key**: `payment_id`
- **Fields**: `rental_id`, `amount`, `payment_type`, `payment_method`, `payment_date`
- **Relationships**: Many-to-one with Rental; One-to-one with Invoice

### Status Tables
- **Rental Status**: Active, Returned, Overdue, Cancelled
- **Reservation Status**: Pending, Confirmed, Cancelled
- **Payment Status**: Pending, Completed, Failed
- **Inventory Status**: Available, Rented, Maintenance, Retired

## 🚀 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- MySQL 8.0+ or MariaDB 10.6+
- Git

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/love-styles-rental-system.git
   cd love-styles-rental-system
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE love_styles_rental;
   
   # Run migrations
   php artisan migrate
   ```

6. **Seed Database**
   ```bash
   php artisan db:seed
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Start Development Servers**
   ```bash
   # Terminal 1 - Backend
   php artisan serve
   
   # Terminal 2 - Frontend (for development)
   npm run dev
   ```

## ⚙️ Configuration

### Environment Variables

Create a `.env` file with the following configuration:

```env
APP_NAME="Love & Styles Rental System"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=love_styles_rental
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Database Configuration

The system uses the following database structure:
- **Customers**: 20 sample customers with realistic data
- **Inventory**: 50+ rental items across various categories
- **Rental History**: Comprehensive rental data with business rule enforcement
- **Users**: Admin users for system management

## 📖 Usage

### Creating a User Account

#### Via Command Line (Recommended)
```bash
php artisan tinker
```

```php
use App\Models\User;
User::create([
'name' => 'John Doe',
'email' => 'john@example.com',
    'password' => Hash::make('secret123')
]);
```

#### Via Web Interface
1. Navigate to `/register`
2. Fill in the registration form
3. Verify email (if configured)

### Managing Customers

#### Adding a New Customer
1. Navigate to **Customers** → **Add New Customer**
2. Fill in customer details:
   - Personal information (name, email, contact)
   - Address information
   - Body measurements (bust, waist, hips, height)
3. Save customer record

#### Customer Management Features
- **View Customer Details**: Complete profile with rental history
- **Edit Customer Information**: Update contact details and measurements
- **Customer Reports**: Rental statistics and payment history
- **Search & Filter**: Find customers by name, email, or contact number

### Managing Inventory

#### Adding New Items
1. Navigate to **Inventory** → **Add New Item**
2. Fill in item details:
   - Item name and description
   - Size and color information
   - Rental price
   - Initial status
3. Save inventory record

#### Inventory Management Features
- **Availability Tracking**: Real-time status updates
- **Item Categories**: Organize by type, size, or color
- **Condition Tracking**: Monitor item wear and maintenance needs
- **Pricing Management**: Update rental rates

### Processing Rentals

#### Reservation Workflow
1. **Create Reservation**: Customer selects item and dates
2. **Confirm Reservation**: Staff confirms availability
3. **Process Payment**: Handle deposit and rental fees
4. **Release Item**: Mark item as rented
5. **Track Return**: Monitor due dates and returns
6. **Handle Returns**: Process item return and final payments

#### Business Rule Enforcement
- **Overdue Prevention**: Customers with overdue rentals cannot rent additional items
- **Automatic Penalties**: Overdue rentals incur daily penalty fees
- **Status Management**: Real-time status updates across all entities

## 📚 API Documentation

### Authentication Endpoints
```http
POST /api/login
POST /api/logout
POST /api/register
```

### Customer Endpoints
```http
GET    /api/customers              # List all customers
POST   /api/customers              # Create new customer
GET    /api/customers/{id}         # Get customer details
PUT    /api/customers/{id}         # Update customer
DELETE /api/customers/{id}         # Delete customer
GET    /api/customers/{id}/rentals # Get customer rental history
```

### Inventory Endpoints
```http
GET    /api/inventory              # List all items
POST   /api/inventory              # Create new item
GET    /api/inventory/{id}         # Get item details
PUT    /api/inventory/{id}         # Update item
DELETE /api/inventory/{id}         # Delete item
```

### Rental Endpoints
```http
GET    /api/rentals                # List all rentals
POST   /api/rentals                # Create new rental
GET    /api/rentals/{id}           # Get rental details
PUT    /api/rentals/{id}           # Update rental
POST   /api/rentals/{id}/return    # Process return
```

## 📋 Business Rules

### Core Business Rules

1. **Overdue Rental Restriction**
   - Customers with overdue rentals cannot rent additional items
   - Overdue status is automatically calculated based on due date vs. current date
   - Penalty fees are calculated as: `(Days Overdue) × 50`

2. **Rental Period**
   - Standard rental period: 7 days
   - Extensions may be granted with additional fees
   - Early returns are accepted without penalty

3. **Payment Requirements**
   - Deposit required before item release
   - Full payment due upon return
   - Multiple payment methods accepted (cash, card, bank transfer)

4. **Inventory Management**
   - Items must be available before reservation
   - Maintenance status prevents rental
   - Retired items are removed from active inventory

### Status Workflows

#### Reservation Status Flow
```
Pending → Confirmed → Cancelled
```

#### Rental Status Flow
```
Active → Returned
Active → Overdue
Cancelled (at any point)
```

#### Payment Status Flow
```
Pending → Completed
Pending → Failed
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

### Test Structure
```
tests/
├── Feature/           # Integration tests
│   ├── CustomerTest.php
│   ├── RentalTest.php
│   └── PaymentTest.php
├── Unit/             # Unit tests
│   ├── CustomerTest.php
│   ├── RentalTest.php
│   └── PaymentTest.php
└── TestCase.php      # Base test class
```

### Database Testing
```bash
# Use in-memory database for tests
php artisan test --env=testing
```

## 🚀 Deployment

### Production Deployment

1. **Server Requirements**
   - PHP 8.2+
   - MySQL 8.0+
   - Nginx/Apache
   - SSL Certificate
   - Composer
   - Node.js (for asset building)

2. **Deployment Steps**
   ```bash
   # Clone repository
   git clone https://github.com/your-username/love-styles-rental-system.git
   cd love-styles-rental-system
   
   # Install dependencies
   composer install --optimize-autoloader --no-dev
   npm install
   
   # Build assets
   npm run build
   
   # Configure environment
   cp .env.example .env
   # Edit .env with production values
   
   # Generate key
   php artisan key:generate
   
   # Run migrations
   php artisan migrate --force
   
   # Seed database (optional)
   php artisan db:seed --force
   
   # Optimize for production
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Web Server Configuration**
   ```nginx
   server {
       listen 80;
       server_name your-domain.com;
       root /path/to/love-styles-rental-system/public;
       
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
   }
   ```

### Docker Deployment

```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

## 🤝 Contributing

### Development Workflow

1. **Fork the Repository**
   ```bash
   git fork https://github.com/your-username/love-styles-rental-system.git
   ```

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make Changes**
   - Follow PSR-12 coding standards
   - Write tests for new functionality
   - Update documentation as needed

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "Add: Brief description of changes"
   ```

5. **Push and Create Pull Request**
   ```bash
   git push origin feature/your-feature-name
   ```

### Code Standards

- **PHP**: PSR-12 coding standard
- **JavaScript**: ESLint configuration
- **CSS**: TailwindCSS utility classes
- **Database**: Laravel migration conventions
- **Testing**: PHPUnit with 80%+ coverage

### Pull Request Guidelines

1. **Clear Description**: Explain what the PR does and why
2. **Tests**: Include tests for new functionality
3. **Documentation**: Update README or code comments
4. **Screenshots**: Include UI changes if applicable
5. **Breaking Changes**: Clearly mark any breaking changes

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

### Getting Help

- **Documentation**: Check this README and inline code comments
- **Issues**: Create a GitHub issue for bugs or feature requests
- **Discussions**: Use GitHub Discussions for questions
- **Email**: Contact the development team

### Common Issues

#### Database Connection Issues
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

#### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

#### Asset Building Issues
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Rebuild assets
npm run build
```

---

**Love & Styles Rental Management System** - Streamlining fashion rental operations with modern technology.

*Built with ❤️ using Laravel, TailwindCSS, and modern web technologies.*
