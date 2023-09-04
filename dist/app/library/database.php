<?php
require_once 'secrets.php';

$myDbLink = mysqli_connect(SECRET_DB_SERVER, SECRET_DB_USER, SECRET_DB_PASS, SECRET_DB_NAME) or die('Database error: ' . mysqli_error($myDbLink));
