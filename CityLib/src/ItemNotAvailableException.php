<?php
declare(strict_types=1);

namespace App;

class ItemNotAvailableException extends \Exception 
{
  public function __construct() 
  {
    parent::__construct('Item is not available.');
  }
}
?>