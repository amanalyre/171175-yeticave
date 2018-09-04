<?php
require_once("functions.php");
require_once("data.php");
require_once ('connection.php');

date_default_timezone_set("Europe/Moscow");


$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

$lotsList = getLotsList(6);
$lotListContent = ''; // содержит все мои лоты
foreach ($lotsList as $lot) {
    $lotListContent .= renderTemplate('lot-oneItem', $lot);
}

$categories = getCatList();

var_dump($_SESSION['user']);

$templContent = renderTemplate('index', [
    'lotListContent' => $lotListContent]);

$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories' => $categories,
    'isAuth' => empty(getUserSessionData()) ? false : true,
    'userName' => getUserSessionData()['us_name'] ?? null,
    'userAvatar' => getUserSessionData()['us_image'] ?? null,
    'pageName' => 'Main - YetiCave']);

print($layoutContent);
