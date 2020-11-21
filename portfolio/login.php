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
  header('Location: http://localhost/DT/portfolio/mypage.php');
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


  // if ($_POST['email'] !== '') {
  //   $email = $_POST['email'];
  // } else {
  //   $email = '';
  //   $errArr['email'] = 'メールアドレスを入力してください';
  // }

  // if ($_POST['password'] !== '') {
  //   $password = $_POST['password'];
  // } else {
  //   $password = '';
  //   $errArr['password'] = 'パスワードを入力してください';
  // }

    // $table = ' member ';
  // $column = '';
  // $where = ' email = ? AND delete_flg = ? ';
  // $arrVal = [$email, 0];

  // $res = $db->select($table, $column, $where, $arrVal);

  // if (count($res) !== 0) {
  //   $memData = $res[0];
    
  //   if ($memData['email'] === $email && password_verify($password, $memData['password']) === true) {

  //     $_SESSION['id'] = $memData['mem_id'];
  //     $_SESSION['user_name'] = $memData['family_name'] . $memData['first_name']; 
  //     header('Location: http://localhost/DT/portfolio/top.php');
      
  //   } else {
  //     $errArr['miss'] = 'メールアドレスかパスワードが違います';
  //   }
  // } else {
  //   $errArr['miss'] = 'メールアドレスかパスワードが違います';
  // }