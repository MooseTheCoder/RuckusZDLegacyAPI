<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isGet()){
    $Zones = $ZD->GetZones();
    HTTPHelper::headerJson();
    echo json_encode($Zones);
    HTTPHelper::closeConnection();
}