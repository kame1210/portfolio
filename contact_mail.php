<?php

namespace portfolio;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use portfolio\lib\PDODatabase;
use portfolio\lib\Session;
use portfolio\lib\Mail;
use portfolio\lib\Common;

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [
  'cache' => Bootstrap::CACHE_DIR
]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

$ses = new Session($db);
$common = new Common($db);

$to = "koaki1005@gmail.com";
$subject = "サイトへ問い合わせがありました";
$mail = new Mail($to, $subject, $_POST);


$contactArr = [];
$errArr = [];
$template = '';


if (isset($_POST['send']) === true) {
  unset($_POST['send']);
  $contactArr = $_POST;

  $errArr = $common->contactErrorCheck($contactArr);
  $err_check = $common->getErrorFlg();

  if ($err_check !== false) {
    // $res = $mail->mailSend();
    $res = true;

    if ($res !== false) {
      $template = 'contact_mail_complete.html.twig';
    } else {
      $template = 'contact_mail_fail.html.twig';
    }
  } else {
    $template = 'contact.html.twig';
  }
}

$context['errArr'] = $errArr;
$context['contactArr'] = $contactArr;
$context['id'] = $_SESSION['id'];
$context['user_name'] = $_SESSION['user_name'];

$template = $twig->loadTemplate($template);
$template->display($context);
