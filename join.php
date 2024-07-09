<?php
include('php-includes/connect.php');
include('php-includes/check-login.php');

// Include PHPMailer classes
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$userid = $_SESSION['userid'];
$capping = 500;

// User clicked on join
if (isset($_GET['join_user'])) {
    $pin = mysqli_real_escape_string($con, $_GET['pin']);
    $email = mysqli_real_escape_string($con, $_GET['email']);
    $mobile = mysqli_real_escape_string($con, $_GET['mobile']);
    $address = mysqli_real_escape_string($con, $_GET['address']);
    $account = mysqli_real_escape_string($con, $_GET['account']);
    $under_userid = mysqli_real_escape_string($con, $_GET['under_userid']);
    $side = mysqli_real_escape_string($con, $_GET['side']);
    $password = "123456";

    $flag = 0;

    if ($pin != '' && $email != '' && $mobile != '' && $address != '' && $account != '' && $under_userid != '' && $side != '') {
        // User filled all the fields.
        if (pin_check($pin)) {
            // Pin is ok
            if (email_check($email)) {
                // Email is ok
                if (!email_check($under_userid)) {
                    // Under userid is ok
                    if (side_check($under_userid, $side)) {
                        // Side check
                        $flag = 1;
                    } else {
                        echo '<script>alert("The side you selected is not available.");</script>';
                    }
                } else {
                    echo '<script>alert("Invalid Under userid.");</script>';
                }
            } else {
                echo '<script>alert("This user id already available.");</script>';
            }
        } else {
            echo '<script>alert("Invalid pin");</script>';
        }
    } else {
        echo '<script>alert("Please fill all the fields.");</script>';
    }

    if ($flag == 1) {
        // Insert into User profile
        $query = mysqli_prepare($con, "INSERT INTO users(`email`,`password`,`mobile`,`address`,`account`,`under_userid`,`side`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "sssssss", $email, $password, $mobile, $address, $account, $under_userid, $side);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);

        // Insert into Tree
        $query = mysqli_prepare($con, "INSERT INTO tree(`userid`) VALUES (?)");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);

        // Insert to side
        $query = mysqli_prepare($con, "UPDATE tree SET `$side` = ? WHERE userid = ?");
        mysqli_stmt_bind_param($query, "ss", $email, $under_userid);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);

        // Update pin status to close
        $query = mysqli_prepare($con, "UPDATE pin_list SET status = 'close' WHERE pin = ?");
        mysqli_stmt_bind_param($query, "s", $pin);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);

        // Insert into Income
        $query = mysqli_prepare($con, "INSERT INTO income(`userid`) VALUES (?)");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        mysqli_stmt_close($query);

        // Update count and Income
        update_count_and_income($under_userid, $side, $capping);

        sendWelcomeEmail($email, $password);
        echo '<script>alert("User successfully joined.");</script>';
    }
}

// Functions
function pin_check($pin) {
    global $con, $userid;
    $query = mysqli_prepare($con, "SELECT * FROM pin_list WHERE pin = ? AND userid = ? AND status = 'open'");
    mysqli_stmt_bind_param($query, "ss", $pin, $userid);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $is_valid = mysqli_num_rows($result) > 0;
    mysqli_stmt_close($query);
    return $is_valid;
}

function email_check($email) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($query, "s", $email);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $is_available = mysqli_num_rows($result) == 0;
    mysqli_stmt_close($query);
    return $is_available;
}

function side_check($email, $side) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM tree WHERE userid = ?");
    mysqli_stmt_bind_param($query, "s", $email);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $r = mysqli_fetch_array($result);
    $side_value = $r[$side] ?? '';
    mysqli_stmt_close($query);
    return $side_value == '';
}

function income($userid) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM income WHERE userid = ?");
    mysqli_stmt_bind_param($query, "s", $userid);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_array($result) ?: [];
    mysqli_stmt_close($query);
    return $data;
}

function tree($userid) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM tree WHERE userid = ?");
    mysqli_stmt_bind_param($query, "s", $userid);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_array($result) ?: [];
    mysqli_stmt_close($query);
    return $data;
}

function getUnderId($userid) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($query, "s", $userid);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $under_userid = mysqli_fetch_array($result)['under_userid'] ?? '';
    mysqli_stmt_close($query);
    return $under_userid;
}

