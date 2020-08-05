<?php

class Structure
{
  private $tables = [];
  private $columns = [];



  function __construct(Conn $conn)
  {
    $this->tables = array_column(mysqli_fetch_all($conn->connDB->query('SHOW TABLES')), 0);

    foreach ($this->tables as $table) {
      $this->columns[$table] = array_column(mysqli_fetch_all($conn->connDB->query("SHOW COLUMNS FROM $table")), 0);
    }
  }



  public function get_tables() {
    return $this->tables;
  }



  public function get_columns(string $table, bool $ID = true)
  {
    if (empty($table)) {
      throw new customException('Value for table is empty!');
    }

    if (array_key_exists($table, $this->columns)) {
      $columns = $this->columns[$table];

      if (!$ID && in_array('ID', $columns)) {
        unset($columns[array_search('ID', $columns)]);
      }

      return $columns;

    } else {
      throw new customException('Table »' . $table . '« does not exist!');
    }
  }
}

?>
