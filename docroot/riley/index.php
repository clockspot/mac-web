<?php
$thispage  = 'Riley';
$titledesc = 'Riley';
$docroot   = '../';
$projroot  = '../../';
require $projroot.'settings.php';
require $projroot.'App.class.php';

$addErrors = [];
$browseErrors = [];

// Add form field values (preserved on error)
$fInsulin    = '';
$fGabapentin = '';
$fAllergy    = '';
$fNote       = '';

// Browse filter values (defaults: from one month ago, to open-ended)
$browseFrom = date('Y-m-d', strtotime('-1 month'));
$browseTo   = '';

// -------------------------------------------------------------------------
// Handle POST
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formAction = isset($_POST['form_action']) ? trim($_POST['form_action']) : '';

  if ($formAction === 'add') {
    // Carry browse state through the add form's hidden fields
    $browseFrom = isset($_POST['browse_from']) ? trim($_POST['browse_from']) : $browseFrom;
    $browseTo   = isset($_POST['browse_to'])   ? trim($_POST['browse_to'])   : $browseTo;

    $fInsulin    = isset($_POST['insulin'])    ? trim($_POST['insulin'])    : '';
    $fGabapentin = isset($_POST['gabapentin']) ? trim($_POST['gabapentin']) : '';
    $fAllergy    = isset($_POST['allergy'])    ? trim($_POST['allergy'])    : '';
    $fNote       = isset($_POST['note'])       ? trim($_POST['note'])       : '';

    try {
      $app = new App();
      $app->rileyAdd(
        $fInsulin    !== '' ? $fInsulin    : null,
        $fGabapentin !== '' ? $fGabapentin : null,
        $fAllergy    !== '' ? $fAllergy    : null,
        $fNote       !== '' ? $fNote       : null
      );
      // PRG: redirect clears the add form and avoids duplicate-submit on reload
      header('Location: '.$docroot.'riley/?added=1');
      exit;
    } catch (Exception $e) {
      $addErrors[] = $e->getMessage();
    }

  } else { // browse
    $browseFrom = isset($_POST['from']) ? trim($_POST['from']) : $browseFrom;
    $browseTo   = isset($_POST['to'])   ? trim($_POST['to'])   : $browseTo;
  }
}

// -------------------------------------------------------------------------
// Run browse (always — auto-populates results on GET and after browse POST)
// -------------------------------------------------------------------------
$browseRows = null;
try {
  $app = isset($app) ? $app : new App();
  $browseRows = $app->rileyBrowse($browseFrom, $browseTo);
} catch (Exception $e) {
  $browseErrors[] = $e->getMessage();
}

// -------------------------------------------------------------------------
// Render
// -------------------------------------------------------------------------
include $docroot.'resources/template-header.php';
?>

<?php if (isset($_GET['added'])): ?>
<p class="success">Entry added.</p>
<?php endif; ?>

<h2>Browse</h2>

<?php if ($browseErrors): ?>
<ul class="errors">
  <?php foreach ($browseErrors as $err): ?>
    <li><?php echo htmlspecialchars($err); ?></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="form_action" value="browse">
  <p>
    From: <input type="text" name="from" value="<?php echo htmlspecialchars($browseFrom); ?>" size="10">
    &nbsp;To: <input type="text" name="to" value="<?php echo htmlspecialchars($browseTo); ?>" size="10">
    &nbsp;<input type="submit" value="Browse">
    <br><small>(yyyy-mm-dd; leave To blank for open-ended)</small>
  </p>
</form>

<?php if ($browseRows !== null): ?>
  <?php if (count($browseRows) === 0): ?>
    <p>No entries found.</p>
  <?php else: ?>
    <table class="results">
      <thead>
        <tr>
          <th>Date/Time</th>
          <th>Insulin</th>
          <th>Gabapentin</th>
          <th>Allergy</th>
          <th>Note</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($browseRows as $i => $row): ?>
          <tr<?php echo ($i % 2 === 1 ? ' class="even"' : ''); ?>>
            <td><?php echo htmlspecialchars($row['datetime']); ?></td>
            <td><?php echo $row['insulin']    !== null ? htmlspecialchars($row['insulin'])    : ''; ?></td>
            <td><?php echo $row['gabapentin'] !== null ? htmlspecialchars($row['gabapentin']) : ''; ?></td>
            <td><?php echo $row['allergy']    !== null ? htmlspecialchars($row['allergy'])    : ''; ?></td>
            <td><?php echo htmlspecialchars((string)($row['note'] ?? '')); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <p><small><?php echo count($browseRows); ?> entr<?php echo count($browseRows) === 1 ? 'y' : 'ies'; ?></small></p>
  <?php endif; ?>
<?php endif; ?>

<hr>

<h2>Add Entry</h2>

<?php if ($addErrors): ?>
<ul class="errors">
  <?php foreach ($addErrors as $err): ?>
    <li><?php echo htmlspecialchars($err); ?></li>
  <?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="post" action="">
  <input type="hidden" name="form_action" value="add">
  <input type="hidden" name="browse_from" value="<?php echo htmlspecialchars($browseFrom); ?>">
  <input type="hidden" name="browse_to"   value="<?php echo htmlspecialchars($browseTo); ?>">
  <table>
    <tr>
      <th>Insulin</th>
      <td><input type="text" name="insulin" value="<?php echo htmlspecialchars($fInsulin); ?>" size="8"></td>
    </tr>
    <tr>
      <th>Gabapentin</th>
      <td><input type="text" name="gabapentin" value="<?php echo htmlspecialchars($fGabapentin); ?>" size="8"></td>
    </tr>
    <tr>
      <th>Allergy</th>
      <td><input type="text" name="allergy" value="<?php echo htmlspecialchars($fAllergy); ?>" size="8"></td>
    </tr>
    <tr>
      <th>Note</th>
      <td><input type="text" name="note" value="<?php echo htmlspecialchars($fNote); ?>" size="40"></td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" value="Add Entry"></td>
    </tr>
  </table>
  <p><small>Leave any field blank to omit it. At least one of insulin/gabapentin/allergy is required.</small></p>
</form>

<?php include $docroot.'resources/template-footer.php'; ?>
