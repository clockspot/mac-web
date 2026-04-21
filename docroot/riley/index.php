<?php
$thispage  = 'Riley';
$titledesc = 'Riley';
$docroot   = '../';
$projroot  = '../../';
require $projroot.'settings.php';
require $projroot.'App.class.php';

$table = 'riley';

//Key: column from SQL table to display and (except for datetime) submit back
//Value: header in html table
$fields = [
  "datetime" => "Date/Time",
  "bglevel" => "BG<br><small>mmol/L</small>",
  "insulin" => "Insulin<br><small>units</small>",
  "gabapentin" => "Gabapentin<br><small>mg</small>",
  "allergy" => "Allergy<br><small>mg</small>",
  "inhaler" => "Inhaler<br><small>sec</small>",
  "note" => "Note"
];

// Browse filter values (defaults: from one week ago, to open-ended)
$browseFrom = date('Y-m-d', strtotime('-1 week'));
$browseTo   = date('Y-m-d');



$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
  if ($_POST['form_action'] === 'add') {
    try {
      $app = new App();
      $app->logAdd($table, $_POST);
      header('Location: '.$docroot.'riley/?added');
      exit;
    } catch (Exception $e) {
      $errors[] = $e->getMessage();
    }
  } else { // browse
    $browseFrom = isset($_POST['from']) ? trim($_POST['from']) : $browseFrom;
    $browseTo   = isset($_POST['to'])   ? trim($_POST['to'])   : $browseTo;
  }
}

$browseRows = null;
try {
  $app = isset($app) ? $app : new App();
  $browseRows = $app->logBrowse($table, $browseFrom, $browseTo);
} catch (Exception $e) {
  $errors[] = $e->getMessage();
}

include $docroot.'resources/template-header.php';
?>

<?php if (isset($_GET['added'])): ?>
<p class="success">Entry added.</p>
<?php endif; ?>

<?php if ($errors): ?>
<ul class="errors">
  <?php foreach ($errors as $err): ?>
    <li><?php echo htmlspecialchars($err); ?></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="form_action" value="browse">
  <p>
    From: <input type="text" name="from" value="<?php echo htmlspecialchars($browseFrom); ?>" size="10" />
    &nbsp;To: <input type="text" name="to" value="<?php echo htmlspecialchars($browseTo); ?>" size="10" />
    &nbsp;<input type="submit" value="Browse" />
    <?php $cnt = is_array($browseRows) ? count($browseRows) : 0; ?>
    &nbsp;<?php echo $cnt; ?> entr<?php echo $cnt === 1 ? 'y' : 'ies'; ?> found.
  </p>
  
</form>

<form method="post" action="">
  <input type="hidden" name="form_action" value="add">
  <input type="hidden" name="browse_from" value="<?php echo htmlspecialchars($browseFrom); ?>">
  <input type="hidden" name="browse_to"   value="<?php echo htmlspecialchars($browseTo); ?>">
  <table border=0 cellpadding=0 cellspacing=3 style="width: 100%;">
    <thead>
      <tr>
        <?php foreach($fields as $k=>$v) echo "<th style='font-family: Geneva, sans-serif; font-size: 10pt; text-align: center;' valign='top'>".$v."</th>"; ?>
      </tr>
    </thead>
    <tbody>
      <?php
        if ($browseRows !== null && is_array($browseRows) && count($browseRows) > 0) {
          foreach ($browseRows as $i => $row) {
            echo "<tr>";
            foreach($fields as $k=>$v) {
              if($k=='datetime') {
                $d = new DateTime($row[$k]); $row[$k] = str_replace(" ","&nbsp;",$d->format('D Mj h:i'));
              } else {
                $row[$k] = htmlspecialchars(($row[$k]!==null? $row[$k]: ''));
              }
              echo "<td style='font-family: Geneva, sans-serif; font-size: 10pt; text-align: ".($k=='note'? 'left': 'right').";' valign='top'>".$row[$k]."</td>";
            }
            echo "</tr>";
          }
        }
        echo "<tr>";
        foreach($fields as $k=>$v) echo "<td style='font-family: Geneva, sans-serif; font-size: 10pt; text-align: ".($k=='note'? 'left': 'right').";' valign='top'>".($k=='datetime'? '<input type="submit" value="Add" />': '<input type="text" name="'.htmlspecialchars($k).'" value="'.htmlspecialchars($_POST[$k] ?? '').'" size="7" align="'.($k=='note'? 'left': 'right').'" />')."</td>";
        echo "</tr>";
      ?>
    </tbody>
  </table>
</form>

<?php include $docroot.'resources/template-footer.php'; ?>