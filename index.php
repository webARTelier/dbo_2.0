<?php

include 'inc/config.inc.php';
include 'inc/autoloader.php';



try {
  $dbo = new Dbo($config['db_access']);



  // get a recordset
  // ---------------
  $rs_test = $dbo->new_recordset();
  $rs_test->query
    ->set_table('country')
    ->set_cols('Code, Region')
    ->set_cond('Code != ?', 'test')
    ->set_order('Code ASC')
    ->set_limit(10);
  $rs_test->execute('select');



  // -------------------------------------------------------------------



  // write into db
  // -------------
  $insertData = array(
    array('city' => 'Äteritsiputeritsipuolilautatsijänkä',  'country' => 'Finland'),
    array('city' => 'Pekwachnamaykoskwaskwaypinwanik',      'country' => 'Canada'),
    array('city' => 'Venkatanarasimharajuvaripeta',         'country' => 'India'),
    array('city' => 'Bovenendvankeelafsnysleegte',          'country' => 'South Africa'),
    array('city' => 'Mamungkukumpurangkuntjunya',           'country' => 'Australia')
  );

  $updateData = array('city' => 'Göttingen', 'country' => 'Deutschland');
  $storeData = array('ID' => 5, 'city' => 'Flensburg', 'country' => 'Deutschland');



  $write = $dbo->new_write();

  foreach ($insertData as $row => $data) {
    $write->store($data, 'test_write');
  }

  $write->update($updateData, 'test_write', 'ID', '4');
  $write->store($storeData, 'test_write');



} catch (customException $e) {
  echo $e->errorMessage();
}

?>
