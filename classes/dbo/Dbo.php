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



  public function createNewRecordset()
  {
    $newQuery = new Query($this->structure);
    $newRS = new Recordset($newQuery);
    return $newRS;
  }



  public function createNewStorage()
  {
    $newStorage = new Storage($this->structure);
    return $newStorage;
  }
}

?>
