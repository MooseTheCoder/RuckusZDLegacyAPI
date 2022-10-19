<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isPost()){
    $Request = HTTPHelper::getJsonAsArray();
    $ID = $Request['id'];
    $WLAN = $ZD->GetWlan($ZONEID, $ID);
    $WLANGroup = $ZD->_GetWlanGroupInternal($ZONEID, $WLANGROUP);
    Logger::Log("==>ACTION REQUIRED : Add WLAN {$WLAN['name']} to WLAN Group {$WLANGroup['name']}");
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
    Logger::Log("==>ACTION REQUIRED : Add REMOVE {$WLAN['name']} from WLAN Group {$WLANGroup['name']}");
    HTTPHelper::headerJson();
    HTTPHelper::responseCode(204);
    echo json_encode([
        'success'=>true
    ]);
}