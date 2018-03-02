<?php

$dbName = "yeticave";
$dbLogin = "root";
$dbPassword="";
$dbHost = "127.0.0.1";

$dbLink = mysqli_connect($dbHost, $dbLogin, $dbPassword, $dbName);

if ($dbLink === false) {
  
  $errorMessageSql = mysqli_connect_error();
  $errorMessageSubject = "Извините, ошибка подключения к базе данных. ";
  $errorMessageFull = $errorMessageSubject . $errorMessageSql;

  $templateData = compact("errorMessageFull");

  $errorDBPage = getContent("phpTemplates/errorDBTemplate.php",$templateData);

  print($errorDBPage);

  exit;

}

return $dbLink;

?>

