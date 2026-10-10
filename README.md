
## Installation Guide

### 1. Install Project Dependencies
Run the following command to install the required dependencies:
```bash
composer install
```

### 2. Environment Configuration
Create a copy of the `.env` file and configure the environment variables:
```bash
cp .env.example .env
```

### 3. Generate Application Key
Generate the application key using the command:
```bash
php artisan key:generate
```

### 4. Set Up the Database
Update the `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

### 5. Run Database Migrations
Run the following command to set up the database structure:
```bash
php artisan migrate
```