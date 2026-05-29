# Book Management API (Laravel 11 + JWT)

A clean, practical, and fully verified REST API backend built with Laravel 11 . The project implements JWT authentication alongside Books CRUD operations, utilizing standard Laravel conventions (Form Requests, Eloquent, Soft Deletes, Pagination, and DB Transactions) without over-engineering or unnecessary layers.

---

## Features
* **Stateless JWT Authentication** (via `php-open-source-saver/jwt-auth`)
* **Books CRUD API** with database transaction protection
* **Image Uploads** for book covers (stored locally under `/storage/app/public/books`)
* **Disk Cleanup**: Automatically deletes old cover images from storage when updated
* **Search & Filters**: Global keyword search (searches title/author) + specific filtering + pagination
* **Soft Deletes**: Uses Laravel's native SoftDeletes for books cataloguing


---

##  Installation

Follow these steps to spin up the application in your local environment:
clone or download the zip file.

### 1. Clone & Install Dependencies
First, install the composer packages:
```bash
composer install
```

### 2. Configure Environment Variables
Copy the example environment file:
```bash
cp .env.example .env
```
Open your newly created `.env` file and set up your local MySQL connection credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_management
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

### 3. Generate App Key & Database Setup
Initialize the application and run migrations:
```bash
# Generate Laravel application key
php artisan key:generate

# Run database migrations to scaffold users and books tables
php artisan migrate
```

### 4. JWT Authentication Setup
Generate the cryptographically secure JWT secret key:
```bash
php artisan jwt:secret
```
This command automatically appends the `JWT_SECRET` variable to your `.env` file.

### 5. Symlink Public Storage
Since the books API supports uploading cover images, symlink the storage directory to make them accessible via URL:
```bash
php artisan storage:link
```

---

## 🚀 Running the Application

Start the local Laravel development server on your preferred port (defaulting to 3003 here):
```bash
php artisan serve --port=3003
```


---

## 📬 Postman API Documentation

We have pre-configured a complete Postman collection containing all endpoints, bodies, and variables. 

url - https://documenter.getpostman.com/view/41078554/2sBXwmSE7S

