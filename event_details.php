<?php //Find all the venues in the system and display them in a dropdown.
//Find all the universities and display them in the system.

$servername = "localhost";
$username = "root";
$password = "";

$db = mysqli_connect($servername, $username, $password);
$db_selected = mysqli_select_db($db, 'Event_DB');

session_start();

if (!isset($_SESSION['User_email'])) {
    exit('Invalid session. Log in, or sign up for an account.');
} elseif (!isset($_GET['event_id'])) {
    exit('Invalid access based upon GET');
}

$sql = "SELECT * FROM _Event WHERE event_id =" . mysqli_real_escape_string($db, $_GET['event_id']);

$query = mysqli_query($db, $sql);
$event = mysqli_fetch_assoc($query);

list($event_start_date, $event_start_time) = explode(' ', $event['Start_date_time']);

$event_start_date = strtotime($event_start_date); // Convert date string to Unix timestamp
$event_start_time = strtotime($event_start_time); // Convert time string to Unix timestamp

$event_start_date = date("F jS, Y", $event_start_date);
$event_start_time = date("h:i A", $event_start_time);

list($event_end_date, $event_end_time) = explode(' ', $event['End_date_time']);

$event_end_date = strtotime($event_end_date); // Convert date string to Unix timestamp
$event_end_time = strtotime($event_end_time); // Convert time string to Unix timestamp

$event_end_date = date("F jS, Y", $event_end_date);
$event_end_time = date("h:i A", $event_end_time);

if (isset($event['User_email'])) {
    //echo "{$event['User_email']}";
    $sql = "SELECT First_name,Last_name FROM _User WHERE User_email ='" . mysqli_real_escape_string($db, $event['User_email']) . "'";
    $query = mysqli_query($db, $sql);
    $user_info = mysqli_fetch_assoc($query);
}
$sql = "SELECT University_name FROM University WHERE University_id ='" . mysqli_real_escape_string($db, $event['University_id']) . "'";
$query = mysqli_query($db, $sql);
$University_name = mysqli_fetch_assoc($query);

$sql = "SELECT Venue_name,Street_address,City,State,Zip FROM Venue WHERE Street_address ='" . mysqli_real_escape_string($db, $event['Street_address']) . "'
        AND City = '" . mysqli_real_escape_string($db, $event['City']) . "'
        AND State = '" . mysqli_real_escape_string($db, $event['State']) . "'
        AND Zip = '" . mysqli_real_escape_string($db, $event['Zip']) . "'";
$query = mysqli_query($db, $sql);
$Venue = mysqli_fetch_assoc($query);

$sql = 'SELECT User_email FROM Enrolled_in WHERE Event_id =' . mysqli_real_escape_string($db, $_GET['event_id']);
$user_query = mysqli_query($db, $sql);

$sql = 'SELECT User_email FROM _Event WHERE Event_id =' . mysqli_real_escape_string($db, $_GET['event_id']);
$query = mysqli_query($db, $sql);
if ($query) {
    if (mysqli_num_rows($query) != 1) {
        exit('query to retrieve managing user email was malformed.');
    }
} else {
    exit('query to find managing user email failed.');
}
$managing_user_email = mysqli_fetch_assoc($query);

$sql = 'SELECT * FROM _User WHERE User_email="' . mysqli_real_escape_string($db, $managing_user_email['User_email']) . '"';
$query = mysqli_query($db, $sql);

if (!$query) {
    exit('Could not retrieve managing user');
}

$managing_user = mysqli_fetch_assoc($query);

$logged_in_user_email = $_SESSION['User_email'];

$sql = 'SELECT Event_published FROM _Event WHERE Event_id=' . mysqli_real_escape_string($db, $_GET['event_id']);
$query = mysqli_query($db, $sql);
$is_event_published = mysqli_fetch_assoc($query);
$is_event_published = $is_event_published['Event_published'];

$logged_in_user_enrolled = 0;
while ($user = mysqli_fetch_assoc($user_query)) {
    if ($user['User_email'] === $logged_in_user_email) {
        $logged_in_user_enrolled = 1;
    }
}
mysqli_data_seek($user_query, 0);

//Display:
//Event Name
//Event Description
//First and last name of managing user
//State Date and time
//End date and time
//Venue
//University
//Event Type
//Presenter abstract deadline
?>
<h1><?php echo $event['Event_name']; ?></h1>
<p><?php echo $event['Event_description']; ?></p>
<h2>Managing User:</h2>
<p><?php echo $user_info['First_name']; ?> <?php echo $user_info['Last_name']; ?></p>
<h2>Event Starts at:<h2>
        <p><?php echo $event_start_date ?>, <?php echo $event_start_time ?></p>
        <h2> Event Ends at: </h2>
        <p><?php echo $event_end_date ?>, <?php echo $event_end_time ?></p>
        <h2>Venue:</h2>
        <p><?php echo $Venue['Venue_name'] ?> - <?php echo $Venue['Street_address'] ?>, <?php echo $Venue['City'] ?>,<?php echo $Venue['State'] ?> <?php echo $Venue['Zip'] ?></p>
        <h2>University:</h2>
        <p><?php echo $University_name['University_name'] ?></p>
        <h2>Event Type:</h2>
        <p><?php echo $event['Event_type'] ?></p>
        <a href="home.php"> Homepage.</a>
        <?php if ($logged_in_user_email === $managing_user_email['User_email']) { ?>
            <a href="delete_event.php?event_id=<?php echo $_GET['event_id'] ?>"><button class="btn">Delete Event</button></a>

            <a href="toggle_publish_event.php?is_event_published=<?php echo $is_event_published; ?>&event_id=<?php echo $_GET['event_id'] ?>"><button class="btn"><?php if ($is_event_published) { ?>Unpublish Event <?php } else { ?>Publish Event <?php } ?></button></a>

        <?php } else { ?>
            <a href="toggle_register_for_event.php?User_email=<?php echo $logged_in_user_email; ?>"><?php if ($logged_in_user_enrolled) { ?>Unregister <?php } else { ?>Register <?php } ?></a>
        <?php } ?>

        <h2> Users who are signed up for this event: </h2>
        <p> Emails: </p>
        <?php //Below code creates a new anchor item for every user signed up for the event.
        ?>
        <?php while ($row = mysqli_fetch_assoc($user_query)) { ?>

            <a href=" show_user.php?email=<?php echo $row['User_email']; ?>"><?php echo $row['User_email'] ?> </a>
        <?php } ?>