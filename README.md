# Multi-level-marketing.github.io

MLM Project

📖 Introduction
Welcome to the MLM (Multi-Level Marketing) Project! This project is a comprehensive solution for managing MLM networks using PHP and MySQL. It provides a web application that allows users to view their network, track earnings, and manage their accounts.

🚀 Features
User Dashboard: View your income, available pins, and network tree.
MLM Tree Structure: Visual representation of your network with clickable nodes.
Income Tracking: Track daily, current, and total income.
Pin Management: View and manage available pins for distribution.
Search Functionality: Search for users by email to view their network tree.
📜 Table of Contents
Introduction
Features
Technologies Used
Installation
Usage
Configuration
File Structure
Screenshots
Contributing
License
Acknowledgements
🛠 Technologies Used
This project utilizes the following technologies:

PHP: Server-side scripting language.
MySQL: Relational database management system.
Bootstrap: Front-end framework for responsive design.
Font Awesome: Icon library for scalable vector icons.

💻 Installation
To get a copy of this project up and running on your local machine, follow these steps:

Clone the Repository:

git clone https://github.com/sidd-tiwari/mlm-project.git
Navigate to the Project Directory:

cd mlm-project
Set Up the Database:

Create a MySQL database and import the mlm.sql file found in the database directory.
sql code
CREATE DATABASE mlm;
USE mlm;
SOURCE path/to/mlm.sql;

Configure Database Connection:

Open the php-includes/connect.php file and update the database connection details.

<?php
$host = 'localhost';
$user = 'root';
$password = 'your_db_password';
$database = 'mlm';

$con = mysqli_connect($host, $user, $password, $database);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
Install Dependencies:

If there are any dependencies, you can install them using Composer or other package managers.

composer install
📖 Usage
To start using the MLM project:

Start the Local Development Server:

php -S localhost:8000
Access the Application:

Open your browser and go to http://localhost:8000.

Login:

Use the credentials for the admin user or create a new user via the registration page.
The default admin credentials are:
Username: golu
Password: 1234
🛠 Configuration
Configuration Files:

php-includes/connect.php - Database connection settings.
php-includes/check-login.php - Login check and user session management.
Environment Variables:

If you prefer using environment variables for configuration, you can create a .env file and add the following content:

DB_HOST=localhost
DB_USER=your_db_user
DB_PASS=your_db_password
DB_NAME=mlm_project
Update the php-includes/connect.php file to read from the .env file if necessary.

🗂 File Structure
Here’s an overview of the project’s file structure:

mlm-project/
│
├── php-includes/
│   ├── check-login.php
│   ├── connect.php
│   └── menu.php
│
├── vendor/
│   ├── bootstrap/
│   ├── font-awesome/
│   ├── jquery/
│   ├── metisMenu/
│   └── composer packages
│
├── dist/
│   ├── css/
│   ├── js/
│   └── sb-admin-2.css
│
├── database/
│   └── mlm.sql
│
├── index.php
├── home.php
├── income.php
├── tree.php
├── pin-request.php
├── pin.php
├── join.php
├── tree_temp.php
├── payment-received.php
├── login.php
├── logout.php
└── README.md

📸 Screenshots
Here are some screenshots of the application:

### Home Page
![Home Page](/images/home.png)

### MLM Tree View
![MLM Tree View](/images/tree.png)

### User Dashboard
![User Dashboard](/images/dashboard.png)


🤝 Contributing
We welcome contributions to this project! If you’d like to contribute, please follow these steps:

Fork the Repository

Create a New Branch:

git checkout -b feature/your-feature-name
Make Your Changes

Commit Your Changes:

git add .
git commit -m "Add a descriptive commit message"
Push Your Changes:

git push origin feature/your-feature-name
Create a Pull Request

Go to the Pull Requests section of the repository and create a new pull request from your branch.
📜 License
This project is licensed under the MIT License - see the LICENSE file for details.

🙏 Acknowledgements

Bootstrap - For the responsive design framework.
Font Awesome - For the icons.
PHP - For the server-side scripting language.
MySQL - For the database management system.

📧 Contact
For any questions or feedback, please contact:

Email: tiwarisid022018@gmail.com

