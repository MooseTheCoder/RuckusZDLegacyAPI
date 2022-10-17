<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isGet()){
    $WLANGroups = $ZD->GetWlanGroups($ZONEID);
    HTTPHelper::headerJson();
    echo json_encode($WLANGroups);
    HTTPHelper::closeConnection();
}