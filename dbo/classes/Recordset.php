<?php

class Recordset
{
  private $conn = '';
  public $query = '';

  private $recordset = [];
  private $totalRows = 0;
  private $curRow = 0;
  private $EOF = false;



  function __construct(Conn $conn, Query $query, string $mode) {
    $this->conn = $conn->connDB;
    $this->query = $query;
  }



  private function execute_query(string $mode)
  {
    $queryData = $this->query->get_query($mode);

    $fetch = $this->conn->prepare($queryData['statement']);
    $fetch->bind_param($queryData['valTypes'], ...$queryData['values']);
    $fetch->execute();

    $this->recordset = $fetch->get_result()->fetch_all(MYSQLI_ASSOC);

    switch ($queryData['type']) {

      case 'select':
        $this->totalRows = $this->conn->affected_rows;

        if ($this->totalRows === 0) {
          $this->EOF = true;
        }

        break;

      case 'count':
        $this->totalRows = $this->recordset[0]['rowCount'];
        $this->EOF = true;
        break;
    }
  }



  public function execute(string $mode)
  {
    if(empty($mode)) {
      throw new customException('Query mode is empty!');
    }

    $this->execute_query($mode);
  }



  // -------------------------------------------------------------------



  public function move_first()
  {
    if ($this->totalRows > 0) {
      $this->curRow = 0;
      $this->EOF = false;
    }
  }



  public function move_next()
  {
    if ($this->curRow == $this->totalRows) {
      $this->EOF = true;
    }

    if(!$this->EOF) {
      $this->curRow++;
    }
  }



  public function move_to(int $row)
  {
    if ($row < 0) {
      throw new customException('Can not move to negative row!');
    }

    if ($row > $this->totalRows - 1)
    {
      throw new customException(
        'Can not move above highest row!<br>Requested row: ' . $row
        . '<br>Total rows: ' . $this->totalRows
      );
    }
  }



  public function move_last()
  {
    if ($this->totalRows > 0) {
      $this->curRow = $this->totalRows - 1;
    }
  }



  // -------------------------------------------------------------------



  public function get_field(string $field)
  {
    if (empty($field)) {
      throw new customException('Value for field is empty!');
    }

    if ($this->EOF) {
      throw new customException('EOF true - can not retrieve field »' . $field . '«');
    }

    if (!array_key_exists($field, $this->recordset[$this->curRow])) {
      throw new customException('Field »' . $field . '« does not exist in recordset!');
    }

    return $this->recordset[$this->curRow][$field];
  }



  public function get_EOF()
  {
    return $this->EOF;
  }
}

?>
