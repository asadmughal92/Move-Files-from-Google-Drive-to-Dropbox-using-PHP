<?php
session_start();
require 'vendor/autoload.php';  // Include the Google and Dropbox API Client Libraries
require 'config.php';

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;


// Check if the user is authenticated with both Google and Dropbox
if (!isset($_SESSION['google_access_token']) || !isset($_SESSION['dropbox_access_token'])) {
    header('Location: login.php');
    exit;
}

$successCount = 0;
$errorCount = 0;

$google_client = new Google_Client();

// Initialize the Google client with your credentials
// (Make sure to add the necessary scopes for file/folder access)

$google_client->setClientId($driveClientId); // Replace with your Client ID
$google_client->setClientSecret($driveClientSecret); // Replace with your Client Secret
$google_client->setAccessToken($_SESSION['google_access_token']);
$google_client->addScope('https://www.googleapis.com/auth/drive');
$google_client->addScope('https://www.googleapis.com/auth/drive.file');


// Create a Dropbox app instance
$app = new DropboxApp($dropboxClientId, $dropboxClientSecret);

// Create a Dropbox client
$dropbox = new Dropbox($app, ['token' => $_SESSION['dropbox_access_token']]);

//Fetch Google Drive files and folders
$drive_service = new Google_Service_Drive($google_client);
$drive_files = [];
$drive_folders = [];



$files = $drive_service->files->listFiles([    
        'q' => "mimeType='image/jpeg'",
        'spaces' => 'drive',
        'fields' => 'nextPageToken, files(id, name)',
]);

//var_dump($files);
if (!empty($files->getFiles())) {
    foreach ($files->getFiles() as $file) {
        if ($file->getMimeType() === 'application/vnd.google-apps.folder') {
            $drive_folders[] = $file;
        } else {
            $drive_files[] = $file;
        }
    }
}

// Function to generate a custom filename based on the content type
function generateCustomFilename($contentType) {
    $parts = explode('/', $contentType);
    $extension = end($parts);
    $customFilename = 'myapp_' . uniqid() . '.' . $extension;
    return $customFilename;
}

// Handle file transfer to Dropbox
if (isset($_POST['transfer'])) {
    $selectedFileIds = $_POST['selected_files'];
   
    $accessToken=$_SESSION['dropbox_access_token'];
    $app = new DropboxApp($dropboxClientId, $dropboxClientSecret, $accessToken);
    $dropbox = new Dropbox($app);

    $google_client->setClientId($driveClientId);
    $google_client->setClientSecret($driveClientSecret);
    $google_client->setAccessToken($_SESSION['google_access_token']);
    $google_client->addScope('https://www.googleapis.com/auth/drive');
    $google_client->addScope('https://www.googleapis.com/auth/drive.file');

    // Initialize the Google Drive client (assuming you've already done this in your code)
    $drive_service = new Google_Service_Drive($google_client);

    foreach ($selectedFileIds as $fileId) {
        // Download the file from Google Drive
        $file = $drive_service->files->get($fileId, ['alt' => 'media']);
        $fileContents = $file->getBody()->getContents();

        $contentType = $file->getHeaderLine('Content-Type');

        // Create a custom filename based on the content type
        $filename = generateCustomFilename($contentType);

    
        // Get the filename from Google Drive
       // $googleDriveFileName = $file->getName();

        // Specify the destination folder for local storage
        $localFolderPath = 'asset/files/';
        
        // Save the file to the local folder
        $localFilePath = $localFolderPath . $filename;
        file_put_contents($localFilePath, $fileContents);

        // Specify the destination folder and file name in Dropbox using the Google Drive filename
        $dropboxFilePath = "/drive/" . $filename;

        // Upload the file to Dropbox
        try {
            $response = $dropbox->simpleUpload($localFilePath, $dropboxFilePath, ['autorename' => true]);
            // Uploaded File
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        
        // Check the response to ensure the upload was successful
        if ($response instanceof Kunnu\Dropbox\Models\FileMetadata && !empty($response->getId())) {
            // File upload was successful
            $successCount++;
        } else {
            // File upload failed
            $errorCount++;
           
        }
        
    }

    if ($successCount > 0) {
        echo '<script>alert("' . $successCount . ' files transferred to Dropbox successfully.");</script>';
    }
    
    if ($errorCount > 0) {
        echo '<script>alert("' . $errorCount . ' files failed to transfer to Dropbox.");</script>';
    }
}


// Display the Google Drive files and allow the user to select files for transfer
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Transfer Files to Dropbox</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport' />
</head>
<body>
<?php include_once('header.php'); ?>
    <div class="container">
        <br />
        <h2 align="center">Transfer Files to Dropbox</h2>
        <br />
        <div class="panel panel-default">
            <form method="post">
                <?php
                if (!empty($drive_files) || !empty($drive_folders)) {
                    foreach ($drive_folders as $folder) {
                        echo '<input type="checkbox" name="selected_files[]" value="' . $folder->getId() . '"> Folder: ' . $folder->getName() . '<br>';
                    }
                    foreach ($drive_files as $file) {
                        echo '<input type="checkbox" name="selected_files[]" value="' . $file->getId() . '"> File: ' . $file->getName() . '<br>';
                    }
                } else {
                    echo 'No files or folders found in Google Drive.';
                }
                ?>
                <input type="submit" name="transfer" value="Transfer Selected Files to Dropbox">
               
            </form>
        </div>
    </div>
</body>
</html>
