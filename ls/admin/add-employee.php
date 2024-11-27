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

if(strlen($_SESSION['alogin'])==0){   
    header('location:index.php');
} else {
    if(isset($_POST['ajax']) && $_POST['ajax'] == 'submit'){
        // Employee data
        $empid = $_POST['empcode'];
        $fname = $_POST['firstName'];
        $lname = $_POST['lastName'];   
        $email = $_POST['email']; 
        $password = md5($_POST['password']);  
        $gender = $_POST['gender']; 
        $dob = $_POST['dob']; 
        $doj = $_POST['doj']; 
        $department = $_POST['department']; 
        $address = $_POST['address']; 
        $city = $_POST['city']; 
        $country = $_POST['country']; 
        $state = $_POST['state']; 
        $zipcode = $_POST['zipcode']; 
        $mobileno = $_POST['mobileno']; 
        $status = 1;

        // Insert employee into the database
        $sql = "INSERT INTO tblemployees(EmpId, FirstName, LastName, EmailId, Password, Gender, Dob, Doj, Department, Address, City, Country, State, ZipCode, Phonenumber, Status) 
                VALUES(:empid, :fname, :lname, :email, :password, :gender, :dob, :doj, :department, :address, :city, :country, :state, :zipcode, :mobileno, :status)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':empid', $empid, PDO::PARAM_STR);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':lname', $lname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':password', $password, PDO::PARAM_STR);
        $query->bindParam(':gender', $gender, PDO::PARAM_STR);
        $query->bindParam(':dob', $dob, PDO::PARAM_STR);
        $query->bindParam(':doj', $doj, PDO::PARAM_STR);
        $query->bindParam(':department', $department, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);
        $query->bindParam(':city', $city, PDO::PARAM_STR);
        $query->bindParam(':country', $country, PDO::PARAM_STR);
        $query->bindParam(':state', $state, PDO::PARAM_STR);
        $query->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
        $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->execute();
        
        $lastInsertId = $dbh->lastInsertId();
        
        if($lastInsertId){
            sendWelcomeEmail($email, $fname, $lname, $empid, $_POST['password'], $gender, $dob, $doj, $department, $address, $city, $country, $state, $zipcode, $mobileno);
            echo json_encode(['status' => 'success', 'message' => "Record has been added successfully. A welcome email has been sent."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Error occurred while adding the record."]);
        }
        exit();
    }

    // Fetch states based on selected country
    if (isset($_POST['countryId'])) {
        $countryId = intval($_POST['countryId']);
        
        $sql = "SELECT id, State FROM tbstate WHERE CountryID = :countryId"; // Assuming tbstate has a CountryID column
        $query = $dbh->prepare($sql);
        $query->bindParam(':countryId', $countryId, PDO::PARAM_INT);
        $query->execute();
        
        $states = $query->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($states); // Return states as JSON
        exit();
    }
}

