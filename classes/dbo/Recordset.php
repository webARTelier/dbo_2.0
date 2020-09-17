<?php

class Recordset
{
  private $conn = '';
  public $query = '';
  public $pagination = false;

  private $recordset = [];
  private $totalRows = 0;
  private $curRow = 0;
  private $EOF = false;

  private $min = false;
  private $max = false;



  function __construct(Query $query, Pagination $pagination = NULL)
  {
    $this->conn = $query->structure->conn;
    $this->query = $query;
  }



  // -------------------------------------------------------------------



  public function execute(string $mode)
  {
    $this->query->structure->check_empty($mode, 'mode');
    $this->execute_query($mode);
  }



  private function execute_query(string $mode)
  {
    $queryData = $this->query->get_query($mode);
    $fetch = $this->conn->prepare($queryData['statement']);
    $fetch->bind_param($queryData['valTypes'], ...$queryData['values']);
    $fetch->execute();
    $this->recordset = $fetch->get_result()->fetch_all(MYSQLI_ASSOC);

    switch ($mode) {

      case 'count':
        $this->totalRows = $this->recordset[0]['rowCount'];
        $this->EOF = true;
        break;



      case 'min':
        $this->min = $this->recordset[0]['min'];
        $this->EOF = true;
        break;



      case 'max':
        $this->max = $this->recordset[0]['max'];
        $this->EOF = true;
        break;



      case 'select':
        $this->totalRows = $this->conn->affected_rows;

        if ($this->totalRows < 1) {
          $this->EOF = true;
        }

        break;
    }
  }



  public function execute_custom(string $sql)
  {
    $this->query->structure->check_empty($sql, 'SQL query');
    $fetch = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    $this->recordset = $fetch->get_result()->fetch_all(MYSQLI_ASSOC);
    $this->totalRows = $this->conn->affected_rows;

    if ($this->totalRows < 1) {
      $this->EOF = true;
    }
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
    if ($this->curRow == $this->totalRows - 1) {
      $this->EOF = true;
    }

    if (!$this->EOF) {
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



  public function add_pagination(Pagination $pagination)
  {
    $this->pagination = $pagination;
    $this->execute_query('count');
    $this->pagination->set_totalEntries($this->totalRows);
    $this->query->set_limit($this->pagination->get_limit());
    $this->query->set_offset($this->pagination->get_offset());
  }



  // -------------------------------------------------------------------



  public function get_totalRows()
  {
    return $this->totalRows;
  }



  public function get_min()
  {
    return $this->min;
  }



  public function get_max()
  {
    return $this->max;
  }



  public function get_EOF()
  {
    return $this->EOF;
  }



  public function get_recordset() {
    return $this->recordset;
  }



  public function get_field(string $field)
  {
    $this->query->structure->check_empty($field, 'field');

    if ($this->EOF) {
      throw new customException('EOF true - can not retrieve field ›' . $field . '‹');
    }

    if (!array_key_exists($field, $this->recordset[$this->curRow])) {
      throw new customException('Field ›' . $field . '‹ does not exist in recordset!');
    }

    return $this->recordset[$this->curRow][$field];
  }



  public function find_rows(string $column, string $content)
  {
    $this->query->structure->check_empty($column, 'column');
    $this->query->structure->check_empty($content, 'content');

    if (!array_key_exists($column, $this->recordset[$this->curRow])) {
      throw new customException('Column ›' . $column . '‹ does not exist in recordset!');
    }

    $rememberCurrow = $this->curRow;
    $rememberEOF = $this->EOF;
    $resultRows = false;

    $this->move_first();

    while (!$this->EOF) {
      if ($this->recordset[$this->curRow][$column] == $content) {
        $resultRows[] = $this->curRow;
      }
      $this->move_next();
    }

    $this->curRow = $rememberCurrow;
    $this->EOF = $rememberEOF;

    return $resultRows;
  }
}

?>
