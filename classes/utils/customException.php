<?php

class CustomException extends Exception
{
  public function errorMessage()
  {
    $errorMsg = '<br><strong>' . $this->getMessage() . '</strong><br>'
      . 'Error occured on line ' . $this->getLine()
      . 'in ' . $this->getFile()
      . '<br><br>'
      . "Stack Trace:<pre>" . $this->getTraceAsString() . '</pre>';
    return $errorMsg;
  }
}

?>
