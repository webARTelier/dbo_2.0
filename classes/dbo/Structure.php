<?php

class Structure
{
  public $conn = '';
  private $tables = [];



  function __construct(Conn $conn)
  {
    $this->conn = $conn->connDB;
    $this->tables = array_column(mysqli_fetch_all($this->conn->query('SHOW TABLES')), 0);
  }



  public function checkTableExists(string $table)
  {
    if (!in_array($table, $this->tables)) {
      throw new customException('Table ›' . $table . '‹ does not exist!');
    }
  }



  public function checkColumnsExist(string $table, array $queryColumns)
  {
    $dbColumns = array_column(mysqli_fetch_all($this->conn->query("SHOW COLUMNS FROM $table")), 0);

    foreach ($queryColumns as $queryColumn) {
      if (!in_array($queryColumn, $dbColumns) && $queryColumn != '*') {
        throw new customException('Column ›' . $queryColumn . '‹ does not exist in table ›' . $table . '‹!');
      }
    }
  }



  public function getTables() {
    return $this->tables;
  }



  public function getColumns(string $table)
  {
    Utils::checkNotEmpty($table, 'table name');
    $this->checkTableExists($table);

    $columns = array_column(mysqli_fetch_all($this->conn->query("SHOW COLUMNS FROM $table")), 0);

    return $columns;
  }
}

?>
