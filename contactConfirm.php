<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Common;
use portfolio\lib\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$common = new Common($db);
$ses = new Session($db);


$errArr = [];

if (isset($_POST['send']) === true) {
  $contactArr = $_POST;
  unset($contactArr['send']);

  $errArr = $common->contactErrorCheck($contactArr);
  $err_check = $common->getErrorFlg();

  $template = ($err_check !== false) ? 'contactconfirm.html.twig' : 'contact.html.twig';
}

if (isset($_POST['back']) === true) {
  $contactArr = $_POST;
  unset($contactArr['back']);

  $template = 'contact.html.twig';
}

$context = [];

$context['contactArr'] = $contactArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);