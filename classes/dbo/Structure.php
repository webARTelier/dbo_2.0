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



  // -------------------------------------------------------------------



  public function check_empty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for ›' . $label  . '‹ is empty!');
    }
  }



  public function check_table(string $table)
  {
    if (!in_array($table, $this->tables)) {
      throw new customException('Table ›' . $table . '‹ does not exist!');
    }
  }



  public function check_columns(string $table, array $queryColumns)
  {
    $dbColumns = array_column(mysqli_fetch_all($this->conn->query("SHOW COLUMNS FROM $table")), 0);

    foreach ($queryColumns as $queryColumn) {
      if (!in_array($queryColumn, $dbColumns) && $queryColumn != '*') {
        throw new customException('Column ›' . $queryColumn . '‹ does not exist in table ›' . $table .'‹!');
      }
    }
  }



  // -------------------------------------------------------------------



  public function get_tables() {
    return $this->tables;
  }



  public function get_columns(string $table, bool $ID = true)
  {
    $this->check_empty($table);
    $this->check_table($table);

    $columns = array_column(mysqli_fetch_all($this->conn->query("SHOW COLUMNS FROM $table")), 0);

    if(!$ID) {
      unset($columns['ID']);
    }

    return $columns;
  }
}

?>
