<?php

class Pagination
{
  private $getParam = 'page';
  private $cutLR = 1;
  private $entriesPerPage = 10;
  private $totalEntries = 0;
  private $totalPages = 0;
  private $curPage = 1;
  private $html = '';
  private $html_count = '';



  // -------------------------------------------------------------------



  public function check_empty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for ›' . $label  . '‹ is empty!');
    }
  }



  // -------------------------------------------------------------------



  public function set_totalEntries(int $totalEntries)
  {
    $this->check_empty($totalEntries);
    $this->totalEntries = $totalEntries;
    $this->totalPages = ceil($this->totalEntries / $this->entriesPerPage);
  }



  public function set_curPage(int $curPage)
  {
    $this->check_empty($curPage);

    if ($curPage < 1) {
      $curPage = 1;
    }

    if ($curPage > $this->totalPages) {
      $curPage = $this->totalPages;
    }

    $this->curPage = $curPage;
    $this->create_html();
  }



  public function get_limit()
  {
    return $this->entriesPerPage;
  }



  public function get_offset()
  {
    if ($this->totalEntries > $this->entriesPerPage) {
      $offset = $this->entriesPerPage * ($this->curPage - 1);
    } else {
      $offset = 0;
    }

    return $offset;
  }
}

?>
