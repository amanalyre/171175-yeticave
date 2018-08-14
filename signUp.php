<?php

require_once("functions.php");
require_once ('connection.php');

//if (isAuthorized() === false) {
//    http_response_code(403); #TODO Было бы хорошо тут редиректить на 403 страницу. Или форму авторизации
//    exit;
//}

$categories = getCatList();

if ($_POST) {
    $user = $_POST['user'];
    $avatar = $_FILES['photo'];

    $resultAddUser = saveUser($user, $avatar); //#TODO это поле должно совпадать с name в форме шаблона

    if ($resultAddUser === true) {
//        header('Location: logIn.php');
        header('Location: /');
    } else {
        $errors = $resultAddUser;
    }}

try {
    $templContent = renderTemplate('signUp', [
        'categories'  => $categories,
        'errors'      => $errors ?? [],
        'photo'       => $_FILES['photo'],
        'email'       => $user['email'] ?? '',
        'name'        => $user['name'] ?? '',
        'message'     => $user['message'] ?? '',
    ]);
} catch (Exception $e)
{
    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
};

$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories'  => $categories,
    'pageName'    => 'Регистрация']);
//'isAuth' => empty($_SESSION['user']) ? false : true,
//'userName' => $_SESSION['user']['name'] ?? null,
//'userAvatar' => $_SESSION['user']['avatar'] ?? null]);

print($layoutContent);