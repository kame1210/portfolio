<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;
use portfolio\lib\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);
$common = new Common($db);
$itm = new Item($db);


$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

// カテゴリーリストの取得
$ctgList = $itm->getCategoryList();

$subCtgList = $itm->getSubCategoryList();

$dataArr = [];
$errArr = [];
$msg = '';

// 登録ボタンがクリックされた場合の処理
if (isset($_POST['send']) === true) {
  unset($_POST['send']);
  $dataArr = $_POST;
  $dataArr['mem_id'] = $_SESSION['id'];
  $dataArr['image'] = $_FILES['image'];

  if (isset($dataArr['category']) === false) {
    $dataArr['category'] = '';
  }
  if (isset($dataArr['subcategory']) === false) {
    $dataArr['subcategory'] = '';
  }

  $image = array_filter($dataArr['image']['name']);
  for ($i = 0;$i < count($image);$i++){
    $image[$i] = 'upload_' . $image[$i];
  }
  $image = implode(',', $image);

  $errArr = $common->itemErrorCheck($dataArr);
  $err_check = $common->getErrorFlg();

  if ($err_check === true) {
    $itm->uploadFile($dataArr);
    $res = $itm->insItemData($dataArr);

    if ($res === true) {
      $msg = '登録に成功しました';
      unset($dataArr);
    } elseif ($res === false) {
      $msg = '登録に失敗しました';
    }
  } else {
    $msg = '入力内容をご確認ください';
  }
}

$context = [];

$context['ctgList'] = $ctgList;
$context['subCtgList'] = $subCtgList;
$context['dataArr'] = (isset($dataArr)) ? $dataArr : '';
$context['errArr'] = $errArr;
$context['msg'] = $msg;

$template = $twig->loadTemplate('item_submit.html.twig');
$template->display($context);
