<?php
session_start();
error_reporting(0);
include('../includes/dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

if(strlen($_SESSION['emplogin']) == 0) {   
    header('location:../index.php');
} else {
    if(isset($_POST['apply_leave_ajax'])) {
        $empid = $_SESSION['eid'];
        $leavetype = $_POST['leavetype'];
        $fromdate = $_POST['fromdate'];  
        $todate = $_POST['todate'];
        $description = $_POST['description'];  
        $status = 0;
        $isread = 0;

        // Validate dates
        if($fromdate > $todate) {
            echo json_encode(['status' => 'error', 'message' => 'End Date should be ahead of Starting Date!']);
            exit;
        }

        // Insert leave application into database
        $sql = "INSERT INTO tblleaves (LeaveType, ToDate, FromDate, Description, Status, IsRead, empid) 
                VALUES (:leavetype, :fromdate, :todate, :description, :status, :isread, :empid)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':leavetype', $leavetype, PDO::PARAM_STR);
        $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
        $query->bindParam(':todate', $todate, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':isread', $isread, PDO::PARAM_STR);
        $query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $query->execute();

        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId) {
            // Set up email with PHPMailer
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
                $mail->setFrom('sojitrayashkumar@gmail.com', 'HR Department');
                $mail->addAddress($_SESSION['emplogin']); // Employee email from session

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Leave Application Submitted';
                $mail->Body = "
                    <html>
                    <head>
                        <title>Leave Application Submitted</title>
                    </head>
                    <body>
                        <p>Dear Employee,</p>
                        <p>Your leave application has been successfully submitted.</p>
                        <p>Details:</p>
                        <ul>
                            <li>Leave Type: $leavetype</li>
                            <li>From: $fromdate</li>
                            <li>To: $todate</li>
                            <li>Description: $description</li>
                        </ul>
                        <p>Please wait for approval.</p>
                        <p>Regards,</p>
                        <p>HR Department</p>
                    </body>
                    </html>
                ";

                // Send email
                $mail->send();
                echo json_encode(['status' => 'success', 'message' => 'Leave application successfully submitted! Email sent.']);
            } catch (Exception $e) {
                echo json_encode(['status' => 'success', 'message' => 'Leave application submitted! However, email could not be sent. Error: ' . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not process the request. Try again later.']);
        }
        exit;
    }
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
    <div id="preloader">
        <div class="loader"></div>
    </div>
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
                            <li class="active">
                                <a href="leave.php" aria-expanded="true"><i class="ti-user"></i><span>Apply Leave</span></a>
                            </li>
                            <li>
                                <a href="leave-history.php" aria-expanded="true"><i class="ti-agenda"></i><span>View My Leave History</span></a>
                            </li>
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
                            <span></span>
                            <span></span>
                            <span></span>
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
                            <h4 class="page-title pull-left">Apply For Leave Days</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><span>Leave Form</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <?php include '../includes/employee-profile-section.php' ?>
                    </div>
                </div>
            </div>
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <div class="col-12 mt-5">
                                <div class="alert alert-dismissible fade show d-none" id="response-message">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="card">
                                    <form id="leaveForm">
                                        <div class="card-body">
                                            <h4 class="header-title">Employee Leave Form</h4>
                                            <p class="text-muted font-14 mb-4">Please fill up the form below.</p>
                                            <div class="form-group">
                                                <label for="fromdate" class="col-form-label">Starting Date</label>
                                                <input class="form-control" type="date" required id="fromdate" name="fromdate">
                                            </div>
                                            <div class="form-group">
                                                <label for="todate" class="col-form-label">End Date</label>
                                                <input class="form-control" type="date" required id="todate" name="todate">
                                            </div>
                                            <div class="form-group">
                                                <label class="col-form-label">Your Leave Type</label>
                                                <select class="custom-select" name="leavetype" required>
                                                    <option value="">Click here to select any ...</option>
                                                    <?php
                                                        $sql = "SELECT LeaveType FROM tblleavetype";
                                                        $query = $dbh->prepare($sql);
                                                        $query->execute();
                                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                        if($query->rowCount() > 0) {
                                                            foreach($results as $result) { ?> 
                                                                <option value="<?php echo htmlentities($result->LeaveType); ?>"><?php echo htmlentities($result->LeaveType); ?></option>
                                                            <?php }
                                                        } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="description" class="col-form-label">Describe Your Conditions</label>
                                                <textarea class="form-control" name="description" id="description" rows="5"></textarea>
                                            </div>
                                            <button class="btn btn-primary" type="submit">SUBMIT</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include '../includes/footer.php' ?>
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
        $(document).ready(function() {
            $('#leaveForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'leave.php',
                    method: 'POST',
                    data: $(this).serialize() + '&apply_leave_ajax=1',
                    dataType: 'json',
                    success: function(response) {
                        const alertBox = $('#response-message');
                        alertBox.removeClass('d-none alert-success alert-danger');
                        if(response.status === 'success') {
                            alertBox.addClass('alert-success').text(response.message);
                        } else {
                            alertBox.addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function() {
                        alert('There was an error processing your request.');
                    }
                });
            });
        });
    </script>
</body>
</html>
