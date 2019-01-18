<?php

class Helper
{
  protected function tableExists($pdo, $table) {

    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        // We got an exception == table not found
        return FALSE;
    }

    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
  }
  public function prg($url) {
    return header('Location: '.$url.'');
  }
  public function getPreset($pdo) {
    $stmt = $pdo->query('SELECT * FROM presets WHERE preset_status = 1');
    $row = $stmt->fetch();
    return $row;
  }
}
