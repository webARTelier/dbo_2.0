<?php

class Dbo
{
  public $conn = '';
  private $structure = '';



  function __construct(array $dbAccess)
  {
    Utils::checkNotEmpty($dbAccess, 'DB access data');
    $this->conn = new Conn(...$dbAccess);
    $this->structure = new Structure($this->conn);
  }



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
