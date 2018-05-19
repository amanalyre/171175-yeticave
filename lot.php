<?php

require_once("functions.php");
require_once ('connection.php');

$lot_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($lot_id) {
    $lot_info = getLot($lot_id);
}

if ($lot_id == false || $lot_info == false) {
    http_response_code(404);
    $templContent = renderTemplate('404', []);
    $categories = getCatList();
    $layoutContent = renderTemplate('layout', [
        'pageContent' => $templContent,
        'categories' => $categories,
        'pageName' => '404 Not Found']);
    print($layoutContent);
    exit;
}

//if (!isset($_GET['id']))
//{
//    header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
//    $templContent = renderTemplate('404', []);
//} else {
//    $lot_id = intval($_GET['id']);
//}

$lot_info =  getLot($lot_id);



$templContent = renderTemplate('lot', [
    'lot_info' => $lot_info]);


$categories = getCatList();


$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories' => $categories,
    'pageName' => $lot_info['lot_name']]);

print($layoutContent);

