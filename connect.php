<?php

  

  $db_hostname = "localhost";
	$db_username = "chusli";
	$db_password = "chusli";
	$db_database = "codechef";

	$hash = "1312psriancghhihcmhuoa";      //Gibberish, Random Text

	$hash_and_salt = '$2a$09$'.$hash.'$';

	$link = mysqli_connect($db_hostname, $db_username, $db_password, $db_database);

	if(mysqli_connect_error())
	{
		die("Cannot Connect to Database Server");
	}

?>
