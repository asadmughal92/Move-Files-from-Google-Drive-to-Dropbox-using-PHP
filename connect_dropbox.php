<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';

// Check if the user is already authenticated with Google
if (!isset($_SESSION['google_access_token'])) {
    header('Location: index.php');
    exit;
}

// If Dropbox access token already exists, redirect to the Transfer File page
if (isset($_SESSION['dropbox_access_token'])) {
    header('Location: transfer_files.php');
    exit;
}

// If the OAuth callback contains the "code" parameter, it means the user has authorized your app with Dropbox
if (isset($_GET['code'])) {
   
    // Replace with your Dropbox app's redirect URI
    $authorizationCode = $_GET['code'];

    $url = 'https://api.dropbox.com/oauth2/token';

    $data = array(
        'code' => $authorizationCode,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $dropboxRedirectUri
    );

    $query_string = http_build_query($data);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, $dropboxClientId . ":" . $dropboxClientSecret);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    $response = json_decode($response, true);

    if (isset($response['access_token'])) {
        // Store the obtained Dropbox access token in the session
        $_SESSION['dropbox_access_token'] = $response['access_token'];
        header('Location: transfer_files.php'); // Redirect to the Transfer File page
        exit;
    }
}

    // If the user is not already connected to Dropbox, provide a link to connect
    $dropboxAuthUrl = 'https://www.dropbox.com/oauth2/authorize' .
        '?response_type=code&client_id=' . $dropboxClientId .
        '&redirect_uri=' . $dropboxRedirectUri;
?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Connect to Dropbox</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport' />
</head>

<body>
    <?php include_once('header.php'); ?>
    <div class="container">
        <br />
        <h2 align="center">Connect to Dropbox</h2>
        <br />
        <div class="panel panel-default">
            <?php
            echo '<div align="center"><a href="' . $dropboxAuthUrl . '">Connect to Dropbox</a></div>';
            ?>
        </div>
    </div>
</body>

</html>