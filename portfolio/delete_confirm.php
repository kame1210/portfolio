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

if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
} elseif (isset($_POST['complete']) === true) {
  $mode = 'complete';
}


$template = '';
$errArr = [];

switch ($mode) {
  case 'confirm':
    if ($_POST['password'] !== '') {
      unset($_POST['confirm']);

      $dataArr = $_POST;
      $dataArr['email'] = $_SESSION['email'];

      var_dump($_SESSION['email']);

      $res = $member->deleteMemberCheck($dataArr);

      if ($res === '') {
        $template = 'delete_confirm.html.twig';
      } else {
        $template = 'delete_member.html.twig';
        $errArr['fail'] = $res;
      }
    } else {
      $template = 'delete_member.html.twig';
      $errArr['password'] = 'パスワードを入力してください';
    }
    break;

  case 'complete':
    $dataArr['email'] = $_SESSION['email'];

    $res = $member->memberDelete($dataArr);

    if ($res === true) {
      $member->logout();
      $template = 'delete_complete.html.twig';
    } else {
      $template = 'delete_confirm.html.twig';
      $errArr['fail'] = '退会処理に失敗しました。詳しくは運営までお問い合わせくださいませ。';
    }
    break;
}

$context = [];
$context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);
