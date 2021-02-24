<?php

include 'inc/config.inc.php';
include 'classes/utils/autoloader.php';



$autoloader = new Autoloader('classes');



try {
  $dbo = new Dbo($config['db_access']);



  // get a recordset
  // ---------------
  $rs_test = $dbo->createNewRecordset();
  $rs_test->query
    ->setTable('country')
    ->setCols('Code, Region')
    ->setCondition('Code != ?', 'test')
    ->setOrder('Code ASC');

  // prepare and init pagination
  // ---------------------------
  !empty($_GET['page'])
    ? $page = intval($_GET['page'])
    : $page = 1;

  $pagination = new Pagination;
  $pagination->setEntriesPerPage(15);

  $rs_test->addPagination($pagination, $page);
  $rs_test->execute('select');

  echo 'Pagination:' . $rs_test->pagination->getPaginationHtml();
  echo $rs_test->pagination->getPaginationCountHtml();



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



  $storage = $dbo->createNewStorage();

  foreach ($insertData as $row => $data) {
    $storage->store($data, 'test_write');
  }

  $storage->update($updateData, 'test_write', 'ID', '4');
  $storage->store($storeData, 'test_write');



} catch (CustomException $e) {
  echo $e->errorMessage();
}

?>
