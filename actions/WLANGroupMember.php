<?php

switch($GLOBALS['zd_mode']){
    case ZDModes::$PHYSICAL :
        $ZD = new RuckusZD($GLOBALS['ZD']['Config']);
    break;
    case ZDModes::$EMULATED :
        $ZD = new RuckusZDEmulator();
    break;
}

if(HTTPHelper::isPost()){
    $Request = HTTPHelper::getJsonAsArray();
    $ID = $Request['id'];
    $WLAN = $ZD->GetWlan($ZONEID, $ID);
    $WLANGroup = $ZD->_GetWlanGroupInternal($ZONEID, $WLANGROUP);
    if($GLOBALS['zd_mode'] === ZDModes::$PHYSICAL){
        Logger::Log("==>ACTION REQUIRED : Add WLAN {$WLAN['name']} to WLAN Group {$WLANGroup['name']}");
    }
    if($GLOBALS['zd_mode'] === ZDModes::$EMULATED){
        Logger::Log("$ID");
        $ZD->AddWlanToGroup($ID, $WLANGROUP);
    }
    HTTPHelper::headerJson();
    echo json_encode([
        'success'=>true
    ]);
}

if(HTTPHelper::isDelete()){
    $Request = HTTPHelper::getJsonAsArray();
    $ID = $WLANID;
    $WLAN = $ZD->GetWlan($ZONEID, $ID);
    $WLANGroup = $ZD->_GetWlanGroupInternal($ZONEID, $WLANGROUP);
    if($GLOBALS['zd_mode'] === ZDModes::$PHYSICAL){
        Logger::Log("==>ACTION REQUIRED : Add REMOVE {$WLAN['name']} from WLAN Group {$WLANGroup['name']}");
    }
    HTTPHelper::headerJson();
    HTTPHelper::responseCode(204);
    echo json_encode([
        'success'=>true
    ]);
}