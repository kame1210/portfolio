<?php

namespace portfolio\lib;

class Item
{
  public $cateArr = [];
  public $db = null;

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getCategoryList()
  {
    $table = ' category ';
    $col = ' ctg_id, category_name ';
    $res = $this->db->select($table, $col);

    return $res;
  }

  public function getSubCategoryList()
  {
    $table = ' subcategory ';
    $col = ' ctg_id, category_name ';
    $res = $this->db->select($table, $col);

    return $res;
  }

  public function getItemList($type, $ctg_id = '', $subctg_id = '', $limit = '', $offset = '')
  {
    $table = ' item ';
    $col = ' item_id, item_name, price, image ';
    $where = '';
    $arrVal = [];

    switch ($type) {
      case 'totalcategory':
        $where = 'ctg_id = ? AND subctg_id = ? ';
        $arrVal = [$ctg_id, $subctg_id];
        break;
      case 'category':
        $where = 'ctg_id = ?';
        $arrVal = [$ctg_id];
        break;
      case 'subcategory':
        $where = ' subctg_id = ? ';
        $arrVal = [$subctg_id];
        break;

      case 'all':
        $where = '';
        $arrVal = [];
        break;
    }

    $res = $this->db->selectLimit($table, $col, $where, $arrVal, $limit, $offset);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getItemSubList($ctg_id = '', $limit = '', $offset = '')
  {
    $table = ' item ';
    $col = ' i.item_id, i.item_name, i.price, i.image, i.ctg_id, l.item_id as likes ';
    $where = ($ctg_id !== '') ? ' subctg_id = ? ' : '';
    $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal, '', $limit, $offset);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getItemCount($type, $ctg_id = '', $subctg_id = '')
  {
    $table = ' item ';
    $column = ' count(item_id) as itemCount ';

    $where = '';
    $arrVal = [];

    switch ($type) {
      case 'totalcategory':
        $where = 'ctg_id = ? AND subctg_id = ? ';
        $arrVal = [$ctg_id, $subctg_id];
        break;
      case 'category':
        $where = 'ctg_id = ?';
        $arrVal = [$ctg_id];
        break;
      case 'subcategory':
        $where = ' subctg_id = ? ';
        $arrVal = [$subctg_id];
        break;

      case 'all':
        $where = '';
        $arrVal = [];
        break;
    }

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res[0]['itemCount'];
  }

  public function getlikesItemList()
  {
    $table = ' likes inner join item on likes.item_id = item.item_id ';
    // $col = ' item_id, item_name, price, image, ctg_id ';
    $col = ' * ';
    $where = ' likes.mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $col, $where, $arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function submitItemList()
  {
    $table = ' item ';
    // $col = ' item_id, item_name, price, image, ctg_id ';
    $col = ' * ';
    $where = ' mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $col, $where, $arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getItemSearch($search)
  {
    $table = ' item ';
    $col = ' item_id, item_name, price, image, ctg_id ';
    $where = ' item_name ';
    $like = ($search !== '') ? ' ? ' : '';
    $arrVal = [$search];

    $res = $this->db->selectlike($table, $col, $where, $like, $arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function getItemSearchCount($search)
  {
    $table = ' item ';
    $col = '  count(item_id) as itemCount ';
    $where = ' item_name ';
    $like = ($search !== '') ? ' ? ' : '';
    $arrVal = [$search];

    $res = $this->db->selectlike($table, $col, $where, $like, $arrVal);

    return $res[0]['itemCount'];
  }

  public function getItemDetailDate($item_id)
  {
    $table = ' item ';
    $col = ' item_id, item_name, detail, price, image, ctg_id ';

    $where = ($item_id !== '') ? ' item_id = ? ' : '';
    $arrVal = ($item_id !== '') ? [$item_id] : [];

    $res = $this->db->select($table, $col, $where, $arrVal);

    return ($res !== false && count($res) !== 0) ? $res : false;
  }

  public function uploadFile($dataArr)
  {
    move_uploaded_file($dataArr['image']['tmp_name'], './upimages/upload_' . $dataArr['image']['name']);
  }

  public function insItemData($dataArr)
  {
    $table = ' item ';
    $insData = [
      'item_name' => $dataArr['item_name'],
      'detail' => $dataArr['detail'],
      'price' => $dataArr['price'],
      'image' => 'upload_' . $dataArr['image']['name'],
      'ctg_id' => $dataArr['category'],
      'subctg_id' => $dataArr['subcategory'],
      'mem_id' => $dataArr['mem_id']
    ];

    $res = $this->db->insert($table, $insData);

    return $res;
  }
}


// -------------------------------- 旧メソッド --------------------------------------------

  // public function getItemList($ctg_id = '')
  // {
  //   $table = ' item i LEFT OUTER JOIN likes l ON i.item_id = l.item_id ';
  //   $col = ' i.item_id, i.item_name, i.price, i.image, i.ctg_id, l.item_id as likes ';
  //   $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
  //   $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

  //   $res = $this->db->select($table, $col, $where, $arrVal);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

  
  // public function getItemList($ctg_id = '', $limit = '', $offset = '')
  // {
  //   $table = ' item ';
  //   $col = ' item_id, item_name, price, image, ctg_id ';
  //   $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
  //   $arrVal = ($ctg_id !== '') ? [$ctg_id] : [];

  //   $res = $this->db->select($table, $col, $where, $arrVal, '', $limit, $offset);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

    // public function getItemCount()
  // {
  //   $table = ' item ';
  //   $column = ' count(item_id) as itemCount ';
  //   $where = '';

  //   $res = $this->db->select($table, $column, $where);

  //   return $res[0]['itemCount'];
  // }

  // 引数に$typeを送る
  // 予想されるのは主に4つ。
  // totalcategory category subcategory all;

  // public function getItemTotal($ctg_id, $subctg_id, $limit = '', $offset = '')
  // {
  //   $table = ' item ';
  //   $col = ' * ';
  //   $where = ' ctg_id = ? AND subctg_id = ? ';
  //   $arrVal = [$ctg_id, $subctg_id];

  //   $res = $this->db->select($table, $col, $where, $arrVal, $limit, $offset);

  //   return ($res !== false && count($res) !== 0) ? $res : false;
  // }

  // ------------------------------------------------------------------------------------