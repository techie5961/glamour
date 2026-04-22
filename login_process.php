<?php

session_start();
$email=$_POST['email'];
$password=$_POST['password'];

$users=json_decode(file_get_contents('users.json'),true);

// Check if user exists
$user_found = false;
$user_data = null;

foreach($users as $data){
	if(($data['email'] ?? '') == $email){
		$user_found = true;
		$user_data = $data;
		break;
	}
}

if(!$user_found){
	$_SESSION['notify']=[
	'status' => 'error',
	'title' => 'Login failed',
	'message' => 'Email not found on our server'
	];
	return header('Location: '.$_SERVER['HTTP_REFERER'].'');
}

// Verify password
if(!password_verify($password, $user_data['password'])){
	$_SESSION['notify']=[
	'status' => 'error',
	'title' => 'Login failed',
	'message' => 'Incorrect password'
	];
	return header('Location: '.$_SERVER['HTTP_REFERER'].'');
}

// Login successful - store user session
$_SESSION['user_email'] = $user_data['email'];
$_SESSION['user_first_name'] = $user_data['first_name'];
$_SESSION['user_last_name'] = $user_data['last_name'];
$_SESSION['user_phone'] = $user_data['phone'];
$_SESSION['user_balance'] = $user_data['balance'];
$_SESSION['user_upgraded'] = $user_data['upgraded'];


return header('Location: dashboard.php');

?>