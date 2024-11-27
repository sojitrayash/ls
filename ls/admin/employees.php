<?php
session_start();
error_reporting(0);
ini_set('display_errors', 1);
include('../includes/dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {

    if(isset($_GET['inid'])) {
        $id = $_GET['inid'];
        $status = 0;
        
        // Update status to deactivated
        $sql = "UPDATE tblemployees SET Status=:status WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();

        // Send email notification about deactivation
        sendEmail($id, 'deactivated');
        
        header('location:employees.php');
    }

    if(isset($_GET['id'])) {
        $id = $_GET['id'];
        $status = 1;
        
        // Update status to activated
        $sql = "UPDATE tblemployees SET Status=:status WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();

        // Send email notification about activation
        sendEmail($id, 'activated');
        
        header('location:employees.php');
    }

    // Handle delete operation
    if(isset($_GET['delete_id'])) {
        $id = $_GET['delete_id'];

        // Delete employee record
        $sql = "DELETE FROM tblemployees WHERE id=:id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $query->execute();

        // Send email notification about deletion
        sendEmail($id, 'deleted');
        
        header('location:employees.php');
    }
}

// Function to send email
function sendEmail($empId, $status) {
    global $dbh;
    
    // Get employee's email address
    $sql = "SELECT FirstName, LastName, EmailId FROM tblemployees WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $empId, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $fname = $result['FirstName'];
        $lname = $result['LastName'];
        $email = $result['EmailId'];

        // Prepare email content based on status
        $statusMessage = '';
        switch ($status) {
            case 'activated':
                $statusMessage = 'Your account has been activated.';
                break;
            case 'deactivated':
                $statusMessage = 'Your account has been deactivated.';
                break;
            case 'deleted':
                $statusMessage = 'Your account has been deleted from our system.';
                break;
            default:
                $statusMessage = 'There was a change in your account status.';
        }

        // Send email using PHPMailer
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
            $mail->setFrom('sojitrayashkumar@gmail.com', 'Admin');
            $mail->addAddress($email); // Send to the employee's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Status Update';
            $mail->Body    = "
                <html>
                <head>
                    <title>Account Status Update</title>
                </head>
                <body>
                    <p>Dear $fname $lname,</p>
                    <p>$statusMessage</p>
                    <p>If you have any questions, please contact the admin.</p>
                    <p>Best regards,<br>Admin Team</p>
                </body>
                </html>
            ";

            $mail->send();
        } catch (Exception $e) {
            // Optionally log or display the error
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>


<!doctype html>
<html class="no-js" lang="en">

<head>
<meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel - Employee Leave</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="../assets/images/icon/favicon.ico">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/metisMenu.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/slicknav.min.css">
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
    <link rel="stylesheet" href="../assets/css/typography.css">
    <link rel="stylesheet" href="../assets/css/default-css.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <script src="../assets/js/vendor/modernizr-2.8.3.min.js"></script>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
</head>

<body>
<div id="preloader">
    <div class="loader"></div>
</div>

<div class="page-container">
    <div class="sidebar-menu">
        <div class="sidebar-header">
            <div class="logo">
                <a href="dashboard.php"><img src="../assets/images/icon/logo.png" alt="logo"></a>
            </div>
        </div>
        <div class="main-menu">
            <div class="menu-inner">
                <?php
                        $page='employee';
                        include '../includes/admin-sidebar.php';
                    ?>
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
                        <?php include '../includes/admin-notification.php'?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="page-title-area">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="breadcrumbs-area clearfix">
                        <h4 class="page-title pull-left">Add Employee Section</h4>
                        <ul class="breadcrumbs pull-left">
                            <li><a href="employees.php">Employee</a></li>
                            <li><span>Add</span></li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-6 clearfix">
                    <div class="user-profile pull-right">
                        <img class="avatar user-thumb" src="../assets/images/admin.png" alt="avatar">
                        <h4 class="user-name dropdown-toggle" data-toggle="dropdown">ADMIN <i
                                class="fa fa-angle-down"></i></h4>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="logout.php">Log Out</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content-inner col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <div class="data-tables datatable-dark">
                        <center><a href="add-employee.php" class="btn btn-sm btn-info">Add New Employee</a></center>
                        <table id="dataTable3" class="table table-hover table-striped text-center">
                            <thead class="text-capitalize">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Joined On</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTable">

                                <?php 
                                $sql = "SELECT EmpId,FirstName,LastName,Department,Status,Doj,id from  tblemployees";
                                $query = $dbh -> prepare($sql);
                                $query->execute();
                                $results=$query->fetchAll(PDO::FETCH_OBJ);
                                $cnt=1;
                                if($query->rowCount() > 0) {
                                    foreach($results as $result) { ?>
                                <tr id="emp-<?php echo $result->id; ?>">
                                    <td>
                                        <?php echo htmlentities($cnt);?>
                                    </td>
                                    <td>
                                        <?php echo htmlentities($result->FirstName);?>&nbsp;
                                        <?php echo htmlentities($result->LastName);?>
                                    </td>
                                    <td>
                                        <?php echo htmlentities($result->EmpId);?>
                                    </td>
                                    <td>
                                        <?php echo htmlentities($result->Department);?>
                                    </td>
                                    <td>
                                        <?php echo date('d-m-Y', strtotime($result->Doj)); ?>

                                    </td>
                                    <td>
                                        <?php 
                                                $stats=$result->Status;
                                                if($stats){ ?>
                                        <span class="badge badge-pill badge-success">Active</span>
                                        <?php } else { ?>
                                        <span class="badge badge-pill badge-danger">Inactive</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <a href="update-employee.php?empid=<?php echo htmlentities($result->id);?>">
                                            <i class="fa fa-edit" style="color:green"></i>
                                        </a>
                                        <?php if($result->Status==1) { ?>
                                        <a href="employees.php?inid=<?php echo htmlentities($result->id);?>"
                                            onclick="return confirm('Are you sure you want to inactive this employee?');">
                                            <i class="fa fa-times-circle" style="color:red" title="Inactive"></i>
                                        </a>
                                        <?php } else { ?>
                                        <a href="employees.php?id=<?php echo htmlentities($result->id);?>"
                                            onclick="return confirm('Are you sure you want to active this employee?');">
                                            <i class="fa fa-check" style="color:green" title="Active"></i>
                                        </a>
                                        <?php } ?>
                                        <button class="btn btn-sm btn-danger deleteEmployee"
                                            data-id="<?php echo $result->id; ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php $cnt++; } 
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php include '../includes/footer.php' ?>
        </div>
    </div>
   
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.jqueryui.min.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>

    <script>
    $(document).ready(function(){
        $('.deleteEmployee').on('click', function() {
            var empId = $(this).data('id');
            var row = $('#emp-' + empId); 
            if(confirm('Are you sure you want to delete this employee?')) {
                $.ajax({
                    url: 'delete-employee.php', 
                    type: 'POST',
                    data: {id: empId},
                    success: function(response) {
                        if(response == 'success') {
                            row.remove(); 
                        } else {
                            alert('Failed to delete the employee.');
                        }
                    }
                });
            }
        });
    });
    </script>

</body>
</html>


