<?php

class Check {

  public static function empty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for ›' . $label  . '‹ is empty!');
    }
  }
}
?>
