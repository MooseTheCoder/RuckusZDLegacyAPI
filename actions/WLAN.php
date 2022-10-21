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
    $Wlan = $ZD->GetWlan($ZONEID, $WLANID);
    HTTPHelper::headerJson();
    echo json_encode($Wlan);
    HTTPHelper::closeConnection();
}