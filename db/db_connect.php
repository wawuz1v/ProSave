<?php


/*подключение к базе данных*/


$host = 'localhost'; // в 90% случаев это менять не надо

$password = "ProSave2023";

$username = "u4142_prosave";

$databasename = "u4142_prosave";


global $db;


setlocale(LC_ALL, "ru_RU.UTF8");


$db = new mysqli($host, $username, $password, $databasename, 3306);
$db->set_charset('utf8mb4');

if ($db->connect_errno) {

    echo "Не удалось подключиться к MySQL: (" . $db->connect_errno . ") " . $db->connect_error;

    exit;

}