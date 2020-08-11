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
    ->set_cols('Code, Region')
    ->set_cond('Code != ?', 'test')
    ->set_order('Code DESC');
  $rs_test->execute('select');

  print_r($rs_test->find_rows('Region', 'Middle East'));




  echo '<pre>';
  print_r($rs_test);
  echo '</pre>';




} catch (customException $e) {
  echo $e->errorMessage();
}

?>
