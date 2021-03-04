<?php

class Conn
{
  public $connDB = '';



  function __construct($db_path, $db_user, $db_pass, $db_name)
  {
    try {
      $this->connDB = new mysqli($db_path, $db_user, $db_pass, $db_name);
    } catch (Exception $e) {
      exit(
        'Could not establish DB connection. PHP says:<br>' . $e->getMessage()
      );
    }

    $this->connDB->set_charset('utf8');
  }
}
