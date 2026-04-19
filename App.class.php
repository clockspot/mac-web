<?php
require_once __DIR__.'/App/Riley.trait.php';

class App {
  // Requires settings.php
  use RileyMethods;

  private $db;

  public function __construct() {
    $this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBBASE);
    if ($this->db->connect_error)
      throw new Exception('Database connection failed: ('.$this->db->connect_errno.') '.$this->db->connect_error);
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

} // end class App
