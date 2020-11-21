<?php

namespace portfolio\lib;

class order
{
  private $db = '';

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function insorder()
  {
    $table = ' order_tb ';
    $insData = [
      ' mem_id ' => $_SESSION['id']
    ];

    $this->db->insert($table, $insData);
  }

  public function getorderId()
  {
    $table = ' order_tb ';
    $column = ' order_id ';
    $where = ' mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $column, $where, $arrVal, '');

    rsort($res);

    return $res[0]['order_id'];
  }

  public function getTotalorderId()
  {
    $table = ' order_tb ';
    $column = ' order_id, regist_date ';
    $where = ' mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $column, $where, $arrVal, '');

    return $res;
  }

  public function getOrderDetail()
  {
    $table = ' order_tb o INNER JOIN order_detail detail ON o.order_id = detail.order_id ';
    $column = ' detail.order_detail_id, detail.order_id, detail.item_id, detail.quantity ';
    $where = ' o.mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }
 
  public function getOrderPrice()
  {
    $table = ' order_tb o JOIN order_detail detail ON o.order_id = detail.order_id JOIN item i ON detail.item_id = i.item_id ';
    $column = ' o.order_id, sum(i.price * quantity ) as sum ';
    $where = ' o.mem_id = ? ';
    $arrVal = [$_SESSION['id']];
    
    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }
  
  public function getOrderitem()
  {
    $table = ' order_tb o INNER JOIN order_detail detail ON o.order_id = detail.order_id INNER JOIN item i ON detail.item_id = i.item_id ';
    $column = ' o.regist_date, detail.order_id, detail.item_id, detail.quantity, i.item_name, i.detail, i.price, i.image ';
    $where = ' o.mem_id = ? ';
    $arrVal = [$_SESSION['id']];

    $res = $this->db->select($table, $column, $where, $arrVal);

    return $res;
  }

  public function insorderDetail($cartData)
  {
    $count = count($cartData);
    $i = 0;

    while ($i < $count) {
      $table = ' order_detail ';
      $insData = [
        'order_id' => $this->getorderId(),
        'item_id' => $cartData[$i]['item_id'],
        'quantity' => $cartData[$i]['quantity'],
      ];

      $this->db->insert($table, $insData);

      $i++;
    }
  }
}
