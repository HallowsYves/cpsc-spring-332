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

if (isset($_GET['is_event_published']) && isset($_GET['event_id']) && isset($_SESSION['User_email'])) {
    //UPDATE THE EVENT THAT CORRESPONDS TO THE EVENT ID

    $sql = 'SELECT User_email FROM _Event WHERE Event_id=' . mysqli_real_escape_string($db, $_GET['event_id']);
    $query = mysqli_query($db, $sql);
    $User_email = mysqli_fetch_assoc($query);
    $User_email = $User_email['User_email'];

    if ($User_email != $_SESSION['User_email']) {
        exit('Session ID not matching event id');
    }


    if ($_GET['is_event_published'] === '0') {
        $published = 1;
    } else {
        $published = 0;
    }
    $sql = 'UPDATE _Event SET Event_published =' . mysqli_real_escape_string($db, $published) . ' WHERE Event_id=' . mysqli_real_escape_string($db, $_GET['event_id']);
    $query = mysqli_query($db, $sql);
    if (!$query) {
        echo "query for event publish toggle update failed.";
    }

    redirect_to("event_details.php?event_id=" . $_GET['event_id']);
} else {
    exit('Improper access to server files.');
}
