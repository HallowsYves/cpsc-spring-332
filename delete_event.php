<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$db = mysqli_connect($servername, $username, $password);
$db_selected = mysqli_select_db($db, 'Event_DB');

function redirect_to($location)
{
    header("Location: " . $location);
    exit;
}

session_start();

if (isset($_SESSION['User_email']) && isset($_GET['event_id'])) {
    //retrieve event from given event_id

    //$sql = 

    if ($_SESSION['User_email'] != $_GET['managing_user_email']) {
        exit('Session ID does not match GET');
    }
} else {
    exit('improper access to server files');
}
