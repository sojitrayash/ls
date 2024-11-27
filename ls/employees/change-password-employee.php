<?php
session_start();
error_reporting(0);
include('../includes/dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';


if (strlen($_SESSION['emplogin']) == 0) {
    header('location:../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'change-password') {
    $password = md5($_POST['password']);
    $newpassword = md5($_POST['newpassword']);
    $username = $_SESSION['emplogin'];

    $sql = "SELECT Password FROM tblemployees WHERE EmailId=:username AND Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();

    $message = '';
    if ($query->rowCount() > 0) {
        $con = "UPDATE tblemployees SET Password=:newpassword WHERE EmailId=:username";
        $chngpwd1 = $dbh->prepare($con);
        $chngpwd1->bindParam(':username', $username, PDO::PARAM_STR);
        $chngpwd1->bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
        $chngpwd1->execute();

        $message = 'Your password has been successfully updated.';
        $status = 'success';
    } else {
        $message = 'Sorry, your current password is incorrect.';
        $status = 'error';
    }

    // Send email notification regardless of the password change
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'sojitrayashkumar@gmail.com'; // SMTP username
        $mail->Password = 'tybv udrt yznb xyit'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('sojitrayashkumar@gmail.com', 'Support Team');
        $mail->addAddress($username); // Send email to the logged-in user

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Change Attempt Notification';
        $mail->Body = "
            <html>
            <head>
                <title>Password Change Attempt</title>
            </head>
            <body>
                <p>Dear User,</p>
                <p>A password change attempt was made for your account.</p>
                <p><strong>Status:</strong> {$message}</p>
                <p>If you did not initiate this action, please contact our support team immediately.</p>
                <p>Regards,<br>Support Team</p>
            </body>
            </html>
        ";

        $mail->send();
        echo json_encode(['status' => $status, 'message' => $message . ' Notification email sent.']);
    } catch (Exception $e) {
        echo json_encode(['status' => $status, 'message' => $message . ' However, the notification email could not be sent.']);
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
    <link rel="shortcut icon" type="image/png" href="../assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/slicknav.min.css">
    <link rel="stylesheet" href="../assets/css/typography.css">
    <link rel="stylesheet" href="../assets/css/default-css.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <script src="../assets/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
    <div id="preloader"><div class="loader"></div></div>
    <div class="page-container">
        <div class="sidebar-menu">
            <div class="sidebar-header">
                <div class="logo">
                    <a href="leave.php"><img src="../assets/images/icon/logo.png" alt="logo"></a>
                </div>
            </div>
            <div class="main-menu">
                <div class="menu-inner">
                    <nav>
                        <ul class="metismenu" id="menu">
                            <li><a href="leave.php"><i class="ti-user"></i><span>Apply Leave</span></a></li>
                            <li><a href="leave-history.php"><i class="ti-agenda"></i><span>View My Leave History</span></a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="header-area">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-8 clearfix">
                        <div class="nav-btn pull-left">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-4 clearfix">
                        <ul class="notification-area pull-right">
                            <li id="full-view"><i class="ti-fullscreen"></i></li>
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Change Current Password</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><span>Password Fields</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <?php include '../includes/employee-profile-section.php'; ?>
                    </div>
                </div>
            </div>
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <div class="col-12 mt-5">
                                <div id="alert-area"></div>
                                <div class="card">
                                    <form id="change-password-form">
                                        <div class="card-body">
                                            <h4 class="header-title">Change Password</h4>
                                            <p class="text-muted font-14 mb-4">Please fill up the form to change your current password.</p>
                                            <div class="form-group">
                                                <label for="password" class="col-form-label">Existing Password</label>
                                                <input class="form-control" id="password" type="password" name="password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="newpassword" class="col-form-label">New Password</label>
                                                <input class="form-control" id="newpassword" type="password" name="newpassword" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirmpassword" class="col-form-label">Confirm Password</label>
                                                <input class="form-control" id="confirmpassword" type="password" name="confirmpassword" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit">CHANGE PASSWORD</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/footer.php'; ?>
    </div>
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script>
        $('#change-password-form').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize() + '&ajax=change-password';
            $.ajax({
                url: 'change-password-employee.php', 
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    const alertArea = $('#alert-area');
                    alertArea.empty();
                    const alertClass = response.status === 'success' ? 'alert-success' : 'alert-danger';
                    alertArea.html(`<div class="alert ${alertClass} alert-dismissible fade show">
                        <strong>Info: </strong>${response.message}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>`);
                }
            });
        });
    </script>
</body>

</html>
