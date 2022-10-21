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
    $WLANGroups = $ZD->GetWlanGroups($ZONEID);
    HTTPHelper::headerJson();
    echo json_encode($WLANGroups);
    HTTPHelper::closeConnection();
}