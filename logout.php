<?php
session_start();

// Store any notification messages before destroying session
$message = null;
$message_type = null;

// Check if there's a custom logout message
if(isset($_SESSION['notify'])){
    $message = $_SESSION['notify']['message'];
    $message_type = $_SESSION['notify']['status'];
}

// Get user type before logout (for logging or redirect purposes)
$user_type = 'user';
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true){
    $user_type = 'admin';
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start a new session for the notification (if any)
session_start();

// Set logout success notification
if($user_type == 'admin'){
    $_SESSION['notify'] = [
        'status' => 'success',
        'title' => 'Logged Out',
        'message' => 'You have been successfully logged out from Admin Panel.'
    ];
    // Redirect to admin login
    header('Location: admins/login.php');
    exit();
} else {
    $_SESSION['notify'] = [
        'status' => 'success',
        'title' => 'Logged Out',
        'message' => 'You have been successfully logged out. Come back soon!'
    ];
    // Redirect to user login
    header('Location: login.php');
    exit();
}
?>