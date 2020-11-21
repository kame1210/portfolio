<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Item;
use portfolio\lib\Common;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);
$itm = new Item($db);
$common = new Common($db);

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);


if (isset($_POST['edit'])) {
  $mode = 'edit';
}
if (isset($_POST['delete'])) {
  $mode = 'delete';
}
if (isset($_POST['complete'])) {
  $mode = 'complete';
}

$itemData = [];
$errArr = [];
$errArr['miss'] = '';

$ctgList = $itm->getCategoryList();

$subCtgList = $itm->getSubCategoryList();

switch ($mode) {
  case 'edit':
    
    $itemData = $_POST;
    $itemData['price'] = floor($itemData['price']);
    unset($itemData['edit']);

    break;

  case 'delete':
    $table = ' item ';
    $where = ' item_id = ? ';
    $arrVal = [$_POST['item_id']];

    $db->delete($table, $where, $arrVal);
    header('Location: http://localhost/DT/portfolio/mypage.php');
    break;

  case 'complete':

    // update処理のための準備
    $dataArr = $_POST;
    $dataArr['image'] = $_FILES['image'];
    unset($dataArr['complete']);


    if (isset($dataArr['category']) === false) {
      $dataArr['category'] = '';
    }

    $errArr = $common->itemErrorCheck($dataArr);

    $err_check = $common->getErrorFlg();

    if ($err_check === true) {
      move_uploaded_file($dataArr['image']['tmp_name'], './upimages/upload_' . $dataArr['image']['name']);


    $table = ' item ';
    $insData = [
      'item_name' => $dataArr['item_name'],
      'detail' => $dataArr['detail'],
      'price' => $dataArr['price'],
      'image ' => 'upload_' . $dataArr['image']['name'],
      'ctg_id' => $dataArr['category']
    ];

    // var_dump($insData);
    $where = ' item_id = ? ';
    $arrVal =  [$_POST['item_id']];

    $res = $db->update($table,$insData,$where, $arrVal);

    if ($res === true) {
      header("Location: http://localhost/DT/portfolio/myitemdetail.php?item_id={$_POST['item_id']}");
    } else {
      $errArr['miss'] = '登録に失敗しました';
    }
  }
break;
}

$context = [];

$context['itemData'] = $itemData;
$context['errArr'] = $errArr;
$context['ctgList'] = $ctgList;
$context['subCtgList'] = $subCtgList;


$template = $twig->loadTemplate('myitemedit.html.twig');
$template->display($context);

