# File Management System 

This project is a simple file management system that allows users to upload, manage, and share files with others. Users can register, log in, and perform file operations like uploading, downloading, and sharing files.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Folder Structure](#folder-structure)
- [Usage](#usage)
- [Login Credentials](#login-credentials)
- [Security](#security)

## Features

- User Registration and Login
- Upload files (with file type and size validation)
- View, download, and delete files
- Share files with other registered users
- CSRF token protection for form submissions
- Directory creation for each user upon registration
- DataTables for displaying user file lists

## Installation

### Prerequisites

Before you can run this project on your local system, make sure you have the following installed:

- **XAMPP (or any local PHP server)**: [Download XAMPP](https://www.apachefriends.org/index.html)
- **Git** (optional): [Download Git](https://git-scm.com/)
- **Composer** (optional): [Download Composer](https://getcomposer.org/) (for dependency management)

### Steps to Install

1. **Clone or Download the Repository:**

   - If you have Git installed, clone the repository:
     ```bash
     git clone https://github.com/jitojosephthomasvaidyan/file-management-system.git
     ```
   - Alternatively, download the project as a ZIP file from GitHub and extract it.

2. **Move to XAMPP htdocs:**

   If you're using XAMPP, move the project folder (`fileManagementSystem`) to the `htdocs` folder in your XAMPP installation:

3. **Set Up Database:**

- Open **phpMyAdmin** (http://localhost/phpmyadmin).
- Create a new database called `file_management`.
- Import the provided SQL file (`file_management.sql`) in the `db` folder to create the necessary tables.

4. **Configure Database:**

- Open the `includes/db.php` file.
- Update the following with your local database credentials:
  ```php
  $servername = "localhost";
  $username = "root";      // Default XAMPP username
  $password = "";          // Default XAMPP password (usually empty)
  $dbname = "file_management";
  ```

5. **Start XAMPP Services:**

- Open **XAMPP Control Panel** and start **Apache** and **MySQL**.

6. **Access the Application:**

Open your browser and visit:

## Folder Structure

- `assets/`: Contains the front-end assets like CSS, JavaScript, and images.
    - `css/`: Contains stylesheets for the application.
    - `js/`: Contains JavaScript files.
    - `images/`: Stores images used in the UI.

- `uploads/`: This folder contains the uploaded files. Each user has their own subfolder created with their username upon registration.

- `includes/`: Contains database connection files and other backend utilities.
    - `db.php`: Manages the connection to the MySQL database.

- `layout/`: Contains reusable layout files such as the header, sidebar, and footer.
    - `header.php`: The header of the application.
    - `asidebar.php`: The sidebar that provides navigation links.
    - `top-bar.php`: The top navigation bar.

- `dashboard.php`: The user dashboard after login.

- `login.php`: The login page for the application.

- `register.php`: The registration page where users sign up for an account.

- `shared_file.php`: Displays files shared with the user by other users.

## Usage

### Registration

1. Go to the [registration page](http://localhost/[YourFolderName]/register.php).
2. Enter your name, email, and password, and click "Sign Up".
3. Upon successful registration, a folder with your email as the name will be created under the `uploads` folder.

### Login

1. Go to the [login page](http://localhost/[YourFolderName]/index.php).
2. Enter the login credentials (see below for default credentials).
3. After logging in, you can upload, delete, download, and share files with other users.

### Uploading Files

1. After logging in, navigate to the "File Upload" section.
2. Select the file you wish to upload. Only images and PDFs are allowed, with a maximum file size of 5MB.
3. Once uploaded, the file will be saved in your user-specific folder under `uploads/your-username`.

### Sharing Files

1. Go to the "Share Files" section.
2. Select a file from your uploads and choose a registered user to share it with.
3. The shared user can download the file from their dashboard.

## Login Credentials

### Default Credentials

For testing, you can use the following default credentials to log in:

- **Test User 1**:
- **Email/Username**: `john@gmail.com`
- **Password**: `1234`

- **Test User 2**:
- **Email/Username**: `ravi@gmail.com`
- **Password**: `1234`

You can also register new users using the registration page.

## Security

- **CSRF Protection**: Every form submission is protected by a CSRF token to prevent Cross-Site Request Forgery attacks.
- **SQL Injection Protection**: All SQL queries use prepared statements to prevent SQL injection.
- **XSS Protection**: Input is sanitized before output to prevent Cross-Site Scripting (XSS) attacks.
- **File Upload Validation**: The system ensures that only allowed file types and sizes are uploaded.

