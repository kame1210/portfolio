<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\master\initMaster;
use portfolio\Bootstrap;
use portfolio\lib\PDODatabase;
use portfolio\lib\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);

$dataArr = [
  'family_name' => '',
  'first_name' => '',
  'family_name_kana' => '',
  'first_name_kana' => '',
  'sex' => '',
  'year' => '',
  'month' => '',
  'day' => '',
  'zip' => '',
  'address1' => '',
  'address2' => '',
  'address3' => '',
  'email' => '',
  'tel' => '',
  'password' => ''
];

// エラーメッセージの作成
$errArr = [];

foreach ($dataArr as $key => $value) {
  $errArr[$key] = '';
}

// 生年月日 セレクトボックスの作成
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

// 性別 ラジオボタンの作成
$sexArr = initMaster::getSex();

$context = [];

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate('regist.html.twig');
$template->display($context);
