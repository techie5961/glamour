<?php
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Access Denied',
        'message' => 'Please login as admin first'
    ];
    header('Location: index.html');
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['save_bank'])){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Invalid Request',
        'message' => 'Please submit the form properly'
    ];
    header('Location: dashboard.php');
    exit();
}

// Get form data (no validation)
$account_number = $_POST['account_number'] ?? '';
$bank_name = $_POST['bank_name'] ?? '';
$account_name = $_POST['account_name'] ?? '';

// Load existing settings
$settings_file = '../admin_settings.json';
$settings = [];

if(file_exists($settings_file)){
    $settings = json_decode(file_get_contents($settings_file), true);
    if(!is_array($settings)){
        $settings = [];
    }
}

// Update bank details
$settings['bank'] = [
    'account_number' => $account_number,
    'bank_name' => $bank_name,
    'account_name' => $account_name,
    'last_updated' => date('Y-m-d H:i:s')
];

// Save to file
if(file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT))){
    $_SESSION['notify'] = [
        'status' => 'success',
        'title' => 'Bank Details Saved',
        'message' => 'Bank account details have been updated successfully'
    ];
} else {
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Save Failed',
        'message' => 'Unable to save bank details. Please check file permissions.'
    ];
}

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
?>