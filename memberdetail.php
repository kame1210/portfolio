<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Common;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, array(
  'cache' => Bootstrap::CACHE_DIR
));

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

if (isset($_GET['mem_id']) === true && $_GET['mem_id'] !== '') {
  $mem_id = $_GET['mem_id'];

  $table = ' member ';
  $column = ' mem_id, family_name, first_name, family_name_kana, first_name_kana, sex, year, month, day, zip1, zip2, address, email, tel1, tel2, tel3, traffic, contents, regist_date ';
  $where = ' mem_id = ? ';
  $arrVal = [$mem_id];

  // $data = $db->select($table, $column, $where);
  $data = $db->select($table, $column, $where, $arrVal);

  $dataArr = ($data !== "" && $data !== []) ? $data[0] : '';

  $dataArr['traffic'] = explode('_', $dataArr['traffic']);

  $context = [];
  $context['trafficArr'] = initMaster::getTrafficWay();
  $context['dataArr'] = $dataArr;

  $template = $twig->loadTemplate('memberdetail.html.twig');
  $template->display($context);
} else {
  header('Location: ' . Bootstrap::ENTRY_URL . 'memberlist.php');
  exit();
}
