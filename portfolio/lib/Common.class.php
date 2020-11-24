<?php

namespace portfolio\lib;

class Common
{
  private $dataArr = [];
  private $errArr = [];
  private $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function errorCheck($dataArr)
  {
    $this->dataArr = $dataArr;

    $this->createErrorMessage();

    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->sexCheck();
    $this->birthCheck();
    $this->zipCheck();
    $this->addCheck();
    $this->telCheck();
    $this->mailCheck();
    $this->passwordCheck();

    return $this->errArr;
  }

  public function loginErrorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    //クラス内のメソッドを読みこむ
    $this->createErrorMessage();

    $this->loginMailCheck();
    $this->loginPasswordCheck();

    return $this->errArr;
  }

  public function itemErrorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    //クラス内のメソッドを読みこむ
    $this->createErrorMessage();

    $this->itemNameCheck();
    $this->priceCheck();
    $this->itemDetailCheck();
    $this->imageCheck();
    $this->categoryCheck();
    $this->subCategoryCheck();

    return $this->errArr;
  }

  public function contactErrorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    //クラス内のメソッドを読みこむ
    $this->createErrorMessage();

    $this->nameCheck();
    $this->contactmailCheck();
    $this->contentsCheck();

    return $this->errArr;
  }

  protected function createErrorMessage()
  {
    foreach ($this->dataArr as $key => $val) {
      $this->errArr[$key] = '';
    }
  }

  private function familyNameCheck()
  {
    if ($this->dataArr['family_name'] === '') {
      $this->errArr['family_name'] = 'お名前(氏)を入力してください';
    }
  }

  private function firstNameCheck()
  {
    if ($this->dataArr['first_name'] === '') {
      $this->errArr['first_name'] = 'お名前(名)を入力してください';
    }
  }

  private function sexCheck()
  {
    if ($this->dataArr['sex'] === '') {
      $this->errArr['sex'] = '性別を選択してください';
    }
  }

  private function birthCheck()
  {
    //それぞれのエラーチェック
    if ($this->dataArr['year'] === '') {
      $this->errArr['year'] = '生年月日の年を選択してください';
    }
    if ($this->dataArr['month'] === '') {
      $this->errArr['month'] = '生年月日の月を選択してください';
    }
    if ($this->dataArr['day'] === '') {
      $this->errArr['day'] = '生年月日の日を選択してください';
    }

    //正しいデータか
    if (checkdate($this->dataArr['month'], $this->dataArr['day'], $this->dataArr['year']) === false) {
      $this->errArr['year'] = '生年月日の年を選択してください';
    }

    if (strtotime($this->dataArr['year'] . '-' . $this->dataArr['month'] . '-' . $this->dataArr['day']) - strtotime("now") > 0) {
      $this->errArr['year'] = '正しい日付を入力してください';
    }
  }

  private function zipCheck()
  {
    if (preg_match('/^[0-9]{7}$/', $this->dataArr['zip']) === 0) {
      $this->errArr['zip'] = '郵便番号は半角数字7桁ハイフンなしで入力してください';
    }
  }

  private function addCheck()
  {
    if ($this->dataArr['address1'] === '') {
      $this->errArr['address1'] = '住所を入力してください';
    } elseif (preg_match('/^(.+[都道府県])(.+[市町村区]).+[0-9]+$/', $this->dataArr['address1']) === 0) {
      $this->errArr['address1'] = '市区町村～番地までを正しい形式で入力してください';
    }
  }

  private function mailCheck()
  {
    $emailData = $this->mailTbCheck();

    if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9])+$/', $this->dataArr['email']) === 0) {
      $this->errArr['email'] = 'メールアドレスを正しい形式で入力してください';
    } elseif (isset($_SESSION['email']) && $_SESSION['email'] === $emailData['email']) {
      $this->errArr['email'] = '';
    } elseif ($emailData['email'] !== '') {
      $this->errArr['email'] = 'このメールアドレスはすでに存在しています';
    }
  }

  private function mailTbCheck()
  {
    $table = ' member ';
    $column = ' email ';
    $where = ' email = ? ';
    $arrVal = [$this->dataArr['email']];

    $res = $this->db->select($table, $column, $where, $arrVal);

    if (count($res) !== 0) {
      return $res[0];
    } else {
      $res[0]['email'] = '';
      return $res[0];
    }
  }

  private function telCheck()
  {
    if (
      preg_match('/^\d{10,11}$/', $this->dataArr['tel']) === 0
    ) {
      $this->errArr['tel'] = '電話番号は、半角数字で11桁以内ハイフンなしで入力してください';
    }
  }

  private function passwordCheck()
  {
    if ($this->dataArr['password'] === '') {
      $this->errArr['password'] = 'パスワードを入力してください';
    } elseif (mb_strlen($this->dataArr['password'], 'UTF-8') < 8) {
      $this->errArr['password'] = 'パスワードをは8文字以上で入力してください';
    }
  }

  private function itemNameCheck()
  {
    if ($this->dataArr['item_name'] === '') {
      $this->errArr['item_name'] = '商品名を入力してください';
    }
  }

  private function priceCheck()
  {
    if ($this->dataArr['price'] === '') {
      $this->errArr['price'] = '価格を設定してください';
    } elseif (ctype_digit($this->dataArr['price']) === false) {
      $this->errArr['price'] = '価格は数字で選択してください';
    }
  }

  private function itemDetailCheck()
  {
    if ($this->dataArr['detail'] === '') {
      $this->errArr['detail'] = '商品詳細を入力してください';
    }
  }

  private function imageCheck()
  {
    $dataCheck = array_filter($this->dataArr['image']['name']);

    if (!empty($dataCheck)) {
      for ($i = 0; $i < count($this->dataArr['image']); $i++) {
        if ($this->dataArr['image']['name'][$i] !== "") {
          if ($this->dataArr['image']['error'][$i] === 0 && $this->dataArr['image']['size'][$i] !== 0) {
            if (is_uploaded_file($this->dataArr['image']['tmp_name'][$i]) === true) {
              $image_info = getimagesize($this->dataArr['image']['tmp_name'][$i]);
              $image_mime = $image_info['mime'];

              if ($this->dataArr['image']['size'][$i] > 1048576) {
                $this->errArr['image'] = 'アップロードできるサイズは、1MBまでです';
              } elseif (preg_match('/^image\/jpeg$/', $image_mime) === 0) {
                $this->errArr['image'] = 'アップロードできる画像の形式は、JPEG形式だけです';
              }
            } else {
              $this->errArr['image'] = 'ファイルが正常にアップロードされませんでした';
            }
          } else {
            $this->errArr['image'] = 'ファイルが正常にアップロードされませんでした';
          }
        }
      }
    } else {
      $this->errArr['image'] = 'サムネイルは1つ以上登録してください';
    }
  }

  // 旧アイテム
  // private function imageCheck()
  // {
  //   if ($this->dataArr['image']['error'] === 0 && $this->dataArr['image']['size'] !== 0) {
  //     if (is_uploaded_file($this->dataArr['image']['tmp_name']) === true) {
  //       $image_info = getimagesize($this->dataArr['image']['tmp_name']);
  //       $image_mime = $image_info['mime'];

  //       // var_dump($image_mime);

  //       if ($this->dataArr['image']['size'] > 1048576) {
  //         $errArr['image'] = 'アップロードできるサイズは、1MBまでです';
  //       } elseif (preg_match('/^image\/jpeg$/', $image_mime) === 0) {
  //         $errArr['image'] = 'アップロードできる画像の形式は、JPEG形式だけです';
  //       }
  //     } else {
  //       $errArr['image'] = 'ファイルが正常にアップロードされませんでした';
  //     }
  //   } else {
  //     $errArr['image'] = 'ファイルが正常にアップロードされませんでした';
  //   }
  // }

  private function categoryCheck()
  {
    if ($this->dataArr['category'] === '') {
      $this->errArr['category'] = 'カテゴリーを選択してください';
    }
  }

  private function subCategoryCheck()
  {
    if ($this->dataArr['subcategory'] === '') {
      $this->errArr['subcategory'] = 'サブカテゴリーを選択してください';
    }
  }

  private function nameCheck()
  {
    if ($this->dataArr['name'] === '') {
      $this->errArr['name'] = '名前を入力してください';
    }
  }

  private function contactmailCheck()
  {
    if (preg_match('/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/', $this->dataArr['email']) === 0) {
      $this->errArr['email'] = 'メールアドレスを正しい形式で入力してください';
    }
  }

  private function contentsCheck()
  {
    if ($this->dataArr['contents'] === '') {
      $this->errArr['contents'] = '問い合わせ内容を入力してください';
    }
  }

  public function getErrorFlg()
  {
    $err_check = true;
    foreach ($this->errArr as $key => $val) {
      if ($val !== '') {
        $err_check = false;
      }
    }
    return $err_check;
  }

  public function loginMailCheck()
  {
    if ($this->dataArr['email'] === '') {
      $this->errArr['email'] = 'メールアドレスを入力してください';
    }
  }

  public function loginPasswordCheck()
  {
    if ($this->dataArr['password'] === '') {
      $this->errArr['password'] = 'パスワードを入力してください';
    }
  }
}
