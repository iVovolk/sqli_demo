<?php

if (empty($_ENV['MYSQL_PASSWORD_FILE']) || empty($_ENV['MYSQL_DB_FILE']) || empty($_ENV['MYSQL_USER_FILE'])) {
    exit('Подключение к базе данных не настроено.');
}
if ((!is_file($_ENV['MYSQL_PASSWORD_FILE']) || !is_readable($_ENV['MYSQL_PASSWORD_FILE']))
    || (!is_file($_ENV['MYSQL_DB_FILE']) || !is_readable($_ENV['MYSQL_DB_FILE']))
    || (!is_file($_ENV['MYSQL_USER_FILE']) || !is_readable($_ENV['MYSQL_USER_FILE']))
) {
    exit('Подключение к базе данных не настроено.');
}
$pass = trim(file_get_contents($_ENV['MYSQL_PASSWORD_FILE']));
$db = trim(file_get_contents($_ENV['MYSQL_DB_FILE']));
$user = trim(file_get_contents($_ENV['MYSQL_USER_FILE']));
try {
    $mysqli = mysqli_init();
    $mysqli->real_connect("sqli-db", $user, $pass, $db);
} catch (\Throwable $e) {
    exit('Ошибка подключения к базе данных');
}

