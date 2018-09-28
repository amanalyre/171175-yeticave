<?php

require_once("functions.php");
require_once ('connection.php');
require_once ("configure.php");

// $lot_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($_POST)) {
    // вызываем функицию добавления ставки
}

//if ($lot_id == false || $lot_info == false) { // тут нужно будет переделать
//    http_response_code(404);
//    $templContent = renderTemplate('404', []);
//    $categories = getCatList();
//    $layoutContent = renderTemplate('layout', [
//        'pageContent' => $templContent,
//        'categories' => $categories,
//        'pageName' => '404 Not Found']);
//    print($layoutContent);
//    exit;
//}

$lot_info =  getLot($lot_id);

$templContent = renderTemplate('lot', [
    'lot_info' => $lot_info]);

$categories = getCatList();

$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories' => $categories,
    'pageName' => $lot_info['lot_name'],
    'isAuth' => empty(getUserSessionData()) ? false : true,
    'userName' => getUserSessionData()['us_name'] ?? null,
    'userAvatar' => getUserSessionData()['us_image'] ?? null]);

print($layoutContent);