<?php
    error_reporting(0);
    mysqli_report(MYSQLI_REPORT_OFF);
    $config = require('config.php');
    
    if (isset($_GET['server'])) {
		$server = $_GET['server'];
	} else {
		$server = array_search(reset($config['servers']), $config['servers']);
	}

    $dbserver = $config['servers'][$server];
    $connection = mysqli_connect($dbserver['ipAddress'], $dbserver['username'], $dbserver['password'], $dbserver['databaseName']);
    if($connection->connect_errno) {
        header('Location: /error.php?error=noconnection');
        exit();
    }
    return $connection;
?>