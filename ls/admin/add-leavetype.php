<?php
session_start();
error_reporting(E_ALL); 
include('../includes/dbconn.php');

if(strlen($_SESSION['alogin']) == 0){   
    header('location:index.php');
    exit;
} else {
    if(isset($_POST['add'])){
        try {
            
            $leavetype = $_POST['leavetype'];
            $description = $_POST['description'];

            $sql = "INSERT INTO tblleavetype(LeaveType, Description) VALUES(:leavetype, :description)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':leavetype', $leavetype, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);

            
            $query->execute();
            $lastInsertId = $dbh->lastInsertId();

            if($lastInsertId) {
                echo json_encode(['status' => 'success', 'message' => 'Leave type added successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
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
                        $page='leave';
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
                            <h4 class="page-title pull-left">Leave Section</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="leave-section.php">Manage Type</a></li>
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
                        <div class="alert" style="display: none;"></div>
                        <div class="card">
                            <form method="POST">
                                <div class="card-body">
                                    <p class="text-muted font-14 mb-4">Please fill up the form in order to add new leave type</p>

                                    <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Leave Type</label>
                                        <input class="form-control" name="leavetype" type="text" required id="example-text-input">
                                    </div>

                                    <div class="form-group">
                                        <label for="example-text-input" class="col-form-label">Short Description</label>
                                        <input class="form-control" name="description" type="text" autocomplete="off" required id="example-text-input">
                                    </div>

                                    <button class="btn btn-primary" name="add" id="add" type="submit">ADD</button>
                                </div>
                            </form>
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
    <script src="assets/js/line-chart.js"></script>
    <script src="assets/js/pie-chart.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script>
   
    $(document).ready(function(){
        $('#add').click(function(e){
            e.preventDefault();
            var leavetype = $('input[name="leavetype"]').val();
            var description = $('input[name="description"]').val();

            $.ajax({
                url: 'add-leavetype.php',
                type: 'POST',
                data: {
                    leavetype: leavetype,
                    description: description,
                    add: 1 
                },
                dataType: 'json', 
                success: function(response) {
                    if(response.status == 'success') {
                        $(".alert").removeClass("alert-danger").addClass("alert-success").text(response.message).show();
                        
                        $("input[name='leavetype']").val('');
                        $("input[name='description']").val('');
                    } else {
                        $(".alert").removeClass("alert-success").addClass("alert-danger").text(response.message).show();
                    }
                },
                error: function() {
                    $(".alert").removeClass("alert-success").addClass("alert-danger").text("An error occurred. Please try again.").show();
                }
            });
        });
    });


    </script>   
</body>

</html>

