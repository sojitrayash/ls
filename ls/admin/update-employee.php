<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../includes/dbconn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit;
}

$eid = isset($_GET['empid']) ? intval($_GET['empid']) : null;
if ($eid === null) {
    echo "Error: Employee ID not set in URL.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $_POST['firstName'];
    $lname = $_POST['lastName'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $doj = $_POST['doj'];
    $department = $_POST['department'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    $mobileno = $_POST['mobileno'];
    $zipcode = $_POST['zipcode'];
    $email = $_POST['email'];

    $sql = "UPDATE tblemployees SET FirstName = :fname, LastName = :lname, Gender = :gender, Dob = :dob, Doj = :doj, Department = :department, Address = :address, City = :city, State = :state, Country = :country, ZipCode = :zipcode, Phonenumber = :mobileno, EmailId = :email WHERE id = :eid";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':fname', $fname, PDO::PARAM_STR);
    $query->bindParam(':lname', $lname, PDO::PARAM_STR);
    $query->bindParam(':gender', $gender, PDO::PARAM_STR);
    $query->bindParam(':dob', $dob, PDO::PARAM_STR);
    $query->bindParam(':doj', $doj, PDO::PARAM_STR);
    $query->bindParam(':department', $department, PDO::PARAM_STR);
    $query->bindParam(':address', $address, PDO::PARAM_STR);
    $query->bindParam(':city', $city, PDO::PARAM_STR);
    $query->bindParam(':country', $country, PDO::PARAM_STR);
    $query->bindParam(':state', $state, PDO::PARAM_STR);
    $query->bindParam(':mobileno', $mobileno, PDO::PARAM_STR);
    $query->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':eid', $eid, PDO::PARAM_INT);

    try {
        $query->execute();

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
            $mail->setFrom('sojitrayashkumar@gmail.com', 'Support Team');
            $mail->addAddress($email); // Send email to the updated email address

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Profile Has Been Updated';
            $mail->Body = "
                <html>
                <head>
                    <title>Profile Update Notification</title>
                </head>
                <body>
                    <p>Dear $fname $lname,</p>
                    <p>Your profile has been successfully updated. Here are your updated details:</p>
                    <ul>
                        <li><strong>First Name:</strong> $fname</li>
                        <li><strong>Last Name:</strong> $lname</li>
                        <li><strong>Gender:</strong> $gender</li>
                        <li><strong>Date of Birth:</strong> $dob</li>
                        <li><strong>Department:</strong> $department</li>
                        <li><strong>Address:</strong> $address</li>
                        <li><strong>City:</strong> $city</li>
                        <li><strong>Country:</strong> $country</li>
                        <li><strong>Phone Number:</strong> $mobileno</li>
                        <li><strong>Email:</strong> $email</li>
                    </ul>
                    <p>If any of these details are incorrect, please contact the support team immediately.</p>
                    <p>Regards,<br>Support Team</p>
                </body>
                </html>
            ";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'Your record has been updated successfully. A confirmation email has been sent.']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Your record was updated successfully, but the email could not be sent. Error: ' . $mail->ErrorInfo]);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error updating employee data: ' . $e->getMessage()]);
    }
    exit;
}

$sql = "SELECT * FROM tblemployees WHERE id = :eid";
$query = $dbh->prepare($sql);
$query->bindParam(':eid', $eid, PDO::PARAM_INT);
$query->execute();

$result = $query->fetch(PDO::FETCH_OBJ);
if (!$result) {
    echo "Error: Employee not found.";
    exit;
}

$sql = "SELECT DepartmentName FROM tbldepartments";
$query = $dbh->prepare($sql);
$query->execute();
$departments = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!doctype html>
<html lang="en">
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
    <script>
        $(document).ready(function() {
            $('#country').change(function() {
                var countryId = $(this).val();
                $.ajax({
                    type: "POST",
                    url: "fetch-states.php", // Separate PHP file for fetching states
                    data: { countryId: countryId },
                    success: function(response) {
                        $('#state').html(response);
                    }
                });
            });
            $('#update').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: "POST",
                    url: "update-employee.php?empid=<?php echo $eid; ?>",
                    data: formData,
                    dataType: "json",
                    success: function(response) {
                        $('#message').show().removeClass('alert-success alert-danger alert-warning');
                        if (response.status === 'success') {
                            $('#message').addClass('alert-success').text(response.message);
                        } else if (response.status === 'warning') {
                            $('#message').addClass('alert-warning').text(response.message);
                        } else {
                            $('#message').addClass('alert-danger').text(response.message);
                        }
                    },
                    error: function() {
                        $('#message').show().addClass('alert-danger').text('Error updating employee!');
                    }
                });
            });
        });
    </script>
