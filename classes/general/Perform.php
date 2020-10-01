<?php

class Perform {

  public static function check_empty($value, string $label)
  {
    if (empty($value)) {
      throw new customException('Value for ›' . $label  . '‹ is empty!');
    }
  }
}
?>
