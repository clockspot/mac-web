<?php
$thispage  = '';
$titledesc = '';
$docroot   = './';
$projroot  = '../';
require $projroot.'settings.php';

include $docroot.'resources/template-header.php';
?>

<h1>Welcome to <?php echo htmlentities(SITE_TITLE); ?></h1>
<ul>
  <li><a href="riley/">Riley</a> &mdash; Medication and allergy log</li>
</ul>

<?php include $docroot.'resources/template-footer.php'; ?>
