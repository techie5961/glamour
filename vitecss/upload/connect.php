<?php
$host = "localhost";
$user = "blaady05";
$pass = "#David05.";
$dbname = "techie";

// Connect to the database
$conn = mysqli_connect($host, $user, $pass, $dbname);

if ($conn) {
    echo "SUCCESS";
} else {
    echo mysqli_connect_error();
}

// Grant privileges
$grant = "GRANT ALL PRIVILEGES ON techie.* TO 'blaady05'@'localhost' IDENTIFIED BY '#David05.'";
$granted = mysqli_query($conn, $grant);

if ($granted) {
    echo "Privileges granted successfully.";
} else {
    echo "Error granting privileges: " . mysqli_error($conn);
}

// Flush privileges
$flush = mysqli_query($conn, "FLUSH PRIVILEGES");

if ($flush) {
    echo "Privileges flushed successfully.";
} else {
    echo "Error flushing privileges: " . mysqli_error($conn);
}
?>
