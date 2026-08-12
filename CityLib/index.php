<?php 
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use App\Book;
use App\Dvd;
use App\Member;
use App\OutstandingFineException;
use App\ItemNotAvailableException;
  
session_start();

if (!isset($_SESSION['catalog'])) 
{
  $_SESSION['catalog'] = 
  [
    new Book('BK-001', 'Clean Code', 320),
    new Book('BK-002', 'The Pragmatic Programmer', 352),
    new Dvd('DV-001', 'Intro to Networking', 95),
  ];
}

if (!isset($_SESSION['members'])) 
{
  $_SESSION['members'] = 
  [
      new Member(1, 'Ana'),
      new Member(2, 'Ben'),
  ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
  $action = $_POST['action'] ?? '';
    
  if ($action === 'reset')
  {
    unset($_SESSION['catalog']);
    unset($_SESSION['members']);

    header('Location: index.php');
    exit();
  }

  $memberId = (int) $_POST['memberId'];

  foreach ($_SESSION['members'] as $member) 
  {
    if ($member->getMemberId() !== $memberId) 
    {
      continue;
    }

    if ($action === 'payFine') 
    {
      $amount = (float) $_POST['amount'];

      $member->payFine($amount);

      $_SESSION['message'] =
      $member->getName() .
      ' paid P' . number_format($amount, 2) .
      ' in fines. Remaining: P' .
      number_format($member->getOutstandingFines(), 2) . '.';

      $_SESSION['messageType'] = 'success';

      break;
    }

    if ($action === 'assessFine') 
    {
      $member->assessFine(50);

      $_SESSION['message'] =
      'Assessed a P50.00 fine on ' .
      $member->getName() .
      ' (for demo/testing).';

      $_SESSION['messageType'] = 'error';

      break;
    }

    $itemId = $_POST['itemId'];

    foreach ($_SESSION['catalog'] as $item) 
    {
      if ($item->getItemId() !== $itemId) 
      {
        continue;
      }

      if ($action === 'return') 
      {
        $member->returnItem($item);

        $_SESSION['message'] =
          $member->getName() .
          ' returned "' .
          $item->getTitle() .
          '".';

        $_SESSION['messageType'] = 'success';
      }
      else
      {
        try 
        {
          $member->borrowItem($item);

          $_SESSION['message'] = 'Item borrowed successfully.';

          $_SESSION['messageType'] = 'success';
        } 
        catch (ItemNotAvailableException $exception) 
        {
          $_SESSION['message'] = $exception->getMessage();

          $_SESSION['messageType'] = 'error';
        } 
        catch (OutstandingFineException $exception) 
        {
          $_SESSION['message'] = $exception->getMessage();

          $_SESSION['messageType'] = 'error';
        }
      }

      break;
    }
    break;
  }
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <link rel="stylesheet" href="./style.css">
  </head>
  <body>
    <header> 
      <div>
        <h1 class="header-title">CityLib</h1>
        <form method="POST" action="index.php">
          <button type="submit" name="action" value="reset" class="btn">
            Reset Demo
          </button>
        </form>
      </div>
    </header>

    <div class="message <?php echo $_SESSION['messageType'] ?? ''; ?>">
      <?php if (!empty($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; ?></p>

        <?php unset($_SESSION['message'], $_SESSION['messageType']); ?>
      <?php endif; ?>
    </div>
    
    <main>
      <div class="catalog">
        <h2 class="title">Catalog</h2> 

        <table>
          <tr>
            <th style="width: 60%;">Item</th>
            <th>Status</th>
            <th>Borrow</th>
          </tr>

          <?php foreach($_SESSION['catalog'] as $item): ?>
            <tr>
              <td> <?php echo $item->getDetails(); ?> </td>
              <td> <?php echo $item->isAvailable() ? '<p class="available">Available</p>' : '<p class="checked-out">Checked out</p>'; ?> </td>
              <td> 
                <form method="POST" action="index.php">
                  <input type="hidden" 
                  name="itemId" 
                  value="<?php echo $item->getItemId(); ?>"
                  >

                  <select name="memberId">
                    <?php foreach($_SESSION['members'] as $member): ?>
                      <option value="<?php echo $member->getMemberId()?>"> <?php echo $member->getName() ?> </option>
                    <?php endforeach ?>
                  </select>

                  <button type="submit" 
                  name="action" value="borrow" 
                  class="btn" 
                  <?php echo !$item->isAvailable() ? 'disabled' : ''; ?>> 
                    Borrow 
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>

      <div class="members">
        <h2 class="title">Members</h2> 

        <table>
          <tr>
            <th>Name</th>
            <th>Outstanding fines</th>
            <th style="width: 30%;">Borrowed items</th>
            <th style="width: 40%;">Pay fine</th>
          </tr>

          <?php foreach($_SESSION['members'] as $member): ?>
            <tr>
              <td> <?php echo $member->getName(); ?> </td>
              <td> <?php echo 'P' . number_format($member->getOutstandingFines(), 2); ?> </td>
              
              <td> 
                <?php foreach($member->getBorrowedItems() as $item): ?>
                    <div class="borrowed-items">
                      <?php echo $item->getTitle(); ?>

                      <form method="POST" action="index.php">
                          <input type="hidden" name="action" value="return">
                          <input
                            type="hidden"
                            name="itemId"
                            value="<?php echo $item->getItemId(); ?>"
                          >

                           <input
                              type="hidden"
                              name="memberId"
                              value="<?php echo $member->getMemberId(); ?>"
                            >

                          <button type="submit" class="return-btn">Return</button>
                      </form>
                    </div>
                <?php endforeach ?>
              </td>

              <td>
                <div>
                   <form method="POST" action="index.php">
                    <input
                      type="hidden"
                      name="memberId"
                      value="<?php echo $member->getMemberId(); ?>"
                    >

                    <input
                      type="text"
                      name="amount"
                    >

                    <button type="submit" 
                    name="action" 
                    value="payFine" 
                    class="pay-btn" 
                    <?php echo $member->getOutstandingFines() <= 0 ? 'disabled' : ''; ?>>
                      Pay
                    </button>

                    <button type="submit" 
                    name="action" 
                    value="assessFine"
                    class="btn">
                      + Assess P50 Fine
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
        </table>
      </div>
    </main>
  </body>
</html>