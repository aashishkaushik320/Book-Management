# Book Management API (Laravel 12 + JWT)

A clean, production-ready, and fully verified RESTful API backend built with Laravel 12. This project implements stateless JWT-based authentication along with robust CRUD operations for a Book Catalog, incorporating database transactions, image uploads with automated disk cleanup, advanced filtering/searching, pagination, and soft deletion.

The application adheres strictly to standard Laravel design patterns, including custom Form Requests, Eloquent API Resources, and database transactions to ensure data consistency.

---

## 🌟 Key Features

*   **Stateless JWT Authentication**: Implemented via `php-open-source-saver/jwt-auth` for secure, token-based API authentication.
*   **Database Transaction Protection**: CRUD operations are wrapped in DB transactions to ensure atomic operations (especially during file handling).
*   **Cover Image Uploads**: Handles book cover image uploads safely (stored under `/storage/app/public/books`).
*   **Disk Cleanup**: Automatically cleans up and deletes previous cover image files from disk when a book is updated or deleted.
*   **Advanced Search & Filtering**: 
    *   Global search on `title` and `author`.
    *   Specific column filtering (e.g., `min_price`, `max_price`).
    *   Pagination with customizable page sizes.
*   **Soft Deletes**: Utilizes Laravel's native `SoftDeletes` trait to prevent accidental records loss.
*   **Postman Collection & Online Docs**: Pre-configured environment variables and complete requests.

---

## 🛠️ Requirements & Environment

*   **PHP**: `^8.3` (Optimized for PHP `8.3.6`)
*   **Laravel Framework**: `^12.0`
*   **Database**: MySQL / MariaDB

---

## 🚀 Installation & Local Setup

Follow these steps to set up the repository on your local computer:

### 1. Clone & Extract
Clone the repository or extract the project ZIP archive:
```bash
git clone https://github.com/aashishkaushik320/Book-Management.git
cd Book-Management
```

### 2. Install PHP Dependencies
Install the required Composer packages:
```bash
composer install
```

### 3. Configure the Environment
Copy the example environment template file:
```bash
cp .env.example .env
```
Open the `.env` file in your editor and configure your local MySQL database connection details:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_management
DB_USERNAME=root
DB_PASSWORD=Admin@@1234
```

### 4. Import the SQL Schema / Run Migrations
You can set up the database in either of the following two ways:

#### Option A: Import the SQL Dump (Recommended)
An SQL dump is included at the root of this project (`book_management.sql`). Create the database in your MySQL console and import the dump:
```sql
CREATE DATABASE book_management;
```
Then import the file using your terminal:
```bash
mysql -u root -p book_management < book_management.sql
```

#### Option B: Run Laravel Migrations
Alternatively, you can boot the database fresh and run Laravel's migrations:
```bash
# Generate the application encryption key
php artisan key:generate

# Scaffold the database tables
php artisan migrate
```

### 5. Configure JWT Secret Key
Generate the cryptographically secure JWT secret key required for signing auth tokens:
```bash
php artisan jwt:secret
```
This appends the generated `JWT_SECRET` key to your `.env` file automatically.

### 6. Symlink Public Storage
Since cover images are uploaded to the protected storage path, create a symlink to make them accessible publicly:
```bash
php artisan storage:link
```

### 7. Run the Development Server
Start the local PHP server on port `3003` (or your preferred port):
```bash
php artisan serve --port=3003
```

---

## 📡 API Documentation & Endpoints

All API endpoints expect headers:
*   `Accept: application/json`
*   `Content-Type: application/json`

### 🔑 Authentication Endpoints

| Method | Endpoint | Auth Required | Description | Request Body Payload |
| :--- | :--- | :--- | :--- | :--- |
| **POST** | `/api/register` | No | Register a new user | `{"name", "email", "password", "password_confirmation"}` |
| **POST** | `/api/login` | No | Login and receive JWT | `{"email", "password"}` |
| **GET** | `/api/profile` | **Yes (Bearer)** | Get authenticated user info | None |

### 📚 Book Catalog Endpoints

| Method | Endpoint | Auth Required | Description | Query Parameters / Request Body |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/api/books` | No | List books (searched, filtered, paginated) | Query: `search`, `min_price`, `max_price`, `per_page`, `page` |
| **GET** | `/api/books/{id}` | No | View a single book record | Path param: `{id}` |
| **POST** | `/api/books` | **Yes (Bearer)** | Create a new book record | Form-Data: `title`, `author`, `price`, `published_date`, `cover_image` (file) |
| **POST** | `/api/books/{id}` | **Yes (Bearer)** | Update a book (with `_method=PUT`) | Form-Data: `_method=PUT`, `title`, `author`, `price`, `published_date`, `cover_image` (file) |
| **DELETE** | `/api/books/{id}` | **Yes (Bearer)** | Soft-delete a book record | Path param: `{id}` |

---

## 📬 Postman Collection

The project includes a pre-configured Postman Collection file at the root:
*   **Filename**: `book_management_postman_collection.json`

### Importing the Collection:
1. Open Postman.
2. Click the **Import** button in the top left.
3. Drag and drop the `book_management_postman_collection.json` file.
4. Select the environment variables (`base_url`) or configure your active environment to use `http://127.0.0.1:3003`.
5. Authenticate via `/api/login` to obtain the token, copy the `token` value, and assign it to the `token` collection variable to authorize protected requests automatically.

### Live Online Documentation:
You can also view the complete API collection online with interactive examples and parameter descriptions:
👉 **[View Postman API Documentation Online](https://documenter.getpostman.com/view/41078554/2sBXwmSE7S)**


