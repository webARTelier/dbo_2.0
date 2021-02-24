<?php

class Query
{
  public $structure = '';

  private $table =  '';
  private $cols = '';
  private $colsAsArray = [];
  private $innerJoin = '';
  private $leftJoin = '';
  private $cond_placeholder = '';
  private $cond_value = '';
  private $and_placeholder = '';
  private $and_value = [];
  private $or_placeholder = '';
  private $or_value = [];
  private $groupBy = '';
  private $orderBy = '';
  private $limit = '';
  private $offset = '';
  private $valTypes = '';



  function __construct(Structure $structure)
  {
    $this->structure = $structure;
  }



  // -------------------------------------------------------------------



  private function checkQuery()
  {
    if (empty($this->table)) {
      throw new customException('No table set for query!');
    }

    if (empty($this->cols)) {
      throw new customException('No columns set for query!');
    }
  }



  // -------------------------------------------------------------------



  public function setTable(string $table)
  {
    Utils::checkNotEmpty($table, 'table');
    $this->structure->checkTableExists($table);
    $this->table = $table;

    return $this;
  }



  public function setCols(string $cols)
  {
    if (empty($this->table)) {
      throw new customException('No table set - set table before columns!');
    }

    Utils::checkNotEmpty($cols, 'columns');
    $this->colsAsArray = explode(',', str_replace(' ', '', $cols));
    $this->structure->checkColumnsExist($this->table, $this->colsAsArray);
    $this->cols = $cols;

    return $this;
  }



  public function setinnerJoin(string $table, string $on)
  {
    Utils::checkNotEmpty($table, 'table');
    Utils::checkNotEmpty($on, 'on');
    $this->structure->checkTableExists($table);
    $this->innerJoin .= ' INNER JOIN ' . $table . ' ON ' . $on;

    return $this;
  }



  public function setleftJoin(string $table, string $on)
  {
    Utils::checkNotEmpty($table, 'table');
    Utils::checkNotEmpty($on, 'on');
    $this->structure->checkTableExists($table);
    $this->leftJoin .= ' LEFT JOIN ' . $table . ' ON ' . $on;

    return $this;
  }



  public function setCondition(string $placeholder, string $value)
  {
    Utils::checkNotEmpty($placeholder, 'placeholder');
    $this->cond_placeholder = " WHERE " . $placeholder;
    $this->cond_value = $value;
    $this->valTypes .= 's';

    return $this;
  }



  public function setAnd(string $placeholder, string $value)
  {
    Utils::checkNotEmpty($placeholder, 'placeholder');
    $this->and_placeholder .= " AND " . $placeholder;
    $this->and_value[] = $value;
    $this->valTypes .= 's';

    return $this;
  }



  public function setOr(string $placeholder, string $value)
  {
    Utils::checkNotEmpty($placeholder, 'placeholder');
    $this->or_placeholder .= " OR " . $placeholder;
    $this->or_value[] .= $value;
    $this->valTypes .= 's';

    return $this;
  }



  public function setgroupBy(string $groupBy)
  {
    Utils::checkNotEmpty($groupBy, 'groupBy');
    $this->groupBy = " GROUP BY " . $groupBy;

    return $this;
  }



  public function setOrder(string $order)
  {
    Utils::checkNotEmpty($order, 'order');
    $this->orderBy = " ORDER BY " . $order;

    return $this;
  }



  public function setLimit(int $limit)
  {
    Utils::checkNotEmpty($limit, 'limit');
    $this->limit = " LIMIT " . $limit;

    return $this;
  }



  public function setOffset(int $offset)
  {
    Utils::checkNotEmpty($offset, 'offset');
    $this->offset = " OFFSET " . $offset;

    return $this;
  }



  // -------------------------------------------------------------------



  public function getQuery(string $mode)
  {
    Utils::checkNotEmpty($mode, 'mode');
    $this->checkQuery();

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
      $this->innerJoin
      $this->leftJoin
      $this->cond_placeholder
      $this->and_placeholder
      $this->or_placeholder
    ";

    try {
      $methodName = 'getQuery' . (ucfirst(strtolower($mode)));
      $statement = $this->{$methodName}($statementParams);
    } catch (customException $e) {
      throw new customException($mode . ' is not a selection mode!');
    }

    return array(
      'statement'   => $statement,
      'valTypes'    => $this->valTypes,
      'values'      => $bind_values
    );
  }



  private function getQueryCount(string $statementParams)
  {
    return "SELECT COUNT(*) as rowCount FROM $statementParams";
  }



  private function getQueryMin(string $statementParams)
  {
    if (count($this->colsAsArray) > 1) {
      throw new customException('Too many columns (exactly 1 expected)');
    }

    return "SELECT MIN($this->cols) AS min FROM $statementParams";
  }



  private function getQueryMax(string $statementParams)
  {
    if (count($this->colsAsArray) > 1) {
      throw new customException('Too many columns (exactly 1 expected)');
    }

    return "SELECT MAX($this->cols) AS max FROM $statementParams";
  }



  private function getQuerySelect(string $statementParams)
  {
    $statementParams .= "
          $this->groupBy
          $this->orderBy
          $this->limit
          $this->offset
        ";

    return "SELECT $this->cols FROM $statementParams";
  }
}

?>
