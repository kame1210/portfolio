<?php
namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\Bootstrap;
use portfolio\master\initMaster;
use portfolio\lib\PDODatabase;
use portfolio\lib\Common;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$table = ' member ';
$column = ' mem_id, family_name, first_name, family_name_kana, first_name_kana, sex, email, traffic, regist_date ';

$dataArr = $db->select($table, $column);

$context = [];
$context['dataArr'] = $dataArr;

$template = $twig->loadTemplate('memberlist.html.twig');
$template->display($context);