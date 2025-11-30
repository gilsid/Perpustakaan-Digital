# Online Library (PHP + Bootstrap + JSON Storage)

A simple online library web application built with **plain PHP**, **Bootstrap 5 (CDN)**, and **JSON files for storage**. No database is used.

## Features

### Public / User
- Register, login, logout (PHP sessions)
- Edit profile (name, email, password)
- Browse and search books (title, author, category, year)
- Filter by category and year
- Book detail page with metadata and description
- Read book via embedded PDF viewer
- Add/remove favorites (wishlist)
- View borrowed books and return them
- Rate and review books

### Admin
- Admin-only login
- Dashboard with stats:
  - Total books
  - Total users
  - Total reviews
  - Total active borrowed books
- View borrow list
- Manage books (add, edit, delete)
  - Upload cover image and PDF
- Manage categories (add, inline edit, delete)
- View all users
- View admin activity logs with pagination

### Storage
All data is stored as JSON in the `data/` directory:

- `data/books.json`
- `data/users.json`
- `data/categories.json`
- `data/tags.json`
- `data/reviews.json`
- `data/favorites.json`
- `data/borrow.json`
- `data/logs.json`

Uploads:
- `uploads/images/` – book cover images
- `uploads/pdf/` – book PDF files

## Running the Project

### Option 1: XAMPP (Apache + PHP)

1. Copy the `online-library` folder into your `htdocs` directory, e.g.
   - Windows: `C:\xampp\htdocs\online-library`
   - Linux: `/opt/lampp/htdocs/online-library`
2. Start Apache from the XAMPP control panel.
3. Open in your browser:
   - `http://localhost/online-library/`

### Option 2: PHP Built-in Server

From inside the `online-library` folder, run:

```bash
php -S localhost:8000
```

Then open:

- `http://localhost:8000/`

## Upload Folder Permissions

The web server must be able to write into `uploads/` and `data/`.

### Linux

```bash
chmod -R 777 uploads/
chmod -R 777 data/
```

> Note: `777` is convenient for local development but not recommended for production environments.

### Windows (XAMPP)

- Ensure the `uploads` and `data` folders are not read-only.
- If necessary, adjust folder security settings to allow the web server user (e.g. `IIS_IUSRS` or Apache) to write.

## Default Admin Account

After seeding, the following admin user exists:

- Email: `admin@example.com`
- Password: `admin123`

A regular user account is also available:

- Email: `user@example.com`
- Password: `password123`

## JSON File Format (Overview)

### books.json

Array of book objects:

```json
[
  {
    "id": 1,
    "title": "The Pragmatic Programmer",
    "author": "Andrew Hunt, David Thomas",
    "year": 1999,
    "category": "programming",
    "description": "...",
    "cover": "cover_*.jpg or null",
    "pdf": "book_*.pdf or null",
    "rating": 4.7,
    "created_at": "ISO8601",
    "updated_at": "ISO8601"
  }
]
```

### users.json

```json
[
  {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "<password_hash>",
    "is_admin": true,
    "created_at": "ISO8601",
    "updated_at": "ISO8601"
  }
]
```

Other files (`categories.json`, `favorites.json`, `borrow.json`, `reviews.json`, `logs.json`) follow the structures described in the code and comments.

## Security Notes

- Passwords are hashed with `password_hash()` and verified with `password_verify()`.
- Sessions are used for user authentication and admin access checks.
- This project is intended as a learning/demo project and does not include advanced security hardening for production use.