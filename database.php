<?php

$config = array(
	"DB_HOST" => "localhost",
	"DB_NAME" => "dbforblog",
	"DB_USER" => "root",
	"DB_PASS" => "password"
);

$db = new mysqli($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME']);

if($db->connect_error){
	die("Could not connect to the database!");
}