function sendWelcomeEmail($email, $fname, $lname, $empid, $password, $gender, $dob, $doj, $department, $address, $city, $country, $state, $zipcode, $mobileno) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'sojitrayashkumar@gmail.com'; // SMTP username
        $mail->Password = 'tybv udrt yznb xyit'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;  // Use appropriate SMTP port

        // Recipients
        $mail->setFrom('sojitrayashkumar@gmail.com', 'Admin');
        $mail->addAddress($email); // Send email to the new employee's email address

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to the Company!';
        $mail->Body    = "
            <html>
            <head>
                <title>Welcome to the Company!</title>
            </head>
            <body>
                <p>Dear $fname $lname,</p>
                <p>Welcome to our company! We are excited to have you join us.</p>
                <p>Your account details are as follows:</p>
                <ul>
                    <li><strong>Employee ID:</strong> $empid</li>
                    <li><strong>Full Name:</strong> $fname $lname</li>
                    <li><strong>Email:</strong> $email</li>
                    <li><strong>Password:</strong> " . $_POST['password'] . "</li>
                    <li><strong>Gender:</strong> $gender</li>
                    <li><strong>Date of Birth:</strong> $dob</li>
                    <li><strong>Date of Join:</strong> $doj</li>
                    <li><strong>Department:</strong> $department</li>
                    <li><strong>Address:</strong> $address, $city, $state, $country, $zipcode</li>
                    <li><strong>Phone Number:</strong> $mobileno</li>
                </ul>
                <p>If you have any questions, feel free to reach out to the HR team.</p>
                <p>Best regards,<br>Admin Team</p>
            </body>
            </html>
        ";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        // Handle error
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
    <link rel="stylesheet" href="../assets/css/typography.css">
    <link rel="stylesheet" href="../assets/css/default-css.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <script src="../assets/js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="../assets/js/vendor/jquery-2.2.4.min.js"></script>
    <script type="text/javascript">
        function valid() {
            if(document.addemp.password.value != document.addemp.confirmpassword.value) {
                alert("New Password and Confirm Password Field do not match !!");
                document.addemp.confirmpassword.focus();
                return false;
            }
            return true;
        }

        $(document).ready(function() {
            $("form[name='addemp']").on("submit", function(event) {
                event.preventDefault(); 

                if (valid()) {
                    $.ajax({
                        url: "add-employee.php",
                        type: "POST",
                        data: $(this).serialize() + '&ajax=submit', 
                        dataType: "json",
                        success: function(response) {
                            if (response.status == "success") {
                                $("#response-message").html(`<div class="alert alert-success">${response.message}</div>`);
                                $("form[name='addemp']")[0].reset(); 
                            } else {
                                $("#response-message").html(`<div class="alert alert-danger">${response.message}</div>`);
                            }
                        },
                        error: function() {
                            $("#response-message").html('<div class="alert alert-danger">An error occurred while processing your request.</div>');
                        }
                    });
                }
            });

            // Fetch states based on selected country
            window.fetchStates = function(countryId) {
                if (countryId) {
                    $.ajax({
                        url: 'add-employee.php', // This file will handle the AJAX request
                        type: 'POST',
                        data: { countryId: countryId },
                        dataType: 'json',
                        success: function(response) {
                            $('#state').empty().append('<option value="">Choose..</option>'); // Clear previous options
                            $.each(response, function(index, state) {
                                $('#state').append('<option value="' + state.id + '">' + state.State + '</option>'); 
                            });
                        },
                        error: function() {
                            console.error("Error fetching states.");
                        }
                    });
                } else {
                    $('#state').empty().append('<option value="">Choose..</option>'); // Clear options if no country selected
                }
            };
        });

        function checkAvailabilityEmpid() {
            $("#loaderIcon").show();
            jQuery.ajax({
                url: "add-employee.php",
                data: 'empcode=' + $("#empcode").val(),
                type: "POST",
                success: function(data) {
                    $("#empid-availability").html(data);
                    $("#loaderIcon").hide();
                },
                error: function() {}
            });
        }

       
    </script>
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
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">ADMIN <i class="fa fa-angle-down"></i </h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="logout.php">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="main-content-inner">
                <div class="row">
                    <div class="col-lg-6 col-ml-12">
                        <div class="row">
                            <div class="col-12 mt-5">
                                <div id="response-message"></div> 
                                <div class="card">
                                    <form name="addemp" method="POST">
                                        <div class="card-body">
                                            <p class="text-muted font-14 mb-4">Please fill up the form in order to add employee records</p>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Employee ID</label>
                                                <input class="form-control" name="empcode" type="text" autocomplete="off" required id="empcode" onBlur="checkAvailabilityEmpid()">
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">First Name</label>
                                                <input class="form-control" name="firstName" type="text" required id="example-text-input">
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Last Name</label>
                                                <input class="form-control" name="lastName" type="text" autocomplete="off" required id="example-text-input">
                                            </div>

                                            <div class="form-group">
                                                <label for="example-email-input" class="col-form-label">Email</label>
                                                <input class="form-control" name="email" type="email" autocomplete="off" required id="example-email-input">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Preferred Department</label>
                                                <select class="custom-select" name="department" autocomplete="off">
                                                    <option value="">Choose..</option>
                                                    <?php 
                                                    $sql = "SELECT DepartmentName from tbldepartments";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    if($query->rowCount() > 0){
                                                        foreach($results as $result) { ?> 
                                                            <option value="<?php echo htmlentities($result->DepartmentName);?>"><?php echo htmlentities($result->DepartmentName);?></option>
                                                    <?php }} ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Gender</label>
                                                <select class="custom-select" name="gender" autocomplete="off">
                                                    <option value="">Choose..</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-date-input" class="col-form-label">D.O.B</label>
                                                <input class="form-control" type="date" name="dob" id="birthdate">
                                            </div>

                                            <div class="form-group">
                                                <label for="example-date-input" class="col-form-label">D.O.J</label>
                                                <input class="form-control" type="date" name="doj" id="joindate">
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Contact Number</label>
                                                <input class="form-control" name="mobileno" type="tel" maxlength="10" autocomplete="off" required>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Country</label>
                                                <select class="custom-select" name="country" id="country" autocomplete="off" onchange="fetchStates(this.value)">
                                                    <option value="">Choose..</option>
                                                    <?php 
                                                    $sql = "SELECT id, Country FROM tbcountry"; // Assuming you have an 'id' column in tbcountry
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                    if($query->rowCount() > 0){
                                                        foreach($results as $result) { ?> 
                                                            <option value="<?php echo htmlentities($result->id); ?>"><?php echo htmlentities($result->Country); ?></option>
                                                    <?php }} ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">State</label>
                                                <select class="custom-select" name="state" id="state" autocomplete="off">
                                                    <option value="">Choose..</option>

                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Address</label>
                                                <input class="form-control" name="address" type="text" autocomplete="off" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">City</label>
                                                <input class="form-control" name="city" type="text" autocomplete="off" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">ZipCode</label>
                                                <input class="form-control" name="zipcode" type="tel" maxlength="10" autocomplete="off" required>
                                            </div>
                                            <h4>Set Password for Employee Login</h4>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Password</label>
                                                <input class="form-control" name="password" type="password" autocomplete="off" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Confirmation Password</label>
                                                <input class="form-control" name="confirmpassword" type="password" autocomplete="off" required>
                                            </div>

                                            <button class="btn btn-primary" name="add" id="update" type="submit">PROCEED</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../includes/footer.php' ?>
        </div>
    </div>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script>
        zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
        ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "ee6b7db5b51705a13dc2339db3edaf6d"];
    </script>
    <script src="assets/js/line-chart.js"></script>
    <script src="assets/js/pie-chart.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>

</html>