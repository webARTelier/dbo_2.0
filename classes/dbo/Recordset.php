<?php

class Recordset
{
  private $conn = '';
  public $query = '';
  public $pagination = '';

  private $recordset = [];
  private $totalRows = 0;
  private $currentRow = 0;
  private $EOF = false;

  private $min = null;
  private $max = null;



  function __construct(Query $query)
  {
    $this->conn = $query->structure->conn;
    $this->query = $query;
  }



  public function addPagination(Pagination $pagination, int $currentPage = 1)
  {
    $this->pagination = $pagination;
    $this->execute('count');
    $this->pagination->setTotalEntries($this->totalRows);
    $this->pagination->setCurrentPage($currentPage);
    $this->query->setLimit($this->pagination->getLimit());

    if (!empty($this->pagination->getOffset())) {
      $this->query->setOffset($this->pagination->getOffset());
    }

    $this->totalRows = 0;
    $this->EOF = false;
    $this->recordset = [];
  }



  public function execute(string $mode)
  {
    Utils::checkNotEmpty($mode, 'mode');

    $queryData = $this->query->getQuery($mode);
    $fetch = $this->conn->prepare($queryData['statement']);
    $fetch->bind_param($queryData['valTypes'], ...$queryData['values']);
    $fetch->execute();
    $this->recordset = $fetch->get_result()->fetch_all(MYSQLI_ASSOC);

    try {
      $methodName = 'execute' . (ucfirst(strtolower($mode)));
      $this->{$methodName}();
    } catch (customException $e) {
      throw new customException($mode . ' is not an execution mode!');
    }
  }



  private function executeCount()
  {
    $this->totalRows = $this->recordset[0]['rowCount'];
    $this->EOF = true;
  }



  private function executeMin()
  {
    $this->min = $this->recordset[0]['min'];
    $this->EOF = true;
  }



  private function executeMax()
  {
    $this->max = $this->recordset[0]['max'];
    $this->EOF = true;
  }



  private function executeSelect()
  {
    $this->totalRows = $this->conn->affected_rows;

    if ($this->totalRows < 1) {
      $this->EOF = true;
    }
  }



  public function executeCustom(string $sql)
  {
    Utils::checkNotEmpty($sql, 'SQL query');

    $fetch = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    $this->recordset = $fetch->get_result()->fetch_all(MYSQLI_ASSOC);
    $this->totalRows = $this->conn->affected_rows;

    if ($this->totalRows < 1) {
      $this->EOF = true;
    }
  }



  private function checkFieldExists(string $field)
  {
    if (!array_key_exists($field, $this->recordset[$this->currentRow])) {
      throw new customException(
        'Field ›' . $field . '‹ does not exist in recordset!'
      );
    }
  }



  public function moveFirst()
  {
    if ($this->totalRows > 0) {
      $this->currentRow = 0;
      $this->EOF = false;
    }
  }



  public function moveNext()
  {
    if ($this->currentRow == $this->totalRows - 1) {
      $this->EOF = true;
    }

    if (!$this->EOF) {
      $this->currentRow++;
    }
  }



  public function moveTo(int $row)
  {
    if ($row < 0) {
      throw new customException('Can not move to negative row!');
    }

    if ($row > $this->totalRows - 1) {
      throw new customException(
        'Can not move above highest row!
        <br>Requested row: ' . $row
          . '<br>Total rows: ' . $this->totalRows
      );

      $this->currentRow = $row;
    }
  }



  public function moveLast()
  {
    if ($this->totalRows > 0) {
      $this->currentRow = $this->totalRows - 1;
    }
  }



  public function getTotalRows()
  {
    return $this->totalRows;
  }



  public function getMin()
  {
    return $this->min ?? false;
  }



  public function getMax()
  {
    return $this->max ?? false;
  }



  public function getEOF()
  {
    return $this->EOF;
  }



  public function getRecordset()
  {
    return $this->recordset;
  }



  public function getField(string $field)
  {
    Utils::checkNotEmpty($field, 'field');

    if ($this->EOF) {
      throw new customException(
        'EOF true - can not retrieve field ›' . $field . '‹'
      );
    }

    $this->checkFieldExists($field);

    return $this->recordset[$this->currentRow][$field];
  }



  public function findRows(string $field, string $content)
  {
    Utils::checkNotEmpty($field, 'field');
    Utils::checkNotEmpty($content, 'content');
    $this->checkFieldExists($field);

    $rememberCurrentRow = $this->currentRow;
    $rememberEOF = $this->EOF;
    $resultRows = false;

    $this->moveFirst();

    while (!$this->EOF) {
      if ($this->recordset[$this->currentRow][$field] == $content) {
        $resultRows[] = $this->currentRow;
      }
      $this->moveNext();
    }

    $this->currentRow = $rememberCurrentRow;
    $this->EOF = $rememberEOF;

    return $resultRows;
  }
}
