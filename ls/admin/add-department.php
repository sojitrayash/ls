<?php
session_start();
error_reporting(0);
include('../includes/dbconn.php');

if(strlen($_SESSION['alogin'])==0){   
    header('location:index.php');
} else {
    if(isset($_POST['departmentname']) && isset($_POST['departmentshortname']) && isset($_POST['deptcode'])) {
        $deptname = $_POST['departmentname'];
        $deptshortname = $_POST['departmentshortname'];
        $deptcode = $_POST['deptcode'];   
        
        $sql = "INSERT INTO tbldepartments(DepartmentName, DepartmentCode, DepartmentShortName) VALUES(:deptname, :deptcode, :deptshortname)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':deptname', $deptname, PDO::PARAM_STR);
        $query->bindParam(':deptcode', $deptcode, PDO::PARAM_STR);
        $query->bindParam(':deptshortname', $deptshortname, PDO::PARAM_STR);
        
        if($query->execute()) {
            echo "Department Created Successfully";
        } else {
            echo "Something went wrong. Please try again";
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
                        $page='department';
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
                        <ul ```html
                        class="notification-area pull-right">
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
                            <h4 class="page-title pull-left">Department Section</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="department.php">Department</a></li>
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
                    <div class="col-12 mt-5">
                        <div id="message" class="alert" style="display:none;"></div>
                        <div class="card">

                            <form id="addDepartmentForm">
                                <div class="card-body">
                                    <p class="text-muted font-14 mb-4">Please fill up the form in order to add new department</p>

                                    <div class="form-group">
                                        <label for="departmentname" class="col-form-label">Department Name</label>
                                        <input class="form-control" name="departmentname" type="text" required id="departmentname">
                                    </div>

                                    <div class="form-group">
                                        <label for="departmentshortname" class="col-form-label">Shortform</label>
                                        <input class="form-control" name="departmentshortname" type="text" autocomplete="off" required id="departmentshortname">
                                    </div>

                                    <div class="form-group">
                                        <label for="deptcode" class="col-form-label">Code</label>
                                        <input class="form-control" name="deptcode" type="text" autocomplete="off" required id="deptcode">
                                    </div>

                                    <button class="btn btn-primary" id="add" type="submit">ADD</button>
                                </div>
                            </form>
                        </div> 
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
        $('#addDepartmentForm').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                url: 'add-department.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#message').show().removeClass('alert-success alert-danger');
                    if(response.includes("Department Created Successfully")) {
                        $('#message').addClass('alert-success').text('Department added successfully!');
                        $('#addDepartmentForm')[0].reset(); 
                    } else {
                        $('#message').addClass('alert-danger').text('Error: ' + response);
                    }
                },
                error: function() {
                    $('#message').show().addClass('alert-danger').text('Error adding department!');
                }
            });
        });
    });
    </script>
</body>
</html>