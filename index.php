<?php
session_start();
require 'vendor/autoload.php';  // Include the Google API Client Library
require 'config.php';

$google_client = new Google_Client();

$google_client->setClientId($driveClientId); // Replace with your Client ID
$google_client->setClientSecret($driveClientSecret); // Replace with your Client Secret
$google_client->setRedirectUri('http://localhost:90/test_dropbox/'); // Replace with your Redirect URI

$google_client->addScope('email');
$google_client->addScope('profile');
$google_client->addScope('https://www.googleapis.com/auth/drive');
$google_client->addScope('https://www.googleapis.com/auth/drive.file');

if (isset($_GET["code"])) {
    $token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);

    if (!isset($token["error"])) {
        $google_client->setAccessToken($token['access_token']);
        $_SESSION['google_access_token'] = $token['access_token'];

        // Get user information
        $oauth2 = new Google_Service_Oauth2($google_client);
        $user_info = $oauth2->userinfo->get();
        $_SESSION['google_user_name'] = $user_info->getName();
        $_SESSION['google_user_email'] = $user_info->getEmail();
        $_SESSION['google_user_photo'] = $user_info->getPicture();

        header('Location: connect_dropbox.php'); // Redirect to Dropbox connection page
    }
}

$login_button = '';

if (!isset($_SESSION['google_access_token'])) {
    $login_button = '<a href="' . $google_client->createAuthUrl() . '"><img src="asset/google.png" /></a>';
}

// Display the login page HTML
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Login with GoogleDrive</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport' />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .panel {
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="container">
        <br />
       
        <br />
        <div class="panel panel-default">
            <?php
            if (!empty($_SESSION['google_access_token'])) {
                // User is already authenticated, you can redirect or display a message here
                // Optionally, provide a link to the "Connect to Dropbox" page
                echo 'You are already logged in as ' . $_SESSION['google_user_name'] . '. <a href="connect_dropbox.php">Connect to Dropbox</a>';
            } else {
                // Display the login button
                echo '<div align="center">' . $login_button . '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
`