</head>
<body>
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
                        $page='update-employee';
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
                            <li id ="full-view-exit"><i class="ti-zoom-out"></i></li>
                            <?php include '../includes/admin-notification.php'?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Update Employee Section</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="employees.php">Employee</a></li>
                                <li><span>Update</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="../assets/images/admin.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">ADMIN <i class="fa fa-angle-down"></i></h4>
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
                                <div id="message" class="alert" style="display:none;" role="alert"></div>
                                <div class="card">
                                    <form name="addemp" id="update" method="POST">
                                        <div class="card-body">
                                            <p class="text-muted font-14 mb-4">Please make changes on the form below in order to update your profile</p>

                                            <?php 
                                                $eid = $_SESSION['emplogin'];
                                                $sql = "SELECT * from tblemployees where EmailId=:eid";
                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) { 
                                            ?> 

                                            <div class="form-group">
                                                <label for="firstName" class="col-form-label">First Name</label>
                                                <input class="form-control" name="firstName" value="<?php echo htmlentities($result->FirstName); ?>" type="text" required id="firstName">
                                            </div>

                                            <div class="form-group">
                                                <label for="lastName" class="col-form-label">Last Name</label>
                                                <input class="form-control" name="lastName" value="<?php echo htmlentities($result->LastName); ?>" type="text" required id="lastName">
                                            </div>

                                            <div class="form-group">
                                                <label for="email" class="col-form-label">Email</label>
                                                <input class="form-control" name="email" type="email" value="<?php echo htmlentities($result->EmailId); ?>" readonly required id="email">
                                            </div>

                                            <div class="form-group">
                                                <label class="col-form-label">Gender</label>
                                                <select class="custom-select" name="gender" required>
                                                    <option value="<?php echo htmlentities($result->Gender); ?>"><?php echo htmlentities($result->Gender); ?></option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="dob" class="col-form-label">D.O.B</label>
                                                <input class="form-control" type="date" name="dob" id="dob" value="<?php echo htmlentities($result->Dob); ?>" required>
                                            </div>

                                            <div class="form-group">
                                            <label for="doj">D.O.J</label>
                                            <input class="form-control" type="date" name="doj" id="doj" value="<?php echo htmlentities($result->Doj); ?>" required>
                                        </div>

                                            <div class="form-group">
                                                <label for="mobileno" class="col-form-label">Mobile Number</label>
                                                <input class="form-control" name="mobileno" type="tel" value="<?php echo htmlentities($result->Phonenumber); ?>" maxlength="10" required id="mobileno">
                                            </div>
                                            <div class="form-group">
                                                <label for="example-text-input" class="col-form-label">Employee ID</label>
                                                <input class="form-control" name=" empcode" type="text" autocomplete="off" readonly required value="<?php echo htmlentities($result->EmpId);?>" id="example-text-input">
                                                    </div>

                                            <div class="form-group">
                                                <label for="department" class="col-form-label">Department</label>
                                                <select class="custom-select" name="department" required>
                                                    <option value="<?php echo htmlentities($result->Department); ?>"><?php echo htmlentities($result->Department); ?></option>
                                                    <?php 
                                                    $sql = "SELECT DepartmentName FROM tbldepartments";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $departments = $query->fetchAll(PDO::FETCH_OBJ);
                                                    foreach ($departments as $department) {   
                                                    ?>  
                                                        <option value="<?php echo htmlentities($department->DepartmentName); ?>"><?php echo htmlentities($department->DepartmentName); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="address" class="col-form-label">Address</label>
                                                <input class="form-control" name="address" type="text" value="<?php echo htmlentities($result->Address); ?>" required id="address">
                                            </div>

                                            <div class="form-group">
                                                <label for="city" class="col-form-label">City</label>
                                                <input class="form-control" name="city" type="text" value="<?php echo htmlentities($result->City); ?>" required id="city">
                                            </div>

                                            <div class="form-group">
                                            <label for="country">Country</label>
                                            <select class="custom-select" name="country" id="country" required>
                                                <?php 
                                                $sql = "SELECT id, country FROM tbcountry";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $countries = $query->fetchAll(PDO::FETCH_OBJ);
                                                foreach ($countries as $country) {   
                                                ?>  
                                                    <option value="<?php echo htmlentities($country->id); ?>" <?php if (htmlentities($country->id) == htmlentities($result->Country)) { echo "selected"; } ?>><?php echo htmlentities($country->country); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <select class="custom-select" name="state" id="state" required>
                                                <?php 
                                                $sql = "SELECT id, State FROM tbstate WHERE CountryId = :countryId";
                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':countryId', $result->Country, PDO::PARAM_INT);
                                                $query->execute();
                                                $states = $query->fetchAll(PDO::FETCH_OBJ);
                                                foreach ($states as $state) {   
                                                ?>  
                                                    <option value="<?php echo htmlentities($state->id); ?>" <?php if (htmlentities($state->id) == htmlentities($result->State)) { echo "selected"; } ?>><?php echo htmlentities($state->State); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        
                                        <div class="form-group">
                                            <label for="zipcode">Zip Code</label>
                                            <input class="form-control" name="zipcode" type="tel" value="<?php echo htmlentities($result->ZipCode); ?>" maxlength="10" required id="zipcode">
                                        </div>

                                            <?php }
                                            } ?>

                                            <button class="btn btn-primary" id="update" type="submit">Update Profile</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include '../includes/footer.php'; ?>
        </div>
    </div>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/metisMenu.min.js"></script>
    <script src="../assets/js/jquery.slimscroll.min.js"></script>
    <script src="../assets/js/jquery.slicknav.min.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
</body>
</html>