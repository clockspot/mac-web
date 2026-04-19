  </div><!-- end content -->
  <div id="footer">
    <?php
    try {
      $versionFile = $projroot.'.version';
      if (file_exists($versionFile)) {
        $version = trim(file_get_contents($versionFile));
        $branch  = null;
        $headFile = $projroot.'.git/HEAD';
        if (file_exists($headFile)) {
          $headRaw = trim(file_get_contents($headFile));
          if (strpos($headRaw, 'ref: refs/heads/') === 0)
            $branch = substr($headRaw, strlen('ref: refs/heads/'));
        }
        echo '<small'.($branch ? ' title="'.htmlspecialchars($branch).'"' : '').'>'.htmlspecialchars($version).'</small>';
      }
    } catch (Exception $e) {
      if (isset($showErrors) && $showErrors) echo htmlspecialchars($e->getMessage());
    }
    ?>
  </div><!-- end footer -->
</div><!-- end wrap -->
</body>
</html>
