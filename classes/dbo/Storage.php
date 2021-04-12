<?php

class Storage
{
  private $structure;

  private $insert_valTypes = '';
  private $insert_fields = '';
  private $insert_values = [];
  private $insert_placeholders = '';
  private $insert_lastID = null;

  private $update_valTypes = '';
  private $update_values = [];
  private $update_placeholders = '';

  private $affectedRows = null;



  function __construct(Structure $structure)
  {
    $this->structure = $structure;
    $this->conn = $this->structure->conn;
  }



  private function resetInsert()
  {
    $this->insert_valTypes = '';
    $this->insert_fields = '';
    $this->insert_values = [];
    $this->insert_placeholders = '';
  }



  private function resetUpdate()
  {
    $this->update_valTypes = '';
    $this->update_values = [];
    $this->update_placeholders = '';
  }



  private function xtractInsert(array $data)
  {
    $prefix = '';

    foreach ($data as $fieldName => $fieldValue) {
      $this->insert_fields .= $prefix . $fieldName;
      $this->insert_placeholders .= $prefix . '?';
      $this->insert_values[] = $fieldValue;
      $this->insert_valTypes .= $this->getDataType($fieldValue);
      $prefix = ',';
    }
  }



  private function xtractUpdate(array $data)
  {
    $prefix = '';

    foreach ($data as $fieldName => $fieldValue) {
      $this->update_placeholders .= $prefix . $fieldName . "= ?";
      $this->update_values[] = $fieldValue;
      $this->update_valTypes .= $this->getDataType($fieldValue);
      $prefix = ',';
    }
  }



  private function getDataType($data)
  {
    $dataTypes = array(
      'integer' => 'i',
      'float'   => 'd',
      'string'  => 's'
    );

    if (key_exists(gettype($data), $dataTypes)) {
      return ($dataTypes[gettype($data)]);
    }

    throw new customException('Unknown data type!');
  }



  public function store(array $data, string $table)
  {
    Utils::checkNotEmpty($data, 'data array');
    Utils::checkNotEmpty($table, 'table');
    $this->structure->checkTableExists($table);
    $this->structure->checkColumnsExist($table, array_keys($data));

    if (!empty($data['ID'])) {
      $this->update($data, $table, 'ID', $data['ID']);
      return true;
    }

    $this->insert($data, $table);
    return true;
  }



  public function insert($data, string $table)
  {
    Utils::checkNotEmpty($data, 'data array');
    Utils::checkNotEmpty($table, 'table');
    $this->structure->checkTableExists($table);
    $this->structure->checkColumnsExist($table, array_keys($data));

    $this->resetInsert();
    $this->xtractInsert($data);

    $insert = $this->conn->prepare("
      INSERT INTO $table ($this->insert_fields)
      VALUES ($this->insert_placeholders)
    ");

    $insert->bind_param($this->insert_valTypes, ...$this->insert_values);
    $insert->execute();

    $this->affectedRows = $insert->affected_rows;
    $this->insert_lastID = $insert->insert_id;
  }



  public function update(
    array $data,
    string $table,
    string $condition_column,
    string $condition_value
  ) {
    Utils::checkNotEmpty($data, 'data array');
    Utils::checkNotEmpty($table, 'table');
    Utils::checkNotEmpty($condition_column, 'condition column');
    Utils::checkNotEmpty($condition_value, 'condition value');
    $this->structure->checkTableExists($table);
    $this->structure->checkColumnsExist($table, array_keys($data));

    $this->resetUpdate();
    $this->xtractUpdate($data);

    $update = $this->conn->prepare("
      UPDATE $table
      SET $this->update_placeholders
      WHERE $condition_column = ?
    ");

    $this->update_valTypes .= 's';  // one more for condition value
    $this->update_values[] = $condition_value;
    $update->bind_param($this->update_valTypes, ...$this->update_values);
    $update->execute();

    $this->affectedRows = $update->affected_rows;
  }



  public function deleteRow(string $table, int $ID)
  {
    Utils::checkNotEmpty($table, 'table');
    Utils::checkNotEmpty($ID, 'ID');
    $this->structure->checkTableExists($table);

    $delete = $this->conn->prepare("
      DELETE FROM $table
      WHERE ID = ?
    ");

    $delete->bind_param("i", $ID);
    $delete->execute();

    $this->affectedRows = $delete->affected_rows;
  }



  public function getAffectedRows()
  {
    return $this->affectedRows ?? false;
  }



  public function getLastID()
  {
    return $this->insert_lastID ?? false;
  }
}
