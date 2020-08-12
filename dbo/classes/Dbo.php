<?php

class Dbo
{
  private $conn = '';
  private $structure = '';
  private $query = '';
  private $recordset = '';



  function __construct(array $dbAccess)
  {
    if (empty($dbAccess)) {
      throw new customException('DB access data is empty!');
    }

    $this->conn = new Conn(...$dbAccess);
    $this->structure = new Structure($this->conn);
  }



  public function new_recordset()
  {
    $newQuery = new Query($this->structure);
    $newRS = new Recordset($newQuery);
    return $newRS;
  }
}

?>
