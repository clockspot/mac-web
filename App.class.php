<?php
require_once __DIR__.'/App/LogTable.trait.php';

class App {
  // Requires settings.php
  use LogTableMethods;

  private $db;

  public function __construct() {
    $this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBBASE);
    if ($this->db->connect_error)
      throw new Exception('Database connection failed: ('.$this->db->connect_errno.') '.$this->db->connect_error);
  }

  public function getTableColumns($table) {
    $safe = $this->safeTableName($table);
    $result = $this->runQuery("SHOW COLUMNS FROM `$safe`", 'get columns');
    $columns = [];
    while($row = $result->fetch_assoc()) $columns[] = strtolower($row['Field']);
    return $columns;
  }

  private function safeTableName($table) {
    return str_replace('`', '', $table);
  }

  private function prepareStmt($query, $purpose) {
    if (!($stmt = $this->db->prepare($query)))
      throw new Exception("For $purpose, prepare failed: ({$this->db->errno}) {$this->db->error}");
    return $stmt;
  }

  private function runStmt($stmt, $purpose) {
    if (!$stmt->execute())
      throw new Exception("For $purpose, execute failed: ({$stmt->errno}) {$stmt->error}");
    return $stmt;
  }

  private function runQuery($query,$purpose) {
    if(!($result = $this->db->query($query)))
      throw new Exception("Sorry! For $purpose, execute failed: (".$this->db->errno.") ".$this->db->error);
    return $result;
  }

  private function runQueryWithData($query,$data,$purpose) {
    if(!($result = $this->db->execute_query($query,$data)))
      throw new Exception("Sorry! For $purpose, execute failed: (".$this->db->errno.") ".$this->db->error);
    return $result;
  }

} // end class App
