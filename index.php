<?php
session_start();
require_once "vendor/autoload.php";

use \Hcode\Model\User;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function () {

    $page = new Page();
    $page->setTpl("index");

});
$app->get('/admin', function () {
    User::Verifylogin();
    $page = new PageAdmin();
    $page->setTpl("index");

});
$app->get('/admin/login', function () {

    $page = new PageAdmin([
        "header" => false,
        "footer" => false,
    ]);
    $page->setTpl("login");

});
$app->post('/admin/login', function () {

    User::login($_POST['login'], $_POST['password']);
    header("location:/admin");
    exit;
});

$app->get('/admin/logout', function () {

    User::logout();
    header("Location: /admin/login");
    exit;
});

$app->get('/admin/users', function () {
    User::Verifylogin();
    $users = User::listAll();
    $page = new PageAdmin();
    $page->setTpl("users", array(
        "users" => $users,
    ));
});
$app->get('/admin/users/create', function () {
    User::Verifylogin();
    /*    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

    $_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

    "cost" => 12,

    ]);

    $user->setData($_POST);

    $user->save();

    header("Location: /admin/users");
    exit; */
    $page = new PageAdmin();
    $page->setTpl("users-create");
});
$app->get("/admin/users/:iduser/delete", function ($iduser) {
    User::Verifylogin();
    $user = new User();
    $user->get((int) $iduser);
    $user->delete();
    header("Location: /admin/users");
    exit;

});
$app->get('/admin/users/:iduser', function ($iduser) {
    User::Verifylogin();
    $user = new User();

    $user->get((int) $iduser);
    $page = new PageAdmin();

    $page->setTpl("users-update", array(
        "user" => $user->getValues(),
    ));
});
$app->post("/admin/users/create", function () {
    User::Verifylogin();

    $user = new User();
    $user->setData($_POST);

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;
    $user->setData($_POST);

    $user->save();

    header("Location:/admin/users");
    exit;

});
$app->post("/admin/users/:iduser", function ($iduser) {
    User::Verifylogin();
    $user = new User();

    $_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

    $user->get((int) $iduser);
    $user->setData($_POST);
    $user->update();

    header("Location: /admin/users");
    exit;

});

$app->run();
