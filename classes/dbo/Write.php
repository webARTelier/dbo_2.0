<?php

class Write
{
  private $structure;

  private $insert_valtypes = '';
  private $insert_fields = '';
  private $insert_values = [];
  private $insert_placeholders = '';
  private $insert_lastID = '';

  private $update_valtypes = '';
  private $update_values = [];
  private $update_placeholders = '';

  private $affectedRows = '';



  function __construct(Structure $structure)
  {
    $this->structure = $structure;
    $this->conn = $this->structure->conn;
  }



  // -------------------------------------------------------------------



  private function reset_insert()
  {
    $this->insert_valtypes = '';
    $this->insert_fields = '';
    $this->insert_values = [];
    $this->insert_placeholders = '';
  }



  private function reset_update()
  {
    $this->update_valtypes = '';
    $this->update_values = [];
    $this->update_placeholders = '';
  }



  private function xtract_from_array($method, $data)
  {
    $prefix = '';

    foreach ($data as $fieldname => $fieldvalue) {

      if ($method == 'insert') {
        $this->insert_fields .= $prefix . $fieldname;
        $this->insert_placeholders .= $prefix . '?';
        $this->insert_values[] = $fieldvalue;

        if (is_string($fieldvalue)) {
          $this->insert_valtypes .= 's';
        } elseif (is_int($fieldvalue)) {
          $this->insert_valtypes .= 'i';
        } elseif (is_float($fieldvalue)) {
          $this->insert_valtypes .= 'd';
        } else {
          throw new customException('Unknown data format for inserting: ' . $fieldvalue);
        }
      }

      else {
        $this->update_placeholders .= $prefix . $fieldname . "= ?";
        $this->update_values[] = $fieldvalue;

        if (is_string($fieldvalue)) {
          $this->update_valtypes .= 's';
        } elseif (is_int($fieldvalue)) {
          $this->update_valtypes .= 'i';
        } elseif (is_float($fieldvalue)) {
          $this->update_valtypes .= 'd';
        } else {
          throw new customException('Unknown data format for updating: '.$fieldvalue);
        }
      }

      $prefix = ', ';
    }
  }



  // -------------------------------------------------------------------



  public function store(array $data, string $table)
  {
    Check::empty($data, 'data array');
    Check::empty($table, 'table');
    $this->structure->check_table($table);
    $this->structure->check_columns($table, array_keys($data));

    if (!empty($data['ID'])) {
      $this->update($data, $table, 'ID', $data['ID']);
    } else {
      $this->insert($data, $table);
    }
  }



  public function insert($data, string $table)
  {
    Check::empty($data, 'data array');
    Check::empty($table, 'table');
    $this->structure->check_table($table);
    $this->structure->check_columns($table, array_keys($data));

    $this->reset_insert();
    $this->xtract_from_array('insert', $data);

    $insert = $this->conn->prepare("
      INSERT INTO $table ($this->insert_fields)
      VALUES ($this->insert_placeholders)
    ");

    $insert->bind_param($this->insert_valtypes, ...$this->insert_values);
    $insert->execute();

    $this->affectedRows = $insert->affected_rows;
    $this->insert_lastID = $insert->insert_id;
  }



  public function update(
    array $data,
    string $table,
    string $condition_column,
    string $condition_value)
  {
    Check::empty($data, 'data array');
    Check::empty($table, 'table');
    Check::empty($condition_column, 'condition column');
    Check::empty($condition_value, 'condition value');
    $this->structure->check_table($table);
    $this->structure->check_columns($table, array_keys($data));

    $this->reset_update();
    $this->xtract_from_array('update', $data);

    $update = $this->conn->prepare("
      UPDATE $table
      SET $this->update_placeholders
      WHERE $condition_column = ?
    ");

    $this->update_valtypes .= 's';  // one more for condition value
    $this->update_values[] = $condition_value;
    $update->bind_param($this->update_valtypes, ...$this->update_values);
    $update->execute();

    $this->affectedRows = $update->affected_rows;
  }



  public function delete_row(string $table, int $ID)
  {
    Check::empty($table, 'table');
    $this->structure->check_table($table);
    Check::empty($ID, 'ID');

    $delete = $this->conn->prepare("
      DELETE FROM $table
      WHERE ID = ?
    ");

    $delete->bind_param("i", $ID);
    $delete->execute();

    $this->affectedRows = $delete->affected_rows;
  }



  // -------------------------------------------------------------------



  public function get_affected()
  {
    return $this->affectedRows;
  }



  public function get_lastID()
  {
    return $this->insert_lastID;
  }
}

?>
