<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;
use portfolio\lib\Member;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common($db);
$ses = new Session($db);
$member = new Member($db);

// mode設定
if (isset($_POST['confirm']) === true) {
  $mode = 'confirm';
} elseif (isset($_POST['back']) === true) {
  $mode = 'back';
} elseif (isset($_POST['complete']) === true) {
  $mode = 'complete';
}

// mode switch
switch ($mode) {
  case 'confirm':
    unset($_POST['confirm']);
    $dataArr = $_POST;

    if (isset($_POST['sex']) === false) {
      $dataArr['sex'] = "";
    }

    // エラーチェック
    $errArr = $common->errorCheck($dataArr);
    $err_check = $common->getErrorFlg();

    $template = ($err_check === true) ? 'confirm.html.twig' : 'regist.html.twig';
    break;

  case 'back':
    unset($_POST['back']);
    $dataArr = $_POST;

    foreach ($dataArr as $key => $value) {
      $errArr[$key] = '';
    }

    $template = 'regist.html.twig';

    break;

  case 'complete':
    unset($_POST['complete']);

    $dataArr = $_POST;
    $res = $member->memberRegist($dataArr);

    // インサート成功時、そのままログイン
    if ($res === true) {
      $member->login($dataArr);
      header('Location: ' . Bootstrap::ENTRY_URL . 'complete.php');
      exit();
    } else {
      $template = 'regist.html.twig';
      foreach ($dataArr as $key => $value) {
        $errArr[$key] = '';
      }
    }
    break;
}

$sexArr = initMaster::getSex();

$context['sexArr'] = $sexArr;

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);