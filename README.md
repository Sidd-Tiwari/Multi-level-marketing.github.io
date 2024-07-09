# Multi-level-marketing.github.io
### MLM Project
Welcome to the MLM (Multi-Level Marketing) Project! This project is a comprehensive solution for managing MLM networks using PHP and MySQL. It provides a web application that allows users to view their network, track earnings, and manage their accounts.

# 🚀 Features<br>
### User Dashboard:
<br> View your income, available pins, and network tree.<br>
MLM Tree Structure:
<br> Visual representation of your network with clickable nodes.<br>
Income Tracking:<br> Track daily, current, and total income.<br>
Pin Management: <br>View and manage available pins for distribution.<br>
Search Functionality:<br> Search for users by email to view their network tree.<br><br>
# 📜 Table of Contents
Introduction<br>
Features<br>
Technologies Used<br>
Installation<br>
Usage<br>
Configuration<br>
File Structure<br>
Screenshots<br>
Contributing<br>
License<br>
Acknowledgements<br>
#  🛠 Technologies Used
PHP: Server-side scripting language.<br>
MySQL: Relational database management system.<br>
Bootstrap: Front-end framework for responsive design.<br>
Font Awesome: Icon library for scalable vector icons.<br>
# 💻 Installation
To get a copy of this project up and running on your local machine, follow these steps:<br>

Clone the Repository:
<br>
git clone https://github.com/sidd-tiwari/multi-level-marketing.github.io.git<br>
Navigate to the Project Directory:
<br>
cd mlm-project<br>
Set Up the Database:<br>
Create a MySQL database and import the mlm.sql file found in the database directory.<br><br>
CREATE DATABASE mlm;<br>
USE mlm;<br>
SOURCE path/to/mlm.sql;<br>
Configure Database Connection:<br>
Open the php-includes/connect.php file and update the database connection details.<br>
<?php
$host = 'localhost'; <br>
$user = 'root';<br>
$password = 'your_db_password';<br>
$database = 'mlm';<br>

$con = mysqli_connect($host, $user, $password, $database);<br>
if (!$con) {<br>
    die("Connection failed: " . <br>mysqli_connect_error());<br>
}<br>
?><br>
Install Dependencies:<br>
If there are any dependencies, you can install them using Composer or other package managers.<br><br>
 composer install
# 📖 Usage<br>
To start using the MLM project:
<br>
Start the Local Development Server:<br>
<br>
php -S localhost:8000<br>
Access the Application:<br>
Open your browser and go to http://localhost:8000.<br><br>
Login:<br>
Use the credentials for the admin user.<br>
<br>
The default admin credentials are:<br>
<br>
Username: Admin<br>
Password: 1234 <br>
# 🛠 Configuration
Configuration Files:<br>
php-includes/connect.php: Database connection settings.<br>
php-includes/check-login.php: Login check and user session management.<br>
Environment Variables:<br>
If you prefer using environment variables for configuration, you can create a .env file and add the following content:<br>

env
<br>
DB_HOST=localhost <br>
DB_USER=root <br>
DB_PASS=your_db_password <br>
DB_NAME=mlm_project <br>
Update the php-includes/connect.php file to read from the .env file if necessary. <br>

# 🗂 File Structure
Here’s an overview of the project’s file structure:<br><br>
mlm-project/<br>
│<br>
├── php-includes/<br>
│   ├── check-login.php<br>
│   ├── connect.php <br>
│   └── menu.php <br>
│<br>
├── vendor/<br>
│   ├── bootstrap/<br>
│   ├── font-awesome/<br>
│   ├── jquery/<br>
│   ├── metisMenu/<br>
│   └── composer packages<br>
│<br>
├── dist/<br>
│   ├── css/ <br>
│   ├── js/ <br>
│   └── sb-admin-2.css <br>
│ <br>
├── database/ <br>
│   └── mlm.sql <br>
│ <br>
├── index.php <br>
├── home.php <br>
├── income.php <br>
├── tree.php <br>
├── pin-request.php <br>
├── pin.php <br>
├── join.php <br>
├── tree_temp.php <br>
├── payment-received.php <br>
├── login.php <br>
├── logout.php <br>
└── README.md <br><br>

# 📸 Screenshots
Here are some screenshots of the application:

### Home Page
![Home Page](/images/home.png)

### MLM Tree View
![MLM Tree View](/images/tree.png)

### User Dashboard
![User Dashboard](/images/dashboard.png)

### User Payment
![User Dashboard](/images/payment_notification.png)

### User Joining
![User Dashboard](/images/join_notification.png)



# 🤝 Contributing <br>
We welcome contributions to this project! If you’d like to contribute, please follow these steps:
 <br>
Fork the Repository <br>
Create a New Branch: <br>
git checkout -b feature/your-feature-name <br>
Make Your Changes <br>
Commit Your Changes: <br>
git add . <br>
git commit -m "Add a descriptive commit message" <br>
Push Your Changes: <br>
git push origin feature/your-feature-name<br>
Create a Pull Request <br>
Go to the Pull Requests section of the repository and create a new pull request from your branch. <br>

# 📜 License
This project is licensed under the MIT License - see the LICENSE file for details. <br>

# 🙏 Acknowledgements <br>
Bootstrap - For the responsive design framework. <br>
Font Awesome - For the icons. <br>
PHP - For the server-side scripting <br> language. <br>
MySQL - For the database management system. <br>
# 📧 Contact <br>
For any questions or feedback, please contact: <br>

### Email: tiwarisid022018@gmail.com