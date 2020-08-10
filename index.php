<?php

include 'inc/config.inc.php';
include 'inc/autoloader.php';



try {
  $conn = new Conn(...$config['db_access']);
  $structure = new Structure($conn);
  $query = new Query($structure);

  $rs_test = new Recordset($conn, $query, 'select');
  $rs_test->query
    ->set_table('country')
    ->set_cols('Code, Name')
    ->set_cond('Code != ?', 'test');
  $rs_test->execute('select');



  //while (!$rs_test->get_EOF()) {}



  echo '<pre>';
  print_r($rs_test);
  echo '</pre>';



} catch(customException $e) {
  echo $e->errorMessage();
}


?>
