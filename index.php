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
    ->set_cond('Code != ?', 'test')
    ->set_order('Code DESC');
  $rs_test->execute('select');



  while (!$rs_test->get_EOF()) {
    echo '<br>' . $rs_test->get_field('Code');
    $rs_test->move_next();
  }



} catch(customException $e) {
  echo $e->errorMessage();
}

?>
