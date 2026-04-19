<?php
trait LogTableMethods {

  public function logBrowse($table, $from, $to) {
    $table = $this->safeTableName($table);
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

    $query = "SELECT *
              FROM `$table`"
            .($conditions ? ' WHERE '.implode(' AND ', $conditions) : '')
            .' ORDER BY `datetime` DESC';

    if ($values) {
      $stmt = $this->prepareStmt($query, 'browse '.$table);
      $stmt->bind_param($types, ...$values);
      $this->runStmt($stmt, 'browse '.$table);
      $result = $stmt->get_result();
      $stmt->close();
    } else {
      $result = $this->db->query($query);
      if (!$result)
        throw new Exception('Browse '.$table." failed: ({$this->db->errno}) {$this->db->error}");
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    return $rows;
  }

  public function logAdd($table, $request) {
    $table = $this->safeTableName($table);

    // Normalize
    foreach(array_keys($request) as $key=>$val) {
      if(is_string($val)) $val = trim($val);
      if($val==='') $val = null;
      $request[$key] = $val;
    }
    unset($val);

    $columns = $this->getTableColumns($safe);
    
    if(in_array('timestamp', $columns)) $vals['timestamp'] = date('Y-m-d H:i:s');

    foreach($req as $k => $v) {
      if(in_array(strtolower($k), $columns) && !in_array(strtolower($k), array_map('strtolower',array_keys($vals)))) {
        $vals[$k] = $v;
      }
    }
    if(empty($vals)) return;
    $placeholders = implode(',', array_fill(0, count($vals), '?'));
    $query = "INSERT INTO `$safe` (`".implode('`,`',$vals)."`) VALUES ($placeholders)";
    return $this->runQueryWithData($query, $vals, 'insert into '.$table);
  } // end logAdd

} // end trait LogTableMethods
