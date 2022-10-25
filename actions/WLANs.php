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
    $Wlans = $ZD->GetWlans($ZONEID);
    HTTPHelper::headerJson();
    echo json_encode($Wlans);
    HTTPHelper::closeConnection();
}

if(HTTPHelper::isPost()){
    /* 
        Strange firmware issue here.
        Add things manually in the API for now.
    */
    if($GLOBALS['zd_mode'] === ZDModes::$PHYSICAL){
        $Request = HTTPHelper::getJsonAsArray();
        Logger::Log("==>ACTION REQUIRED : CREATE WLAN {$Request['name']}");
    }

    if($GLOBALS['zd_mode'] === ZDModes::$EMULATED){
        $Request = HTTPHelper::getJsonAsArray();
        $ZD->CreateWlan($Request);
    }
    HTTPHelper::headerJson();
    echo json_encode(['success'=>true]);
    HTTPHelper::closeConnection();
}