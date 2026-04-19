<?php
trait RileyMethods {

  public function rileyBrowse($from, $to) {
    $table = RILEY_TABLE;
    $conditions = [];
    $types = '';
    $values = [];

    if ($from !== '') {
      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from))
        throw new Exception("'From' date must be yyyy-mm-dd.");
      $conditions[] = '`datetime` >= ?';
      $types .= 's';
      $values[] = $from.' 00:00:00';
    }
    if ($to !== '') {
      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))
        throw new Exception("'To' date must be yyyy-mm-dd.");
      $conditions[] = '`datetime` <= ?';
      $types .= 's';
      $values[] = $to.' 23:59:59';
    }

    $query = "SELECT `id`, `datetime`, `insulin`, `gabapentin`, `allergy`, `note`
              FROM `$table`"
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            .' ORDER BY `datetime` DESC';

    if ($values) {
      $stmt = $this->prepareStmt($query, 'browse riley');
      $stmt->bind_param($types, ...$values);
      $this->runStmt($stmt, 'browse riley');
      $result = $stmt->get_result();
      $stmt->close();
    } else {
      $result = $this->db->query($query);
      if (!$result)
        throw new Exception("Browse failed: ({$this->db->errno}) {$this->db->error}");
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    return $rows;
  } // end rileyBrowse

  public function rileyAdd($insulin, $gabapentin, $allergy, $note) {
    // Normalize: empty string -> null; validate numerics
    foreach (['insulin' => &$insulin, 'gabapentin' => &$gabapentin, 'allergy' => &$allergy] as $name => &$val) {
      if ($val === '' || $val === null) {
        $val = null;
      } else {
        if (!is_numeric($val))
          throw new Exception("'".ucfirst($name)."' must be a number.");
        $val = (float) $val;
      }
    }
    unset($val);

    $note = ($note !== null && trim($note) !== '') ? trim($note) : null;

    if ($insulin === null && $gabapentin === null && $allergy === null)
      throw new Exception("Please enter at least one value (insulin, gabapentin, or allergy).");

    $now   = date('Y-m-d H:i:s');
    $table = RILEY_TABLE;
    $query = "INSERT INTO `$table` (`datetime`, `insulin`, `gabapentin`, `allergy`, `note`)
              VALUES (?, ?, ?, ?, ?)";
    $stmt  = $this->prepareStmt($query, 'add riley entry');
    $stmt->bind_param('sddds', $now, $insulin, $gabapentin, $allergy, $note);
    $this->runStmt($stmt, 'add riley entry');
    $id = $this->db->insert_id;
    $stmt->close();
    return $id;
  } // end rileyAdd

} // end trait RileyMethods
