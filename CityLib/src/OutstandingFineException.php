<?php 
declare(strict_types=1);

namespace App;

class OutstandingFineException extends \Exception  
{
  public function __construct() 
  {
    parent::__construct('Member has unpaid fines.');
  }
}
?>