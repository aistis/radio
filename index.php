<?php
    session_start();
    include ''.__DIR__.'/bootsrap.php';
    $radio = new Radio;
    $helper = new Helper;
    
    if (isset($_POST['power']) && $_POST['power'] === 'click') {
        if ($_SESSION['power'] === true) {
            $_SESSION['power'] = false;
            session_destroy();
        }
        elseif ($_SESSION['power'] === false || !isset($_SESSION['power'])) {
            $_SESSION['power'] = true;
        }
        $helper->prg('index.php');
        $radio->power();
    }
    
    if (isset($_POST['drop'])) {
        if ($_SESSION['drop'] = true) {
            $radio->dropDb();
            var_dump($_POST);
        }
        $helper->prg('index.php');
    }

    if ($_SESSION) {
        $row = $helper->getPreset($radio->pdo());
        $initialVolume = $radio->showVolume(1);
        $initialFrequency = $radio->showTune(1);
        $initialTune = $radio->showRadioStationName(1);
        if (isset($_POST['load_preset'])) {
            if ($_POST['load_preset'] === '1') {
                $radio->loadPresets1();
            }
            if ($_POST['load_preset'] === '2') {
                $radio->loadPresets2();
            }
            if ($_POST['load_preset'] === '3') {
                $radio->loadPresets3();   
            }
        $helper->prg('index.php');
        }
        if (isset($_POST['save_preset'])) {
            if ($_POST['save_preset'] === '1') {
                $radio->savePresets1();
            }
            if ($_POST['save_preset'] === '2') {
                $radio->savePresets2();
            }
            if ($_POST['save_preset'] === '3') {
                $radio->savePresets3();   
            }
        $helper->prg('index.php');
        }
        if (isset($_POST['volume'])) {
            if ($_POST['volume'] === 'up') {
                $radio->volumeUp();
            }
            if ($_POST['volume'] === 'down') {
                $radio->volumeDown();
            }
        $helper->prg('index.php');
        }
        if (isset($_POST['frequency'])) {
            if ($_POST['frequency'] === 'next') {
                $radio->tuneUp();
            }
            if ($_POST['frequency'] === 'prev') {
                $radio->tuneDown();
            }
        $helper->prg('index.php');
        }
        if (isset($_POST['station'])) {
            if ($_POST['station'] === 'next') {
                $radio->goToNexStationUp();
            }
            if ($_POST['station'] === 'prev') {
                $radio->goToNexStationDown();
            }
        $helper->prg('index.php');
        }
    }
    else {
        $initialVolume = '';
        $initialFrequency = '';
        if (isset($_POST) && !isset($_POST['power'])) {
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <title>Radio</title>
    <style>
    .xdebug-var-dump{
        background-color: black;
        color:white;
    }
    </style>
</head>
<body>
    <div class="container" id="radioBox">
        <div id="brand" class="row">
            <span style="font-weight:900; <?php if (isset($_SESSION['power'])) {echo 'color: tomato;';}?>">SHARP</span>
        </div>
        <div id="station" class="row">
            <div class="col-2">
                <form action="" method="post">
                    <input type="text" hidden class="form-control" name="station" value="prev">
                    <input type="submit" class="btn btn-info" value="Previous">
                </form>
            </div>
            <div class="col-8" id="tuneText">
                <?php if (isset($initialTune)) {echo $initialTune;} ?>
            </div>
            <div class="col-2">
                <form action="" method="post">
                    <input type="text" hidden class="form-control" name="station" value="next">
                    <input type="submit" class="btn btn-info" value="Next">
                </form>
            </div>
        </div>
        <div id="tunner" class="row">
            <div class="col-2">
                <form action="" method="post">
                    <input type="text" hidden class="form-control" name="frequency" value="prev">
                    <input type="submit" class="btn btn-info" value="Tune down">
                </form>
            </div>
            <div class="col-8" id="frequencyText">
                <?php if (isset($initialTune)) {echo $initialFrequency.' FM';} ?>
            </div>
            <div class="col-2">
                <form action="" method="post">
                    <input type="text" hidden class="form-control" name="frequency" value="next">
                    <input type="submit" class="btn btn-info" value="Tune up">
                </form>
            </div>
        </div>
        <div id="controls" class="row">
            <div class="row col-6" id="volumeControls">
                <div class="col-12">
                    <p>Volume</p>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                        <input type="text" hidden class="form-control" name="volume" value="down">
                        <input type="submit" class="btn btn-success" value="-">
                    </form>
                </div>
                <div class="col-4" id="volumeText">
                    <?php echo $initialVolume; ?>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                        <input type="text" hidden class="form-control" name="volume" value="up">
                        <input type="submit" class="btn btn-success" value="+">
                    </form>
                </div>
            </div>
            <div class="row col-6" id="presetControls">
                <div class="col-12">
                    <p>Presets control</p>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="load_preset" value="1">
                          <input type="submit" class="btn btn-success" value="1">
                    </form>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="load_preset" value="2">
                          <input type="submit" class="btn btn-success" value="2">
                    </form>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="load_preset" value="3">
                          <input type="submit" class="btn btn-success" value="3">
                    </form>
                </div>
            </div>
            <div class="row col-6">
                <div class="col-6">
                    <form action="" method="post">
                        <input type="text" hidden class="form-control" name="power" value="click">
                        <input type="submit" class="btn btn-danger power" value="Power">
                    </form>
                </div>
            </div>
            <div class="row col-6">
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="save_preset" value="1">
                          <input type="submit" class="btn btn-danger" value="Set">
                    </form>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="save_preset" value="2">
                          <input type="submit" class="btn btn-danger" value="Set">
                    </form>
                </div>
                <div class="col-4">
                    <form action="" method="post">
                          <input type="text" hidden class="form-control" name="save_preset" value="3">
                          <input type="submit" class="btn btn-danger" value="Set">
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>