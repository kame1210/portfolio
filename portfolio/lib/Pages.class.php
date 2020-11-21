<?php

namespace portfolio\lib;

class Pages
{
  private $db = null;
  public $max_view = '';
  private $count = '';
  private $totalPages = '';
  private $pageRange = 2;
  private $page = '';
  private $prev = '';
  private $next = '';
  private $startPage = 1;

  public function __construct($db, $max_view, $page = 1)
  {
    $this->db = $db;
    $this->max_view = $max_view;
    $this->count = $this->getItemCount();
    $this->totalPages = $this->getTotalPages();
    $this->page = intval($page);
    $this->prev = $this->getPrev();
    $this->next = $this->getNext();
  }

  public function getItemCount()
  {
    $table = ' item ';
    $column = ' count(item_id) as itemCount ';
    $where = '';

    $res = $this->db->select($table, $column, $where);

    return $res;
  }

  public function getTotalPages()
  {
    $res = ceil($this->count[0]['itemCount'] / $this->max_view);

    return $res;
  }

  public function getPrev()
  {
    $res = max($this->page - 1, $this->startPage);

    return $res;
  }

  public function getNext()
  {
    $res = min($this->page + 1, $this->totalPages);

    return $res;
  }

  public function getStart()
  {
    $res = max($this->page - $this->pageRange, 1);

    return $res;
  }

  public function getEnd()
  {
    $res = min($this->page + $this->pageRange, $this->totalPages);

    // if ($this->page === 1) {
    //   $res = $this->pageRange * 2;
    // }

    return $res;
  }


  public function getPageslist()
  {
    $nums = [];

    $start = $this->getStart();
    $end = $this->getEnd();

    for ($i = $start; $i <= $end; $i++) {
      $nums[] = $i;

      // num[
      //   0 => $i,
      //   1 => $i,
      //   2 => $i,
      // ]; = $i
    }

    return $nums;
  }

  // public function pagination()
  // {
  //   for ($i = 1;$i <= $this->page; $i++) {
  //     if ($)
  //   }
  // }

}
