<?php

include 'inc/config.inc.php';
include 'inc/autoloader.php';



try {
  $dbo = new DBO($config['db_access']);

  $rs_test = $dbo->new_recordset();
  $rs_test->query
    ->set_table('country')
    ->set_cols('Code, Region')
    ->set_cond('Code != ?', 'test')
    ->set_order('Code ASC');
  $rs_test->execute('select');



  echo '<pre>';
  print_r($dbo);
  echo '</pre>';
  die;






} catch (customException $e) {
  echo $e->errorMessage();
}

?>
