<?php 
declare(strict_types=1);

namespace App;

interface Borrowable 
{
  function borrow(): void;

  function returnItem(): void;
}
?>