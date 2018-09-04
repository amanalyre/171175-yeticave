<?php

require_once("functions.php");
require_once ('connection.php');

$categories = getCatList();

if (isAuthorized() === false) {
    http_response_code(403);
    $templContent = renderTemplate('403', []);
    $categories = getCatList();
    $layoutContent = renderTemplate('layout', [
        'pageContent' => $templContent,
        'categories' => $categories,
        'pageName' => '403 Not Authorized']);
    print($layoutContent);
    exit;
}

if ($_POST) {
    $lot = $_POST['lot'];
    $image = $_FILES['photo'];

    $resultAddLot = saveLot($lot, $image); //#TODO это поле должно совпадать с name в форме шаблона

    if (is_numeric($resultAddLot)) {
        header('Location: lot.php?id=' . $resultAddLot);
    } else {
        $errors = $resultAddLot;
    }
}

try {
    $templContent = renderTemplate('add-lot', [
        'name'        => $lot['name'] ?? '',
        'category'    => $lot['category'] ?? '',
        'categories'  => $categories,
        'description' => $lot['description'] ?? '',
        'start_price' => $lot['start_price'] ?? '',
        'step'        => $lot['step'] ?? '',
        'finish_date' => $lot['finish_date'] ?? '',
        'errors'      => $errors ?? [],
        'photo'       => $_FILES['photo']
    ]);
} catch (Exception $e)
{
    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
};

var_dump($_SESSION['user']);
$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories'  => $categories,
    'pageName'    => 'Добавление нового лота',
    'isAuth'      => empty($_SESSION['user']) ? false : true,
    'userName' => $_SESSION['user']['name'] ?? null,
    'userAvatar' => $_SESSION['user']['avatar'] ?? null]);

print($layoutContent);