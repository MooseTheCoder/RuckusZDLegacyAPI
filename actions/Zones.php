<?php

switch($GLOBALS['zd_mode']){
    case ZDModes::$PHYSICAL :
        $ZD = new RuckusZD($GLOBALS['ZD']['Config']);
    break;
    case ZDModes::$EMULATED :
        $ZD = new RuckusZDEmulator();
    break;
}

if(HTTPHelper::isGet()){
    $Zones = $ZD->GetZones();
    HTTPHelper::headerJson();
    echo json_encode($Zones);
    HTTPHelper::closeConnection();
}