<?php
session_start();
error_reporting(0);
include('includes/dbconn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if (isset($_POST['action']) && $_POST['action'] == 'change') {
    $newpassword = md5($_POST['newpassword']);
    $empid = $_SESSION['empid'];

    // Update the password in the database
    $con = "UPDATE tblemployees SET Password=:newpassword WHERE id=:empid";
    $chngpwd1 = $dbh->prepare($con);
    $chngpwd1->bindParam(':empid', $empid, PDO::PARAM_STR);
    $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
    $chngpwd1->execute();

    // Fetch employee email for sending notification
    $query = "SELECT EmailId FROM tblemployees WHERE id=:empid";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(':empid', $empid, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $email = $result->EmailId;

    // Send notification email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'sojitrayashkumar@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'tybv udrt yznb xyit'; // Replace with your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('sojitrayashkumar@gmail.com', 'Employee Management');
        $mail->addAddress($email); // Send email to employee

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Changed Successfully';
        $mail->Body = "Hello,<br><br>Your password has been successfully changed. If you didn't request this change, please contact the support team immediately.<br><br>Best Regards,<br>Employee Management Team";

        $mail->send();
        echo json_encode(array('status' => 'success', 'message' => 'Your Password has been updated and an email notification has been sent.'));
    } catch (Exception $e) {
        echo json_encode(array('status' => 'error', 'message' => "Password updated, but email could not be sent. Error: {$mail->ErrorInfo}"));
    }
    exit;
}
?>


<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Employee Leave Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/metisMenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.min.css">
    <link rel="stylesheet" href="assets/css/typography.css">
    <link rel="stylesheet" href="assets/css/default-css.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div class="login-area login-s2">
        <div class="container">
            <div class="login-box ptb--100">
                <form id="recoverForm" method="POST">
                    <div class="login-form-head">
                        <h4>Recover Your Password</h4>
                        <p>Please provide your employee details for recovery.</p>
                        <div id="alertMessage" style="display:none;"></div>
                    </div>
                    <div class="login-form-body">
                        <div class="form-gp">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" id="exampleInputEmail1" name="emailid" autocomplete="off">
                            <i class="ti-email"></i>
                            <div class="text-danger"></div>
                        </div>
                        <div class="form-gp">
                            <label for="exampleInputPassword1">Employee ID</label>
                            <input type="text" id="exampleInputPassword1" name="empid" autocomplete="off">
                            <i class="ti-id-badge"></i>
                            <div class="text-danger"></div>
                        </div>
                        
                        <div class="submit-btn-area">
                            <button id="form_submit" name="submit" type="submit">PROCEED FOR RECOVERY <i class="ti-arrow-right"></i></button>
                        </div>
                        <div class="form-footer text-center mt-5">
                            <p class="text-muted">Have an Account? <a href="index.php">Login Now</a></p>
                        </div>
                    </div>
                </form>

                <div id="updatePasswordSection" style="display:none;">
                    <div class="login-form-body">
                        <form id="updatePwdForm" method="POST">
                            <div class="form-gp">
                                <label for="newPassword">Enter New Password</label>
                                <input type="password" id="newPassword" name="newpassword" required autocomplete="off">
                                <i class="ti-key"></i>
                                <div class="text-danger"></div>
                            </div>
                            <div class="form-gp">
                                <label for="confirmPassword">Confirm Password</label>
                                <input type=" password" id="confirmPassword" name="confirmpassword" required autocomplete="off">
                                <i class="ti-key"></i>
                                <div class="text-danger"></div>
                            </div>
                            
                            <div class="submit-btn-area">
                                <button id="form_submit" name="change" type="submit">DONE <i class="ti-arrow-right"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php 
                if(isset($_POST['submit'])) {
                    $empid = $_POST['empid'];
                    $email = $_POST['emailid'];
                    $sql = "SELECT id FROM tblemployees WHERE EmailId=:email and EmpId=:empid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':email', $email, PDO::PARAM_STR);
                    $query->bindParam(':empid', $empid, PDO::PARAM_STR);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    if($query->rowCount() > 0) {
                        foreach ($results as $result) {
                            $_SESSION['empid'] = $result->id;
                        }
                        echo "<script>document.getElementById('updatePasswordSection').style.display = 'block';</script>";
                    } else {
                        echo "<script>alert('Sorry, Invalid Details.');</script>";
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script src="assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/metisMenu.min.js"></script>
    <script src="assets/js/jquery.slimscroll.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/scripts.js"></script>

    <script>
        $(document).ready(function() {
            $('#updatePwdForm').on('submit', function(e) {
                e.preventDefault(); 
                $.ajax({
                    type: 'POST',
                    url: 'password-recovery.php', 
                    data: $(this).serialize() + '&action=change', 
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#alertMessage').html('<div class="alert alert-success">' + response.message + '</div>').show(); 
                        } else {
                            $('#alertMessage').html('<div class="alert alert-danger">' + response.message + '</div>').show(); 
                        }
                    },
                    error: function() {
                        $('#alertMessage').html('<div class="alert alert-danger">Something went wrong.</div>').show(); 
                    }
                });
            });
        });
    </script>
</body>

</html>