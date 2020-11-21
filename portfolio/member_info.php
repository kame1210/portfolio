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

if (isset($_SESSION['user_name']) === false || isset($_SESSION['id']) === false) {
  header('Location: http://localhost/DT/portfolio/login.php');
  exit();
} else {
  $res = $member->memberCheck($_SESSION['id']);

  if (count($res) !== 0) {
    $userData = $res[0];
  } else {
    $userData = '';
    $errArr['id'] = 'データが取得できませんでした';
  }
}

$context = [];

$context['user_name'] = $_SESSION['user_name'];
$context['id'] = $_SESSION['id'];
$context['userData'] = $userData;


$template = $twig->loadTemplate('member_info.html.twig');
$template->display($context);
