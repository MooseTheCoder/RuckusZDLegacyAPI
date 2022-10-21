<?php

$GLOBALS['App_Key'] = '2532720351';
$GLOBALS['ZD'] = [
    'Config'=>[
        'Model'=>'1100',
        'Username'=>'admin',
        'Password'=>'admin',
        'SNMP3_IP'=>'10.10.10.8',
        'SNMP3_Port'=>'1161',
        'SNMP3_SecurityLevel'=>'authPriv',
        'SNMP3_User'=>'readwrite',
        'SNMP3_AuthType'=>'md5',
        'SNMP3_AuthPass'=>'readwrite',
        'SNMP3_AuthPrivacy'=>'DES',
        'SNMP3_AuthPrivacyPhrase'=>'readwrite'
    ]
];
$GLOBALS['debug'] = false;
$GLOBALS['zd_mode'] = ZDModes::$PHYSICAL;