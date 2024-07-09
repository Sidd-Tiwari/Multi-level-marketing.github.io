<?php
include('php-includes/connect.php');
include('php-includes/check-login.php');

require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Function to send income notification
function sendIncomeNotification($email, $amount, $total_income, $today_income) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gt579175@gmail.com';
        $mail->Password = 'wxrmvjyizornevaj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('gt579175@gmail.com', 'MLM Website');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Income Notification';
        $mail->Body = '
            Dear User,<br><br>
            You have received a new income of ₹' . number_format($amount, 2) . '.<br><br>
            Total Income: ₹' . number_format($total_income, 2) . '<br>
            Today\'s Income: ₹' . number_format($today_income, 2) . '<br><br>
            Thank you for being a part of our MLM system.<br><br>
            Best regards,<br>
            MLM Website
        ';
        $mail->AltBody = '
            Dear User,

            You have received a new income of ₹' . number_format($amount, 2) . '.

            Total Income: ₹' . number_format($total_income, 2) . '
            Today\'s Income: ₹' . number_format($today_income, 2) . '

            Thank you for being a part of our MLM system.

            Best regards,
            MLM Website
        ';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Function to update income
function addMoneyToAccount($userid, $amount) {
    global $con;
    $capping = 1000; // Example capping amount

    // Update the income
    $income_query = mysqli_prepare($con, "SELECT * FROM income WHERE userid = ?");
    mysqli_stmt_bind_param($income_query, "s", $userid);
    mysqli_stmt_execute($income_query);
    $result = mysqli_stmt_get_result($income_query);
    $income_data = mysqli_fetch_array($result);
    $day_bal = $income_data['day_bal'] ?? 0;
    $current_bal = $income_data['current_bal'] ?? 0;
    $total_bal = $income_data['total_bal'] ?? 0;

    $new_day_bal = $day_bal + $amount;
    $new_current_bal = $current_bal + $amount;
    $new_total_bal = $total_bal + $amount;

    $update_income_query = mysqli_prepare($con, "UPDATE income SET day_bal = ?, current_bal = ?, total_bal = ? WHERE userid = ?");
    mysqli_stmt_bind_param($update_income_query, "iiis", $new_day_bal, $new_current_bal, $new_total_bal, $userid);
    mysqli_stmt_execute($update_income_query);
    mysqli_stmt_close($update_income_query);

    // Fetch user's email for notification
    $user_query = mysqli_prepare($con, "SELECT email FROM users WHERE userid = ?");
    mysqli_stmt_bind_param($user_query, "s", $userid);
    mysqli_stmt_execute($user_query);
    $user_result = mysqli_stmt_get_result($user_query);
    $user_data = mysqli_fetch_array($user_result);
    $user_email = $user_data['email'] ?? '';
    mysqli_stmt_close($user_query);

    if ($user_email) {
        // Send income notification
        sendIncomeNotification($user_email, $amount, $new_total_bal, $new_day_bal);
    }

    // Optionally, call your MLM logic here
    // Example: update_count_and_income($under_userid, $side, $capping);

    // Debug output
    echo "Money added and notification sent.";
}

// Example of how to call the function
$userid = 'example_user_id'; // Replace with the actual user ID
$amount = 500; // Replace with the amount to add
addMoneyToAccount($userid, $amount);
?>