<?php 

declare(strict_types=1);

namespace App;

class Book extends LibraryItem implements Borrowable
{
  private int $pages;

  public function __construct(string $itemId, string $title, int $pages) 
  {
    parent::__construct($itemId, $title);

    $this->pages = $pages;

  }

  public function getDetails(): string 
  {
    return "[{$this->getItemId()}] {$this->getTitle()} ({$this->pages} pages)";
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