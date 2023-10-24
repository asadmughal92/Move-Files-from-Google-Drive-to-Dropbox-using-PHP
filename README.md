# Google Drive to Dropbox File Transfer

This PHP-based project allows users to authenticate with their Google Drive account and transfer files from Google Drive to Dropbox. It utilizes the Google Drive API and Dropbox API for the integration.

## Features

- User authentication using Google Account.
- Transfer files from Google Drive to Dropbox.
- Displays user name and email after login.
- Secure authentication and token management.

## Prerequisites

Before running the project, you need to set up the following:

- **Google API Credentials**: You must obtain a client ID and client secret from the Google API Console.

- **Dropbox API Credentials**: You need to register your app on the Dropbox Developer Dashboard to get an app key and secret.

- **Web Server**: You should have a web server (e.g., Apache or Nginx) with PHP support set up on your machine.

## Installation

1. Clone this repository or download the source code to your local machine.

2. Install Composer: If you don't have Composer installed, download it from [https://getcomposer.org/download/](https://getcomposer.org/download/) and follow the installation instructions.

3. Run `composer install` to install the required dependencies.

4. Set up your API credentials: Replace the placeholders in `config.php` with your Google Drive and Dropbox API credentials.

5. Start your web server and visit `index.php` in your web browser to begin the authentication process.

6. After logging in with your Google Account, you'll be redirected to the Dropbox connection page. You can start transferring files from your Google Drive to Dropbox.

## Customization

- You can customize the appearance and layout of the pages by modifying the HTML and CSS files.

- The transfer logic can be customized as needed, such as handling specific file types, renaming files, or applying filters.

## Dependencies

- [Google API Client Library](https://developers.google.com/api-client-library)
- [Kunnu Dropbox PHP SDK](https://github.com/kunalvarma05/dropbox-php-sdk)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Special thanks to the developers of the Google Drive and Dropbox APIs.

---

Feel free to customize the README according to your specific project details, and don't forget to provide relevant documentation and instructions for users to use and configure your application.
