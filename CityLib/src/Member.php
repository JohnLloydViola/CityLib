<?php 
declare(strict_types=1);

namespace App;

class Member 
{
  private int $memberId;
  private string $name;
  private float $outstandingFines = 0.0;
  private array $borrowedItems = [];

  public function __construct(int $memberId, string $name)
  {
    $this->memberId = $memberId;
    $this->name = $name;
  }

  public function getName(): string 
  {
    return $this->name;
  }

  public function getMemberId(): int 
  {
    return $this->memberId;
  }

  public function getBorrowedItems(): array
  {
    return $this->borrowedItems;
  }

  public function payFine(float $amount): void 
  {
    if ($amount > 0) 
    {
      $this->outstandingFines -= $amount;
    }
  }

  public function getOutstandingFines(): float 
  {
    return $this->outstandingFines;
  }

  public function borrowItem(LibraryItem $item): void 
  {
  if ($this->outstandingFines > 0) 
  {
    throw new OutstandingFineException();
  }

  if (!$item->isAvailable()) 
  {
    throw new ItemNotAvailableException();
  }
    
  $item->borrow();

  $this->borrowedItems[] = $item;
  }

  public function returnItem(LibraryItem $item): void
  {
    foreach ($this->borrowedItems as $key => $borrowedItem) 
    {
      if ($borrowedItem->getItemId() === $item->getItemId()) 
      {
        unset($this->borrowedItems[$key]);

        $item->returnItem();

        $this->borrowedItems = array_values($this->borrowedItems);
        return;
      }
    }
  }

  public function assessFine(float $amount): void
  {
    if ($amount > 0)
    {
      $this->outstandingFines += $amount;
    }
  }
}
?>