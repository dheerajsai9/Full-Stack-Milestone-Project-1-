# Automated Quiz Engine with PDF Certification Generation

A web application for taking general knowledge quizzes with certificate generation. Built with **HTML5, CSS3, ES6, PHP, and MySQL** (XAMPP).

## Features

- **Register & Login** - User authentication
- **Dashboard** - Overview and quiz rules
- **Quiz** - 10 multiple choice questions, 30 seconds per question
- **Submit** - Confirmation page after quiz completion
- **Result** - Score display with Gold/Silver/Bronze badges
- **Download** - Print/Save certificate as PDF

## Setup (XAMPP)

1. **Start XAMPP** - Start Apache and MySQL

2. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Import or run the SQL file: `database/quiz_db.sql`
   - This creates the `quiz_engine` database with tables and sample questions

3. **Configure** (if needed)
   - Edit `config/database.php` if your MySQL credentials differ
   - Default: host=localhost, user=root, password=empty

4. **Run the Application**
   - Navigate to: http://localhost/Automated%20Quiz%20Engine%20with%20PDF%20Certification%20Generation/

## Badge System

- **Gold** - 9-10 correct answers
- **Silver** - 7-8 correct answers  
- **Bronze** - 0-6 correct answers

## Certificate

The certificate can be printed or saved as PDF using the browser's Print dialog (Ctrl+P → Save as PDF).

## Tech Stack

- HTML5, CSS3, ES6 (JavaScript)
- PHP 7+ with PDO
- MySQL
- XAMPP (Apache + PHP + MySQL)
