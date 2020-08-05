<?php

class customException extends Exception
{
  public function errorMessage()
  {
    $errorMsg = '<strong>' . $this->getMessage() . '</strong><br>';
    $errorMsg .= 'Error occured on line ' . $this->getLine() . ' in ' . $this->getFile() . '<br><br>';
    $errorMsg .= "Stack Trace:<br>" . $this->getTraceAsString();
    return $errorMsg;
  }
}

?>
