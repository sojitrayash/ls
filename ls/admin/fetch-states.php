<?php
include('../includes/dbconn.php');

if (isset($_POST['countryId'])) {
    $countryId = intval($_POST['countryId']);
    $sql = "SELECT id, State FROM tbstate WHERE CountryId = :countryId";
    $query = $dbh->prepare($sql);
    $query->bindParam(':countryId', $countryId, PDO::PARAM_INT);
    $query->execute();
    $states = $query->fetchAll(PDO::FETCH_OBJ);

    foreach ($states as $state) {
        echo '<option value="' . htmlentities($state->id) . '">' . htmlentities($state->State) . '</option>';
    }
}
?>