<?php
//session_start();

// Check if the user is authenticated with Google
if (!isset($_SESSION['google_access_token'])) {
    header('Location: login.php');
    exit;
}

$googleUserName = $_SESSION['google_user_name'];
$googleUserEmail = $_SESSION['google_user_email'];
$googleUserPhoto = $_SESSION['google_user_photo'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>User Profile</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport' />
    <style>
        /* Define styles for the header */
        .header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: right;
        }

        /* Style the profile picture */
        .profile-picture {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        /* Style the user info container */
        .user-info {

            display: flex;
            align-items: center;
            flex-direction: column;
            flex-wrap: nowrap;
            align-content: flex-end;
            justify-content: space-evenly;
        }


        /* Adjust text size and margin for user info */
        .user-info p {
            margin: 0;
            font-size: 16px;

        }

        /* Style the logout button */
        .logout-button {
            background-color: #fff;
            color: #333;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

    <!-- Header -->
    <div class="header">
        <div class="user-info">
            <img class="profile-picture" src="<?php echo $googleUserPhoto; ?>" alt="User Photo">
            <p><?php echo $googleUserName; ?></p>
            <p><?php echo $googleUserEmail; ?></p>
        </div>
        <button class="logout-button" onclick="location.href = 'logout.php'">Logout</button>
    </div>