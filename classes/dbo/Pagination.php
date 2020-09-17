<?php

class Pagination
{
  private $recordset = '';

  private $getParam = 'page';
  private $cutLR = 1;
  private $entriesPerPage = 10;
  private $totalEntries = 0;
  private $totalPages = 0;
  private $currentPage = 1;
  private $html = '';
  private $html_count = '';



  function __construct(Recordset $recordset)
  {
    $this->recordset = $recordset;
    $this->totalEntries = $this->recordset->get_totalRows();
  }



  // -------------------------------------------------------------------



  public function set_getParam(string $getParam)
  {
    if (empty($getParam)) {
      throw new customException('Value for ›Get parameter‹ is empty!');
    }

    $this->getParam = $getParam;
  }



  public function set_entriesPerPage(int $entriesPerPage)
  {
    if (empty($entriesPerPage)) {
      throw new customException('Value for ›entries per page‹ is empty!');
    }

    $this->entriesPerPage = $entriesPerPage;
  }



  // -------------------------------------------------------------------




}

?>
