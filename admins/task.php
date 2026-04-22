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
if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['post_task'])){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Invalid Request',
        'message' => 'Please submit the form properly'
    ];
    header('Location: dashboard.php');
    exit();
}

// Get form data (no validation)
$task_title = $_POST['task_title'] ?? '';
$task_description = $_POST['task_description'] ?? '';
$task_reward = $_POST['task_reward'] ?? 0;
$task_link = $_POST['task_link'] ?? '';
$task_deadline = $_POST['task_deadline'] ?? '';

// Load existing tasks
$tasks_file = '../tasks.json';
$tasks = [];

if(file_exists($tasks_file)){
    $tasks = json_decode(file_get_contents($tasks_file), true);
    if(!is_array($tasks)){
        $tasks = [];
    }
}

// Create new task
$new_task = [
    'id' => uniqid(),
    'title' => $task_title,
    'description' => $task_description,
    'reward' => $task_reward,
    'link' => $task_link,
    'deadline' => $task_deadline,
    'created_at' => date('Y-m-d H:i:s'),
    'status' => 'active'
];

// Add to beginning of array
array_unshift($tasks, $new_task);

// Save to file
if(file_put_contents($tasks_file, json_encode($tasks, JSON_PRETTY_PRINT))){
    $_SESSION['notify'] = [
        'status' => 'success',
        'title' => 'Task Posted',
        'message' => 'New task has been published successfully'
    ];
} else {
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Post Failed',
        'message' => 'Unable to post task. Please check file permissions.'
    ];
}

// Redirect back to dashboard
header('Location: dashboard.php');
exit();
?>