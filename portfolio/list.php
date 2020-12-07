<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Item;
use portfolio\lib\likes;
use portfolio\lib\Page2;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', '', '', 'item_id');

$ses = new Session($db);
$itm = new Item($db);
$likes = new likes($dbgroup);
$page = new Page2();

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$function = new \Twig_SimpleFunction('like_exsits', function ($item_id) {
  $dbgroup = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE, '', '', '', 'item_id');
  $likes = new likes($dbgroup);
  return $likes->like_exsits($_SESSION['id'], $item_id);
});
$twig->addFunction($function);


$ses->checkSession();

// 検索のモード設定
if (isset($_GET['ctg_id']) && isset($_GET['subctg_id']) && $_GET['subctg_id'] !== '' && $_GET['ctg_id'] !== '') {
  $mode = 'totalcategory';
} elseif (isset($_GET['ctg_id']) && $_GET['ctg_id'] !== '') {
  $mode = 'category';
} elseif (isset($_GET['subctg_id']) && $_GET['subctg_id'] !== '') {
  $mode = 'subcategory';
} elseif (isset($_GET['search']) && $_GET['search']) {
  $mode = 'search';
} else {
  $mode = 'all';
}

$dataArr = [];
$getPage = $_GET['page'];

switch ($mode) {
  case 'totalcategory':
    $type = 'totalcategory';
    $ctg_id = (preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';
    $subctg_id = (preg_match('/^[0-9]+$/', $_GET['subctg_id']) === 1) ? $_GET['subctg_id'] : '';

    $itemCount = $itm->getItemCount($type, $ctg_id, $subctg_id);
    $pData = $page->getPageData($getPage, $itemCount);

    $dataArr = $itm->getItemList($type, $ctg_id, $subctg_id, $pData['limit'], $pData['offset']);

    $pageLink = $page->getPageLink();
    break;
  case 'category':
    $type = 'category';
    $ctg_id = (preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

    $itemCount = $itm->getItemCount($type, $ctg_id);
    $pData = $page->getPageData($getPage, $itemCount);

    $dataArr = $itm->getItemList($type, $ctg_id, '', $pData['limit'], $pData['offset']);

    $pageLink = $page->getPageLink();
    break;
  case 'subcategory':
    $type = 'subcategory';
    $subctg_id = (preg_match('/^[0-9]+$/', $_GET['subctg_id']) === 1) ? $_GET['subctg_id'] : '';

    $itemCount = $itm->getItemCount($type, '', $subctg_id);
    $pData = $page->getPageData($getPage, $itemCount);

    $dataArr = $itm->getItemList($type, '', $subctg_id, $pData['limit'], $pData['offset']);

    $pageLink = $page->getPageLink();
    break;
  case 'search':
    $search = '%' . $_GET['search'] . '%';
    $itemCount = $itm->getItemSearchCount($search);
    $pData = $page->getPageData($getPage, $itemCount);

    $dataArr = $itm->getItemSearch($search);

    $pageLink = $page->getPageLink();
    break;
  case 'all':
    $type = 'all';
    $itemCount = $itm->getItemCount($type);
    $pData = $page->getPageData($getPage, $itemCount);

    $dataArr = $itm->getItemList($type, '', '', $pData['limit'], $pData['offset']);

    $pageLink = $page->getPageLink();
    break;
}

// カテゴリー取得
$cateArr = $itm->getCategoryList();
$subCateArr = $itm->getSubCategoryList();

// いいねの呼び出し
$likeArr = $likes->getLike();

// $like =  $likes->like_exsits($_SESSION['id'], )



$context = [];

$context['get'] = $_GET;
$context['cateArr'] = $cateArr;
$context['subCateArr'] = $subCateArr;
$context['dataArr'] = $dataArr;
$context['likeArr'] = $likeArr;
$context['pageLink'] = $pageLink;
$context['pData'] = $pData;
// $context['likeMemberArr'] = $likeMemberArr;

if (isset($_SESSION['user_name']) === true) {
  $context['user_name'] = $_SESSION['user_name'];
  $context['id'] = $_SESSION['id'];
}

$template = $twig->loadTemplate('list.html.twig');
$template->display($context);




// カテゴリーでの検索処理
// $ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

// 検索窓での検索処理
// $search = (isset($_GET['search']) === true) ? '%' . $_GET['search'] . '%' : '';

// $subcate = (isset($_GET['subcate'])) ? $_GET['subcate'] : '';


// 検索窓が使われていたら、if処理。使われてなかったらelse処理
// $dataArr = [];
// if ($search !== '') {
//   unset($_GET['send']);
//   $dataArr = $itm->getItemsearch($search);
// } else {
//   $dataArr = $itm->getItemList($ctg_id, $pages->max_view, $page);
// }
