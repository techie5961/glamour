<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_email'])){
    header('Location: login.php');
    exit();
}

// Check if form was submitted
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Invalid Request',
        'message' => 'Invalid request method.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Get task data
$task_id = isset($_POST['task_id']) ? $_POST['task_id'] : '';
$reward = isset($_POST['reward']) ? floatval($_POST['reward']) : 0;

if(empty($task_id) || $reward <= 0){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Invalid Task',
        'message' => 'Invalid task information.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Load users data
$users_file = 'users.json';
$users = [];
if(file_exists($users_file)){
    $users = json_decode(file_get_contents($users_file), true);
    if(!is_array($users)) $users = [];
}

// Find current user
$user_email = $_SESSION['user_email'];
$user_key = null;
$current_user = null;

foreach($users as $key => $user){
    if($user['email'] == $user_email){
        $user_key = $key;
        $current_user = $user;
        break;
    }
}

if($user_key === null){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'User Not Found',
        'message' => 'User account not found.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Load tasks performed tracking
$tasks_performed_file = 'tasks_performed.json';
$tasks_performed = [];
if(file_exists($tasks_performed_file)){
    $tasks_performed = json_decode(file_get_contents($tasks_performed_file), true);
    if(!is_array($tasks_performed)) $tasks_performed = [];
}

// Check if task already completed by this user
foreach($tasks_performed as $performed){
    if($performed['user_email'] == $user_email && $performed['task_id'] == $task_id){
        $_SESSION['notify'] = [
            'status' => 'error',
            'title' => 'Task Already Completed',
            'message' => 'You have already completed this task.'
        ];
        header('Location: dashboard.php');
        exit();
    }
}

// Load tasks to verify task exists and is active
$tasks_file = 'tasks.json';
$tasks = [];
$task_exists = false;
$task_reward = 0;

if(file_exists($tasks_file)){
    $tasks = json_decode(file_get_contents($tasks_file), true);
    if(!is_array($tasks)) $tasks = [];
    
    foreach($tasks as $task){
        if($task['id'] == $task_id && $task['status'] == 'active'){
            $task_exists = true;
            $task_reward = $task['reward'];
            break;
        }
    }
}

if(!$task_exists){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Task Not Found',
        'message' => 'Task no longer available or has expired.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Verify reward matches
if($reward != $task_reward){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Reward Mismatch',
        'message' => 'Task reward information mismatch.'
    ];
    header('Location: dashboard.php');
    exit();
}

// Conversion rate: 1 EUR = 1800 NGN
define('EUR_TO_NGN', 1800);

// Convert reward from NGN to EUR for balance storage
$reward_eur = $reward / EUR_TO_NGN;

// Add reward to user balance
$users[$user_key]['balance'] = ($current_user['balance'] ?? 0) + $reward_eur;

// Save updated users
file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));

// Record task as performed
$task_record = [
    'id' => uniqid('performed_'),
    'user_email' => $user_email,
    'task_id' => $task_id,
    'reward' => $reward,
    'completed_at' => date('Y-m-d H:i:s'),
    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
];

$tasks_performed[] = $task_record;
file_put_contents($tasks_performed_file, json_encode($tasks_performed, JSON_PRETTY_PRINT));

// Update session balance
$_SESSION['user_balance'] = $users[$user_key]['balance'];

// Set success notification
$_SESSION['notify'] = [
    'status' => 'success',
    'title' => 'Reward Earned! 🎉',
    'message' => 'You earned ₦' . number_format($reward, 2) . ' for completing this task!'
];

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
?>