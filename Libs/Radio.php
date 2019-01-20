<?php

class Radio extends Box implements RadioInterface
{
    private $serverName, $userName, $password, $dbname, $dsn, $pdo;
    private $station, $volume, $stationsInArea;

    function __construct() {
        parent::setDb();
        $pdo = Radio::pdo();
        parent::initiate($pdo);
    }
    
    //Indicators
    
    public function showTune($data){
        $pdo = parent::open();
        $stmt = $pdo->query('SELECT * FROM presets WHERE preset_current = '.$data.'');
        $row = $stmt->fetch();
        return $row->preset_frequency;
    }
    public function showRadioStationName($data){
        $stationsInArea = Radio::buildStationList();
        $stationFrequency = Radio::showTune($data);
        $stationName = array_search($stationFrequency, $stationsInArea);
        return $stationName;
    }
    public function showVolume($data){
        $pdo = Radio::pdo();
        $stmt = $pdo->query('SELECT * FROM presets WHERE preset_current = '.$data.'');
        $row = $stmt->fetch();
        return $row->preset_volume;

    }

    //Buttons
    public function power(){
        $pdo = Radio::pdo();
        if ($_SESSION['power'] === true) {
            parent::initiate($pdo);
        }
    }
    public function dropDb() {
        $pdo = Radio::pdo();
        $pdo->exec("DROP TABLE IF EXISTS presets");
        parent::initiate($pdo);
    }    
    public function volumeUp(){
        //+0.5
        $pdo = Radio::pdo();
        Radio::setTempStation();
        $maxVolume = RadioInterface::maxVol;
        $stmt = $pdo->query('SELECT preset_volume FROM presets WHERE preset_current = 1');
        $currentVolume = $stmt->fetch();
        if ($currentVolume->preset_volume < $maxVolume) {
            $pdo->query('UPDATE presets SET preset_volume = preset_volume + 0.5 WHERE preset_current = 1');
        }
    }
    public function volumeDown(){
        //-0.5
        $pdo = Radio::pdo();
        Radio::setTempStation();
        $minVolume = RadioInterface::minVol;
        $stmt = $pdo->query('SELECT preset_volume FROM presets WHERE preset_current = 1');
        $currentVolume = $stmt->fetch();
        if ($currentVolume->preset_volume > $minVolume) {
            $pdo->query('UPDATE presets SET preset_volume = preset_volume - 0.5 WHERE preset_current = 1');
        }
    }
    public function goToNexStationUp(){
        $stations = Radio::buildStationList();
        $currentFrequency = Radio::showTune(1);
        $fmList = array_values($stations);

        if ($fmList[count($fmList)-1] > RadioInterface::maxFm) {
            foreach (array_reverse($stations) as $key => $value) {
                if ($value <= RadioInterface::maxFm) {
                    $lastStation = $stations[$key];
                    break;
                }
            }
        }
        else $lastStation = $fmList[count($fmList)-1];

        if ($fmList[0] < RadioInterface::minFm) {
            foreach ($stations as $key => $value) {
                if ($value >= RadioInterface::minFm) {
                    $firstStation = $stations[$key];
                    break;
                }
            }
        }
        else $firstStation = $fmList[0];

        if ($lastStation == $currentFrequency || $currentFrequency >= RadioInterface::maxFm) {
            $pdo = Radio::pdo();     
            $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$firstStation.' WHERE presets.preset_id = 4');
        }
        else {
            if (!array_search($currentFrequency, $stations)){
                foreach ($stations as $key => $value) {
                    if ($value > $currentFrequency) {
                        //uzsetinu curent pagal rasta frequency
                        $pdo = Radio::pdo();     
                        $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$value.' WHERE presets.preset_id = 4');
                        break;
                    }
                }
            }
            elseif (array_search($currentFrequency, $stations)){
                $currentFrequency = $currentFrequency +0.1;
                foreach ($stations as $key => $value) {
                    if ($value > $currentFrequency) {
                        //uzsetinu curent pagal rasta frequency
                        $pdo = Radio::pdo();     
                        $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$value.' WHERE presets.preset_id = 4');
                        break;
                    }
                }
            }
        }
    }
    public function goToNexStationDown(){
        $stations = Radio::buildStationList();
        $currentFrequency = Radio::showTune(1);
        $fmList = array_values($stations);

        if ($fmList[count($fmList)-1] > RadioInterface::maxFm) {
            foreach (array_reverse($stations) as $key => $value) {
                if ($value <= RadioInterface::maxFm) {
                    $lastStation = $stations[$key];
                    break;
                }
            }
        }
        else $lastStation = $fmList[count($fmList)-1];
        
        if ($fmList[0] < RadioInterface::minFm) {
            foreach ($stations as $key => $value) {
                if ($value >= RadioInterface::minFm) {
                    $firstStation = $stations[$key];
                    break;
                }
            }
        }
        else $firstStation = $fmList[0];

        if ($firstStation == $currentFrequency || $currentFrequency <= RadioInterface::minFm) {
            $pdo = Radio::pdo();     
            $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$lastStation.' WHERE presets.preset_id = 4');
        }
        else {
            if (!array_search($currentFrequency, $stations)){
                foreach (array_reverse($stations) as $key => $value) {
                    if ($value < $currentFrequency) {
                        //uzsetinu curent pagal rasta frequency
                        $pdo = Radio::pdo();     
                        $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$value.' WHERE presets.preset_id = 4');
                        break;
                    }
                }
            }
            elseif (array_search($currentFrequency, $stations)){
                $currentFrequency = $currentFrequency -0.1;
                foreach (array_reverse($stations) as $key => $value) {
                    if ($value < $currentFrequency) {
                        //uzsetinu curent pagal rasta frequency
                        $pdo = Radio::pdo();     
                        $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency = '.$value.' WHERE presets.preset_id = 4');
                        break;
                    }
                }
            }
        }
        
    }
    public function tuneUp(){
        //-0.1
        $pdo = Radio::pdo();
        Radio::setTempStation();
        $stmt = $pdo->query('SELECT preset_frequency FROM presets WHERE preset_current = 1');
        $currentFrequency = $stmt->fetch();
        if ($currentFrequency->preset_frequency < RadioInterface::maxFm) {
            $pdo->query('UPDATE presets SET preset_frequency = preset_frequency + 0.1 WHERE preset_current = 1');
        }
        elseif ($currentFrequency->preset_frequency = RadioInterface::maxFm) {
            $pdo->query('UPDATE presets SET preset_frequency = '.RadioInterface::minFm.' WHERE preset_current = 1');
        }
    }
    public function tuneDown(){
        //+0.1
        $pdo = Radio::pdo();
        Radio::setTempStation();
        $stmt = $pdo->query('SELECT preset_frequency FROM presets WHERE preset_current = 1');
        $currentFrequency = $stmt->fetch();
        if ($currentFrequency->preset_frequency > RadioInterface::minFm) {
            $pdo->query('UPDATE presets SET preset_frequency = preset_frequency - 0.1');
        }
        elseif ($currentFrequency->preset_frequency = RadioInterface::minFm) {
            $pdo->query('UPDATE presets SET preset_frequency = '.RadioInterface::maxFm.' WHERE preset_current = 1');
        }
    }
    private function getActive() {
        $pdo = Radio::pdo();
        $stmt = $pdo->query('SELECT * FROM presets WHERE preset_current = 1');
        $row = $stmt->fetch();
        return $row;
    }
    public function savePresets1(){
        $row = Radio::getActive();
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE presets SET preset_frequency ='.$row->preset_frequency.', preset_volume = '.$row->preset_volume.' WHERE presets.preset_id = 1');
    }
    public function savePresets2(){
        $row = Radio::getActive();
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE presets SET preset_frequency ='.$row->preset_frequency.', preset_volume = '.$row->preset_volume.' WHERE presets.preset_id = 2');
    }
    public function savePresets3(){
        $row = Radio::getActive();
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE presets SET preset_frequency ='.$row->preset_frequency.', preset_volume = '.$row->preset_volume.' WHERE presets.preset_id = 3');
    }
    public function loadPresets1(){
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE `presets` SET `preset_current` = 0 WHERE `presets`.`preset_id` != 1; UPDATE `presets` SET `preset_current` = 1 WHERE `presets`.`preset_id` = 1');
    }
    public function loadPresets2(){
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE `presets` SET `preset_current` = 0 WHERE `presets`.`preset_id` != 2; UPDATE `presets` SET `preset_current` = 1 WHERE `presets`.`preset_id` = 2');
    }
    public function loadPresets3(){
        $pdo = Radio::pdo();     
        $stmt = $pdo->query('UPDATE `presets` SET `preset_current` = 0 WHERE `presets`.`preset_id` != 3; UPDATE `presets` SET `preset_current` = 1 WHERE `presets`.`preset_id` = 3');
    }
    public function pdo() {
        $pdo = parent::open();
        return $pdo;
    }
    public function getStation() {
        $pdo = Radio::pdo();
        $stmt = $pdo->query('SELECT * FROM presets WHERE preset_current = 1');
        $row = $stmt->fetch();
        return $row;
    }
    public function buildStationList() {
        foreach (RadioInterface::stationInArea as $key => $value) {
            $mod = str_replace(',','.',$value);
            $stationsInArea[$key] = $mod;
          };
        return $stationsInArea;
      }
    protected function setTempStation() {
        $pdo = Radio::pdo();
        $stmt = $pdo->query('SELECT * FROM presets WHERE preset_current = 1');
        $row = $stmt->fetch();
        $volume = $row->preset_volume;
        $frequency = $row->preset_frequency;
        $stmt = $pdo->query('UPDATE presets SET preset_current = 0 WHERE presets.preset_id != 4; UPDATE presets SET preset_current = 1, preset_frequency ='.$frequency.', preset_volume = '.$volume.' WHERE presets.preset_id = 4');
    }
}