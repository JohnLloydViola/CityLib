<?php 
declare(strict_types=1);

namespace App;

class Dvd extends LibraryItem implements Borrowable
{
  private int $durationMinutes;

  public function __construct(string $itemId, string $title, int $durationMinutes) 
  {
    parent::__construct($itemId, $title);

    $this->durationMinutes = $durationMinutes;
  }

  public function getDetails(): string 
  {
    return "[{$this->getItemId()}] {$this->getTitle()} ({$this->durationMinutes} min)";
  }

   public function borrow(): void
  {
    if ($this->isAvailable()) 
    {
      $this->setAvailability(false);
    }
  }

  public function returnItem(): void
  {
    $this->setAvailability(true);
  }
}
?>