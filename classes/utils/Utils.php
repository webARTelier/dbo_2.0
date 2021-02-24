<?php

class Utils
{

  public static function relay(Ea $ea, string $nextPage)
  {
    $_SESSION['ea'] = serialize($ea);
    exit(header('Location: ' . $nextPage));
  }



  public static function checkNotEmpty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for ›' . $label  . '‹ is empty!');
    }
  }



  public static function clearArrayValues(array $array)
  {
    foreach ($array as $arrayKey => $arrayValue) {

      is_array($arrayValue)
        ? Utils::clearArrayValues($arrayValue)
        : $array[$arrayKey] = '';

    }

    return $array;
  }



  public static function createPrefillData(array $data)
  {
    $prefill = '<script>';
    $prefill .= 'var prefillData = {';

    $prefix = '';

    foreach ($data as $inputname => $inputvalue) {
      $prefill .= $prefix . "'" . $inputname . "':'" . $inputvalue . "'";
      $prefix = ', ';
    }

    $prefill .= '};';
    $prefill .= '</script>';

    return $prefill;
  }
}
?>
