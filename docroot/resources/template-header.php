<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title><?php echo htmlspecialchars(SITE_TITLE).(isset($titledesc)&&$titledesc ? ': '.htmlspecialchars($titledesc) : ''); ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <?php
  $fStyles = $docroot.'resources/styles.css';
  if (file_exists($fStyles)) {
    echo '<link rel="stylesheet" type="text/css" href="'.$fStyles.'?v='.filemtime($fStyles).'">';
  }
  ?>
</head>
<body>
<div id="wrap">
  <div id="header">
    <div id="nav">
      <a href="<?php echo $docroot; ?>"<?php echo (!isset($thispage)||$thispage==='' ? ' class="current"' : ''); ?>><?php echo htmlspecialchars(SITE_TITLE); ?></a>&nbsp;
      <a href="<?php echo $docroot; ?>riley/"<?php echo (isset($thispage)&&$thispage==='Riley' ? ' class="current"' : ''); ?>>Riley</a>
    </div>
  </div><!-- end header -->
  <div id="content">
