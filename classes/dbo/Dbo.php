<?php

class Dbo
{
  public $conn = '';
  private $structure = '';



  function __construct(array $dbAccess)
  {
    if (empty($dbAccess)) {
      throw new CustomException('DB access data is empty!');
    }

    $this->conn = new Conn(...$dbAccess);
    $this->structure = new Structure($this->conn);
  }



  // -------------------------------------------------------------------



  public function new_recordset()
  {
    $newQuery = new Query($this->structure);
    $newRS = new Recordset($newQuery);
    return $newRS;
  }



  public function new_write()
  {
    $newWrite = new Write($this->structure);
    return $newWrite;
  }
}

?>