function getUnderIdPlace($userid) {
    global $con;
    $query = mysqli_prepare($con, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($query, "s", $userid);
    mysqli_stmt_execute($query);
    $result = mysqli_stmt_get_result($query);
    $side = mysqli_fetch_array($result)['side'] ?? '';
    mysqli_stmt_close($query);
    return $side;
}

function update_count_and_income($under_userid, $side, $capping) {
    global $con;
    $temp_under_userid = $under_userid;
    $temp_side_count = $side . 'count';
    $temp_side = $side;
    $total_count = 1;

    while ($total_count > 0) {
        $query = mysqli_prepare($con, "SELECT * FROM tree WHERE userid = ?");
        mysqli_stmt_bind_param($query, "s", $temp_under_userid);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        $r = mysqli_fetch_array($result);
        $current_temp_side_count = ($r[$temp_side_count] ?? 0) + 1;

        $update_tree_query = mysqli_prepare($con, "UPDATE tree SET `$temp_side_count` = ? WHERE userid = ?");
        mysqli_stmt_bind_param($update_tree_query, "is", $current_temp_side_count, $temp_under_userid);
        mysqli_stmt_execute($update_tree_query);
        mysqli_stmt_close($update_tree_query);

        if ($temp_under_userid != "") {
            $income_data = income($temp_under_userid);
            $day_bal = $income_data['day_bal'] ?? 0;
            $current_bal = $income_data['current_bal'] ?? 0;
            $total_bal = $income_data['total_bal'] ?? 0;

            if ($day_bal < $capping) {
                $tree_data = tree($temp_under_userid);
                $temp_left_count = $tree_data['leftcount'] ?? 0;
                $temp_right_count = $tree_data['rightcount'] ?? 0;
                if ($temp_left_count > 0 && $temp_right_count > 0) {
                    if ($temp_side == 'left' && $temp_left_count <= $temp_right_count) {
                        $new_day_bal = $day_bal + 100;
                        $new_current_bal = $current_bal + 100;
                        $new_total_bal = $total_bal + 100;
                        $update_income_query = mysqli_prepare($con, "UPDATE income SET day_bal = ?, current_bal = ?, total_bal = ? WHERE userid = ?");
                        mysqli_stmt_bind_param($update_income_query, "iiis", $new_day_bal, $new_current_bal, $new_total_bal, $temp_under_userid);
                        mysqli_stmt_execute($update_income_query);
                        mysqli_stmt_close($update_income_query);
                    } else if ($temp_side == 'right' && $temp_right_count <= $temp_left_count) {
                        $new_day_bal = $day_bal + 100;
                        $new_current_bal = $current_bal + 100;
                        $new_total_bal = $total_bal + 100;
                        $update_income_query = mysqli_prepare($con, "UPDATE income SET day_bal = ?, current_bal = ?, total_bal = ? WHERE userid = ?");
                        mysqli_stmt_bind_param($update_income_query, "iiis", $new_day_bal, $new_current_bal, $new_total_bal, $temp_under_userid);
                        mysqli_stmt_execute($update_income_query);
                        mysqli_stmt_close($update_income_query);
                    }
                }
            }

            $temp_side = getUnderIdPlace($temp_under_userid);
            $temp_under_userid = getUnderId($temp_under_userid);
        }

        if ($temp_under_userid == "") {
            $total_count = 0;
        }
    }
}


function sendWelcomeEmail($email, $password) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gt579175@gmail.com';
        $mail->Password = 'wxrmvjyizornevaj'; // Avoid hardcoding, use environment variables
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('gt579175@gmail.com', 'MLM Website');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to MLM System';
        $mail->Body = 'Your account is created successfully.<br><br> Your Login details:<br>Email: ' . $email . '<br>Password: ' . $password .'<br>Url: <a href="http://localhost/phpwithdb/Multi-level-marketing.github.io/index.php">Login</a>';
        $mail->AltBody = 'Your account is created successfully. Your Login details: Email: ' . $email . ' Password: ' . $password;

        $mail->send();
        echo 'Email has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mlml Website  - Join</title>

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
                        <h1 class="page-header">Join</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-4">
                        <form method="get">
                            <div class="form-group">
                                <label>Pin</label>
                                <input type="text" name="pin" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Account</label>
                                <input type="text" name="account" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Under Userid</label>
                                <input type="text" name="under_userid" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Side</label><br>
                                <input type="radio" name="side" value="left"> Left
                                <input type="radio" name="side" value="right"> Right
                            </div>
                            
                            <div class="form-group">
                                <input type="submit" name="join_user" class="btn btn-primary" value="Join">
                            </div>
                        </form>
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