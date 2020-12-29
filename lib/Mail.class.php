<?php

namespace portfolio\lib;

class Mail
{
  private $to = '';
  private $subject = '';
  private $message = '';
  private $headers = '';

  public function __construct($to, $subject, $postData)
  {
    $this->to = $to;
    $this->subject = $subject;
    $this->message = $this->setMessage($postData);
    $this->headers = $this->setHeaders($postData);
  }

  public function mailSend()
  {
    $res = mb_send_mail($this->to, $this->subject, $this->message, $this->headers);

    return $res;
  }

  public function setMessage($postData)
  {
    $message = "【送信元】\n" . $postData['name']
    . "\n【メールアドレス】\n" . $postData['email'] . "\n"
    . "\n【お問い合わせ内容】\n" . $postData['contents'] . "\n";

    return $message;
  }

  public function setHeaders($postData)
  {
    $headers = 'From: ' . $postData['email'];

    return $headers;
  }
}