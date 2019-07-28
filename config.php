<?php

require "environment.php";

$config = array();

if (ENVIRONMENT == "development") {
	$config['dbtype'] = "pgsql";
	$config['dbname'] = "backend";
	$config['dbhost'] = "localhost";
	$config['dbuser'] = "caio";
	$config['dbpass'] = "";
}
else {
	$config['dbtype'] = "";
	$config['dbname'] = "";
	$config['dbhost'] = "";
	$config['dbuser'] = "";
	$config['dbpass'] = "";
}

try {
	global $pdo;
	$pdo = new PDO("{$config['dbtype']}:dbname={$config['dbname']};host={$config['dbhost']}", $config['dbuser'], $config['dbpass']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Falhou: " . $e->getMessage();
}