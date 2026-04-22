<?php

session_start();
$first_name=$_POST['first_name'];
$last_name=$_POST['last_name'];
$email=$_POST['email'];
$phone=$_POST['phone'];
$password=$_POST['password'];
$confirm_password=$_POST['confirm_password'];

$users=json_decode(file_get_contents('users.json'),true);

// ✅ FIXED: Compare plain text passwords directly
if($password !== $confirm_password){
	$_SESSION['notify']=[
	'status' => 'error',
	'title' => 'Invalid password',
	'message' => 'Password and confirm password must match',
	
	];
	return header('Location: '.$_SERVER['HTTP_REFERER'].'');
}

foreach($users as $data){
	if(($data['email'] ?? '') == $email){
	$_SESSION['notify']=[
	'status' => 'error',
	'title' => 'Email already exists',
	'message' => 'Email already exists on our server'
	];
	return header('Location: '.$_SERVER['HTTP_REFERER'].'');

	}
}


foreach($users as $data){
	if(($data['phone'] ?? '') == $phone){
	$_SESSION['notify']=[
	'status' => 'error',
	'title' => 'Phone number already exists',
	'message' => ' Phone number already exists on our server'
	];
	return header('Location: '.$_SERVER['HTTP_REFERER'].'');

	}
}


$users[$email]=[
'first_name' => $first_name,
'last_name' => $last_name,
'email' => $email,
'phone' => $phone,
'password' => password_hash($password,PASSWORD_DEFAULT),
'balance' => 7,
'upgraded' => 'no'

];

file_put_contents('users.json',json_encode($users));
	$_SESSION['notify']=[
	'status' => 'success',
	'title' => 'Registration successful',
	'message' => 'Registration successful,login to your account to continue'
	];
	
	return header('Location: login.php');

?>