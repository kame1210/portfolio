<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Common;
use portfolio\lib\Member;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);
$common = new Common($db);
$member = new Member($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);



// ログイン状態だった場合は、マイページに遷移
if (isset($_SESSION['user_name']) === true) {
  header("Location:" . Bootstrap::ENTRY_URL . "mypage.php");
}


$errArr = [];

// ログインボタンが押された時の処理。
if (isset($_POST['send']) == true) {
  unset($_POST['send']);
  $dataArr = $_POST;

  // エラーチェック
  $errArr = $common->loginerrorCheck($dataArr);

  // ログイン処理
  $res = '';

  if ($errArr['email'] == '' && $errArr['password'] == '') {
    $res = $member->login($dataArr);
    if ($res !== '' ) {
      $errArr['fail'] = $res;
    }
  }
}

$context = [];

$context['errArr'] = $errArr;

$template = $twig->loadTemplate('login.html.twig');
$template->display($context);