<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_email'])){
    header('Location: login.php');
    exit();
}

// Get platform from GET parameter
$platform = isset($_GET['platform']) ? $_GET['platform'] : '';

if(empty($platform)){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Error',
        'message' => 'Invalid request.'
    ];
    header('Location: upgrade.php');
    exit();
}

$upgrade_fee_ngn = 14000;
$upgrade_fee_eur = round(14000 / 1800, 2);

// Load existing transactions
$transactions = [];
if(file_exists('transactions.json')){
    $transactions = json_decode(file_get_contents('transactions.json'), true);
    if(!is_array($transactions)){
        $transactions = [];
    }
}

// Create new transaction
$new_transaction = [
    'id' => uniqid('txn_'),
    'user_email' => $_SESSION['user_email'],
    'user_name' => $_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name'],
    'amount' => $upgrade_fee_ngn,
    'platform' => $platform,
    'status' => 'pending',
    'created_at' => date('Y-m-d H:i:s')
];

$transactions[] = $new_transaction;
file_put_contents('transactions.json', json_encode($transactions, JSON_PRETTY_PRINT));

// Set notification
$_SESSION['notify'] = [
    'status' => 'success',
    'title' => 'Request Submitted!',
    'message' => 'Your upgrade request has been submitted. Send payment and wait for admin approval.'
];

// Redirect to appropriate chat link
$message = "Hello, I have requested a premium upgrade.%0A"
    . "Email: " . urlencode($_SESSION['user_email']) . "%0A"
    . "Transaction ID: " . $new_transaction['id'] . "%0A"
    . "Amount: ₦" . number_format($upgrade_fee_ngn, 2);

if($platform == 'whatsapp'){
    $chat_link = 'https://wa.me/234XXXXXXXXXX?text=' . $message;
    header('Location: ' . $chat_link);
} elseif($platform == 'telegram'){
    $chat_link = 'https://t.me/YourTelegramUsername?text=' . $message;
    header('Location: ' . $chat_link);
} else {
    header('Location: upgrade.php');
}
exit();
?>