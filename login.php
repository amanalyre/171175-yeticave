<?php
require_once("functions.php");
require_once ('connection.php');
require_once ("configure.php");


$categories = getCatList();

if ($_POST) {
    $user = $_POST['user'];

    list($foundUser, $errorsFound) = login($user);
    if ($foundUser !== false) {
        $_SESSION['user'] = $foundUser;
        setcookie('sessWasStarted', 'hi', time()+60*60*24*30);
        header('Location: index.php');

        exit;
    } else {
        $errors = $errorsFound;
    }
}


try {
    $templContent = renderTemplate('login', [
        'categories'  => $categories,
        'errors'      => $errors ?? [],
        'email'       => $user['email'] ?? ''
    ]);
} catch (Exception $e)
{
    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
};

$layoutContent = renderTemplate('layout', [
    'pageContent' => $templContent,
    'categories'  => $categories,
    'pageName'    => 'Вход - Yeticave',
    'isAuth' => empty(getUserSessionData()) ? false : true,
    'userName' => getUserSessionData()['us_name'] ?? null,
    'userAvatar' => getUserSessionData()['us_image'] ?? null]);

print($layoutContent);