<?php
require ($_SERVER['DOCUMENT_ROOT'] . "/wp-load.php");
global $wpdb;

    if ($wpdb->delete(
            $wpdb->prefix . "roast_db",
            array('id' => $_POST['roast_id'])
            )
        ) {
            echo json_encode(array("roast_id" => $_POST['roast_id'], "success" => true));
    }
?>
