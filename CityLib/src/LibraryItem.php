<?php 

declare(strict_types=1);

namespace App;

class LibraryItem 
{
  private string $itemId;
  private string $title;
  private bool $isAvailable;

  public function __construct(string $itemId, string $title) 
  {
    $this->itemId = $itemId;
    $this->title = $title;
    $this->isAvailable = true;
  }

  public function getItemId(): string 
  {
    return $this->itemId;
  }

  public function getTitle(): string 
  {
    return $this->title;
  }

  public function isAvailable(): bool 
  {
    return $this->isAvailable;
  }

  protected function setAvailability(bool $isAvailable): void 
  {
    $this->isAvailable = $isAvailable;
  }

  public function getDetails(): string
  {
    return "[{$this->itemId}] {$this->title}";
  }
}
?>