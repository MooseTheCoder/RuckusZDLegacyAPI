<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isGet()){
    $Wlan = $ZD->GetWlan($ZONEID, $WLANID);
    HTTPHelper::headerJson();
    echo json_encode($Wlan);
    HTTPHelper::closeConnection();
}