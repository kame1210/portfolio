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
$ses = new Session($db);
$member = new Member($db);
$common = new Common($db);


if (isset($_SESSION['id']) === true) {
  $mem_id = $_SESSION['id'];

  $res = $member->memberCheck($mem_id);

  if (count($res) !== 0) {
    $userData = $res[0];
  } else {
    $userData = '';
    exit('登録情報がありません');
  }
}

$errMsg = '';
$errArr = [];

$res = '';

if (isset($_POST['update']) === true) {
  $updateData = $_POST;
  unset($updateData['update']);

  $errArr = $common->errorCheck($updateData);
  $err_check = $common->getErrorFlg();


  if ($err_check !== false) {
    $res = $member->memberUpdate($updateData, $mem_id);
  }

  if ($res !== false && $res !== '') {
    header('Location: http://localhost/DT/portfolio/member_info.php');
  } else {
    $errArr['fail'] = '登録に失敗しました';
  }
}



$sexArr = initMaster::getSex();
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();


$context = [];
$context['userData'] = $userData;

$context['sexArr'] = $sexArr;
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['errArr'] = $errArr;

$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];


$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];


$template = $twig->loadTemplate('settings.html.twig');
$template->display($context);


  // $table = ' member ';
  // $column = '';
  // $where = ' mem_id = ? ';
  // $arrVal = [$id];

  // $res = $db->select($table, $column, $where, $arrVal);

  // $updateData['password'] = password_hash($updateData['password'], PASSWORD_DEFAULT);

  // $table = ' member ';
  // $arrVal = $updateData;
  // $where = ' mem_id = ? ';
  // $arrWhereVal = [$_SESSION['id']];

  // $res = $db->update($table, $arrVal, $where, $arrWhereVal);