<?php

require('classes.php');

$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "connect_app_db";

$db_op = new DBproc();
$db_connect = $db_op::connectDB($servername, $username, $db_password, $dbname);

$email = 'test4@test.com';

$login = 'Test4';

$password = '12345';


//$db_op::insertUser($email, $login, $password, $db_connect);

//$auth = new Auth($email, $login, $password, $db_connect);

//$user = $auth::authUser();

//$user = $auth::logOut();

/*
if(isset($user))
{
  echo "<br />You are logged.";
}
else
{
  echo "<br />You were dislogged.";
} */
