<?php

include 'inc/config.inc.php';
include 'classes/general/autoloader.php';



$autoloader = new Autoloader();
$autoloader
  ->set_baseDir('classes')
  ->register();



try {
  $dbo = new Dbo($config['db_access']);



  // get a recordset
  // ---------------
  $rs_test = $dbo->new_recordset();
  $rs_test->query
    ->set_table('country')
    ->set_cols('Code, Region')
    ->set_cond('Code != ?', 'test')
    ->set_order('Code ASC');

  // prepare and init pagination
  // ---------------------------
  !empty($_GET['page'])
    ? $page = intval($_GET['page'])
    : $page = 1;

  $pagination = new Pagination;
  $pagination->set_entriesPerPage(15);

  $rs_test->add_pagination($pagination, $page);
  $rs_test->execute('select');

  echo 'Pagination:' . $rs_test->pagination->get_html();
  echo $rs_test->pagination->get_html_count();



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



} catch (CustomException $e) {
  echo $e->errorMessage();
}

?>
