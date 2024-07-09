<?php
include('php-includes/check-login.php');
include('php-includes/connect.php');
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
        $mail->Username = 'EMAIL';
        $mail->Password = 'password'; // Use environment variables or configuration for the password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('Email', 'MLM Website');
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
    $capping = 500; // Example capping amount

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

// Handle form submission for adding money
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    if ($amount > 0) {
        addMoneyToAccount($userid, $amount);
    } else {
        echo "Invalid amount.";
    }
}

// Fetch current income data for display
$query = mysqli_query($con, "SELECT * FROM income WHERE userid='$userid'");
$result = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>MLM Website - Income</title>
    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div id="wrapper">
        <!-- Navigation -->
        <?php include('php-includes/menu.php'); ?>
        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Income</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="panel-title">Current Income</div>
                            </div>
                            <div class="panel-body">
                                <i class="fa fa-rupess"></i> <?php echo number_format($result['current_bal'], 2); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="panel-title">Total Income</div>
                            </div>
                            <div class="panel-body">
                                <i class="fa fa-rupess"></i> <?php echo number_format($result['total_bal'], 2); ?>
                            </div>
                        </div>
                    </div>
                </div><!--/.row-->
                <!-- Form to Add Money -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="panel-title">Add Money</div>
                            </div>
                            <div class="panel-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Money</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div><!--/.row-->
            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>
</body>

</html>
