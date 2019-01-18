<?php

class Box extends Helper
{   protected $pdo;
    function __construct() {
    }

    protected function open() {
        
        // DB settings
    
        $serverName = RadioInterface::dbHost;
        $userName = RadioInterface::dbUser;
        $password = RadioInterface::dbPassword;
        $dbname = RadioInterface::dbDataBase;    
        
        // set DSN
        
        $dsn = 'mysql:host='.$serverName.'; dbname='.$dbname;

        // create a PDO instance

        $pdo = new PDO($dsn, $userName, $password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        
        if (!$pdo) {
            die ('Connection failed');
        }
        // echo 'Connection to <strong>'.$dbname.'</strong> database is ready! </br></br>';
        return $pdo;
    }
    protected function initiate($pdo) {
         
        // Check if tabe is created
        
        if (parent::tableExists($pdo, 'presets') == false) {
            //create db migrations
    
            $pdo->exec("DROP TABLE IF EXISTS presets");
            try {
                $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                
                $sql ="CREATE TABLE presets (
                    preset_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    preset_frequency DECIMAL(10,1) NOT NULL,
                    preset_volume DECIMAL(10,1) NOT NULL,
                    preset_status BOOLEAN NOT NULL,
                    preset_current BOOLEAN NOT NULL,
                    preset_set TIMESTAMP
                    )";
                $pdo->exec($sql);
            } 
            
            catch(PDOException $e) {
                echo $e->getMessage();    
            }

            // seed data
            
            $sql = 'INSERT INTO `presets` (`preset_id`, `preset_frequency`, `preset_volume`, `preset_status`, `preset_current`) VALUES (NULL, 88.0, 10, 1, 0), (NULL, 88.0, 10, 1, 0), (NULL, 88.0, 10, 1, 0), (NULL, 88.0, 10, 1, 1) ';
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                print("Initial <strong>PRESETS</strong> created.\n</br>");
        }
        return $pdo;
    }
}
