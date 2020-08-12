<?php

class Query
{
  public $structure = '';

  private $table =  '';
  private $cols = '';
  private $colsAsArray = [];
  private $innerjoin = '';
  private $leftjoin = '';
  private $cond_placeholder = '';
  private $cond_value = '';
  private $and_placeholder = '';
  private $and_value = [];
  private $or_placeholder = '';
  private $or_value = [];
  private $groupby = '';
  private $orderby = '';
  private $limit = '';
  private $offset = '';
  private $valTypes = '';



  function __construct(Structure $structure) {
    $this->structure = $structure;
  }



  // -------------------------------------------------------------------



  private function check_empty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for »' . $label  . '« is empty!');
    }
  }



  private function check_query()
  {
    if (empty($this->table)) {
      throw new customException('No table set for query!');
    }

    if (empty($this->cols)) {
      throw new customException('No columns set for query!');
    }
  }



  // -------------------------------------------------------------------



  public function set_table(string $table)
  {
    $this->check_empty($table, 'table');
    $this->structure->check_table($table);
    $this->table = $table;
    return $this;
  }



  public function set_cols(string $cols)
  {
    if (empty($this->table)) {
      throw new customException('No table set - set table before columns!');
    }

    $this->check_empty($cols, 'columns');
    $this->colsAsArray = explode(',', str_replace(' ', '', $cols));
    $this->structure->check_columns($this->table, $this->colsAsArray);
    $this->cols = $cols;
    return $this;
  }



  public function set_innerjoin(string $table, string $on)
  {
    $this->check_empty($table, 'table');
    $this->structure->check_table($table);
    $this->check_empty($on, 'on');
    $this->innerjoin .= ' INNER JOIN ' . $table . ' ON ' . $on;
    return $this;
  }



  public function set_leftjoin(string $table, string $on)
  {
    $this->check_empty($table, 'table');
    $this->structure->check_table($table);
    $this->check_empty($on, 'on');
    $this->leftjoin .= ' LEFT JOIN ' . $table . ' ON ' . $on;
    return $this;
  }



  public function set_cond(string $placeholder, string $value)
  {
    $this->check_empty($placeholder, 'placeholder');
    $this->check_empty($value, 'value');
    $this->cond_placeholder = " WHERE " . $placeholder;
    $this->cond_value = $value;
    $this->valTypes .= 's';
    return $this;
  }



  public function set_and(string $placeholder, string $value)
  {
    $this->check_empty($placeholder, 'placeholder');
    $this->check_empty($value, 'value');
    $this->and_placeholder = " AND " . $placeholder;
    $this->and_value[] = $value;
    $this->valTypes .= 's';
    return $this;
  }



  public function set_or(string $placeholder, string $value)
  {
    $this->check_empty($placeholder, 'placeholder');
    $this->check_empty($value, 'value');
    $this->or_placeholder .= " OR " . $placeholder;
    $this->or_value[] = $value;
    $this->valTypes .= 's';
    return $this;
  }



  public function set_groupby(string $groupby)
  {
    $this->check_empty($groupby, 'groupby');
    $this->groupby = " GROUP BY " . $groupby;
    return $this;
  }



  public function set_order(string $order)
  {
    $this->check_empty($order, 'order');
    $this->orderby = " ORDER BY " . $order;
    return $this;
  }



  public function set_limit(string $limit)
  {
    $this->check_empty($limit, 'limit');
    $this->limit = " LIMIT " . $limit;
    return $this;
  }



  public function set_offset(string $offset)
  {
    $this->check_empty($offset, 'offset');
    $this->limit = " OFFSET " . $offset;
    return $this;
  }



  // -------------------------------------------------------------------



  public function get_query(string $mode)
  {
    $this->check_empty($mode, 'mode');
    $this->check_query();

    $bind_values = [];
    $bind_values[] = $this->cond_value;

    foreach ($this->and_value as $and_value) {
      $bind_values[] = $and_value;
    }

    foreach ($this->or_value as $or_value) {
      $bind_values[] = $or_value;
    }

    $statementParams = "
      $this->table
      $this->innerjoin
      $this->leftjoin
      $this->cond_placeholder
      $this->and_placeholder
      $this->or_placeholder
    ";

    switch ($mode) {

      case 'count':
        $statement = "SELECT COUNT(*) as rowCount FROM $statementParams";
        break;



      case 'min':
        if (count($this->colsAsArray) > 1) {
          throw new customException('Too many columns (exactly 1 expected)');
        }

        $statement = "SELECT MIN($this->cols) AS min FROM $statementParams";
        break;



      case 'max':
        if (count($this->colsAsArray) > 1) {
          throw new customException('Too many columns (exactly 1 expected)');
        }

        $statement = "SELECT MAX($this->cols) AS max FROM $statementParams";
        break;



      case 'select':
        $statementParams .= "
          $this->groupby
          $this->orderby
          $this->limit
          $this->offset
        ";
        $statement = "SELECT $this->cols FROM $statementParams";
        break;



      default:
        throw new customException('»' . $mode . '« is not a query mode!');
    }

    return array(
      'statement'   => $statement,
      'valTypes'    => $this->valTypes,
      'values'      => $bind_values
    );
  }
}

?>
