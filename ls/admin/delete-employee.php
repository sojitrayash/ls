<?php
include('../includes/dbconn.php');
if(isset($_POST['id'])){
    $id = $_POST['id'];
    $sql = "DELETE FROM tblemployees WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_STR);
    
    if($query->execute()){
        echo 'success'; 
    } else {
        echo 'failure'; 
    }
}
?>
