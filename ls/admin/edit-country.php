<?php
session_start();
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
include('../includes/dbconn.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['country']) && isset($_POST['description'])) {
    $id = intval($_GET['id']);  
    $country = $_POST['country']; 
    $description = $_POST['description'];  

    $sql = "UPDATE tbcountry SET Country = :country, Description = :description WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':country', $country, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':id', $id, PDO::PARAM_INT); 

    try {
        $query->execute();

        if ($query->rowCount() > 0) {
            echo "Country updated successfully";
        } else {
            echo "No changes were made. Please check the data.";
        }
    } catch (PDOException $e) {
        echo "Error updating Country: " . $e->getMessage();
    }
    exit; 
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
                        $page='country';
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
                            <li id="full-view-exit"><i class="ti-zoom-out"></i></li> <?php include '../includes/admin-notification.php'; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Country Section</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="country.php">country</a></li>
                                <li><span>Edit</span></li>
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
                            <form method="POST" id="updatecountry">
                                <div class="card-body">
                                    <p class="text-muted font-14 mb-4">Please make changes on the form below in order to update country</p>
                                    <?php
                                        $id = intval($_GET['id']);
                                        $sql = "SELECT * FROM tbcountry WHERE id=:id";
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':id', $id, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                                        if($query->rowCount() > 0) {
                                            foreach($results as $result) { ?> 
                                                <div class="form-group">
                                                    <label for="example-text-input" class="col-form-label">Country</label>
                                                    <input class="form-control" name="country" type="text" required id="example-text-input" value="<?php echo htmlentities($result->Country); ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="example-text-input" class="col-form-label">Short Description</label>
                                                    <input class="form-control" name="description" type="text" autocomplete="off" required id="example-text-input" value="<?php echo htmlentities($result->Description); ?>" required>
                                                </div>
                                            <?php }
                                        } ?>

                                        <button class="btn btn-primary" name="update" id="update" type="button" onclick="updateLeaveType()">MAKE CHANGES</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php include '../includes/footer.php'; ?>
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
        function updatecountry() {
            var formData = $("#updatecountry").serialize();
            $.ajax({
                type: "POST",
                url: "edit-country.php?id=<?php echo $id; ?>", 
                data: formData,
                success: function(response) {
                    $('#message').show().removeClass('alert-success alert-danger');
                    if(response.includes("Leave type updated successfully")) {
                        $('#message').addClass('alert-success').text('Country updated successfully!');
                    } else {
                        $('#message').addClass('alert-danger').text('Error: ' + response);
                    }
                },
                error: function() {
                    $('#message').show().addClass('alert-danger').text('Error updating leave type!');
                }
            });
        }


        
    </script>
</body>
</html>