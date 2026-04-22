<?php
session_start();
//$admin=file_get_contents('admins.json');
//$admin=json_decode($admin);
//$admin[0]->password=password_hash('Blaady05', PASSWORD_DEFAULT);

//file_put_contents('admins.json',json_encode($admin));
//exit;
$tag = $_POST['tag'] ?? '';
$password = $_POST['password'] ?? '';

// Load admins from JSON file
$admins = json_decode(file_get_contents('admins.json'), true);

// Check if admin exists by tag (email)
$admin_found = false;
$admin_data = null;

foreach($admins as $admin){
    if(($admin['tag'] ?? '') == $tag){
        $admin_found = true;
        $admin_data = $admin;
        break;
    }
}

if(!$admin_found){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Login failed',
        'message' => 'Admin account not found'
    ];
    return header('Location: ' . $_SERVER['HTTP_REFERER'] . '');
}

// Verify password
if(!password_verify($password, $admin_data['password'])){
    $_SESSION['notify'] = [
        'status' => 'error',
        'title' => 'Login failed',
        'message' => 'Incorrect password'
    ];
    return header('Location: ' . $_SERVER['HTTP_REFERER'] . '');
}

// Login successful - store admin session
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_tag'] = $admin_data['tag'];
$_SESSION['admin_name'] = $admin_data['name'] ?? '';
$_SESSION['admin_role'] = $admin_data['role'] ?? 'admin';
$_SESSION['admin_last_login'] = date('Y-m-d H:i:s');

// Optional: Update last login timestamp in admins.json
$admin_data['last_login'] = date('Y-m-d H:i:s');
foreach($admins as $key => $admin){
    if(($admin['tag'] ?? '') == $tag){
        $admins[$key]['last_login'] = date('Y-m-d H:i:s');
        break;
    }
}
file_put_contents('admins.json', json_encode($admins, JSON_PRETTY_PRINT));

$_SESSION['notify'] = [
    'status' => 'success',
    'title' => 'Welcome Admin',
    'message' => 'Successfully logged in to admin panel'
];

return header('Location: dashboard.php');
?>