<?php
    session_start();
    error_reporting(0);
    include('../includes/dbconn.php');
    if(strlen($_SESSION['alogin'])==0){   
        header('location:index.php');
    } else {
        if(isset($_POST['action']) && $_POST['action'] == 'addAdmin') {
            $fullname = $_POST['fullname']; 
            $email = $_POST['email']; 
            $password = md5($_POST['password']); 
            $username = $_POST['username']; 

            $sql = "INSERT INTO admin(fullname, email, Password, UserName) VALUES(:fullname, :email, :password, :username)";
            $query = $dbh->prepare($sql);

            $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->bindParam(':username', $username, PDO::PARAM_STR);

            if ($query->execute()) {
                echo json_encode(["status" => "success", "message" => "New admin has been added successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error occurred while adding admin."]);
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
    <script type="text/javascript">
        function valid() {
            if (document.addemp.password.value != document.addemp.confirmpassword.value) {
                alert("New Password and Confirm Password Field do not match!!");
                document.addemp.confirmpassword.focus();
                return false;
            }
            return true;
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
                        $page='manage-admin';
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
                            <?php include '../includes/admin-notification.php' ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Add Admin Section</h4>
                            <ul class="breadcrumbs pull-left"> 
                                <li><a href="manage-admin.php">Manage Admin</a></li>
                                <li><span>Add</span></li>
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
                                <div id="response-message"></div>
                                <div class="card">
                                    <form name="addemp" id="addAdminForm" method="POST">
                                        <div class="card-body">
                                            <p class="text-muted font-14 mb-4">Please fill up the form in order to add a new system administrator</p>
                                            <div class="form-group">
                                                <label for="fullname" class="col-form-label">Full Name</label>
                                                <input class="form-control" name="fullname" id="fullname" type="text" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="col-form-label">Email ID</label>
                                                <input class="form-control" name="email" id="email" type="email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="username" class="col-form-label">Username</label>
                                                <input class="form-control" name="username" id="username" type="text" required>
                                            </div>
                                            <h4>Setting Passwords</h4>
                                            <div class="form-group">
                                                <label for="password" class="col-form-label">Password</label>
                                                <input class="form-control" name="password" id="password" type="password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirmpassword" class="col-form-label">Confirmation Password</label>
                                                <input class="form-control" name="confirmpassword" id="confirmpassword" type="password" required>
                                            </div>
                                            <button class="btn btn-primary" type="submit" onclick="return valid();">PROCEED</button>
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
    
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.jqueryui.min.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script>
        $(document).ready(function () {
            $("#addAdminForm").on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "add-admin.php",
                    data: $(this).serialize() + "&action=addAdmin",
                    dataType: "json",
                    success: function (response) {
                        if (response.status == "success") {
                            $("#response-message").html(`<div class="alert alert-success">${response.message}</div>`);
                            $("#addAdminForm")[0].reset();
                        } else {
                            $("#response-message").html(`<div class="alert alert-danger">${response.message}</div>`);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>


