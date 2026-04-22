<?php
$json=[
'tag' => 'master',
'password' => password_hash('Blaady05')
];
$file=file_put_contents('admins.json',json_encode($json));
if($file){
	echo 'File inserted success';
}