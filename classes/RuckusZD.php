<?php

class RuckusZD{
    private $ZDConfig;
    private $SNMP;
    private $OIDMap = [
        'WLAN'=>[
            'NAME'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.4',
            'SSID'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.2',
            'VLAN'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.45',
            'GROUP'=>[
                'TABLE'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.2',
                'NAME'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.2.1.5',
                'DESC'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.2.1.6',
            ],
            'GROUP_WLAN'=>[
                'ROWSTATUS'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.3.1.10'
            ],
            'WLANS' => [
                'TABLE'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1',
                'SSID'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.2',
                'NAME'=>'iso.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.4'
            ]
        ]
    ];
    
    function __construct($Config){   
        $this->ZDConfig = $Config;
        $this->InitSNMP();
    }

    public function Session($Username, $Password){
        if($Username === $this->ZDConfig['Username'] && $Password === $this->ZDConfig['Password']){
            return hash('sha256', $this->ZDConfig['App_Secret'].date('Y-m-d'));
        }
        return false;
    }

    public function GetZones(){
        return [
            'totalCount'=>1,
            'hasMore'=>false,
            'firstIndex'=>0,
            'list'=>[
                [
                    'id'=>implode('-' , str_split(md5('Emulated Zone'), 8)),
                    'name'=>'Emulated Zone'
                ]
            ]
        ];
    }

    public function GetWlanGroups($ZONEID){
        $WlanGroupTable = $this->SNMP->walk($this->OIDMap['WLAN']['GROUP']['TABLE']);
        $WlanGroupList = [];
        foreach($WlanGroupTable as $OID=>$Value){
            $Value = str_replace('"', '', $Value);
            $SNMPId = substr($OID, -1);
            $ArrayIndex = (substr($OID, -1) -1);
            $ReferenceOid = substr($OID, 0, -2);
            $VirtualId = implode('-' , str_split(md5($SNMPId.$this->ZDConfig['App_Secret']), 8));
            $WlanGroupList[$ArrayIndex]['id'] = $VirtualId;
            $WlanGroupList[$ArrayIndex]['zoneId'] = $ZONEID;
            if($ReferenceOid === $this->OIDMap['WLAN']['GROUP']['NAME']){
                $WlanGroupList[$ArrayIndex]['name'] = $Value;
            }
            if($ReferenceOid === $this->OIDMap['WLAN']['GROUP']['DESC']){
                $WlanGroupList[$ArrayIndex]['description'] = $Value;
            }
        }
        // For each WlanGroup we need its members
        foreach($WlanGroupList as $Index=>$WlanGroup){
            $GroupMembers = $this->GetGroupMembers($ZONEID, $WlanGroup['id']);
            $WlanGroupList[$Index]['members'] = $GroupMembers;
            Logger::Log(json_encode($GroupMembers));
        }
        return [
            'totalCount'=>count($WlanGroupList),
            'hasMore'=>false,
            'firstIndex'=>0,
            'list'=>$WlanGroupList
        ];
    }

    private function GetGroupMembers($ZONEID, $GroupId){
        $GroupMembers = [];
        $GroupMemberLink = $this->SNMP->walk($this->OIDMap['WLAN']['GROUP_WLAN']['ROWSTATUS']);
        foreach($GroupMemberLink as $OID=>$Value){
            $GroupMember = explode('.', substr($OID, -3)); // 0 = Group, 1 = WLAN
            $VirtualId = implode('-' , str_split(md5($GroupMember[0].$this->ZDConfig['App_Secret']), 8));
            if($VirtualId === $GroupId){
                // This matches WLAN
                $WLAN = $this->GetWlan($ZONEID, $GroupMember[1]);
                $GroupMembers[] = [
                    'id'=>$WLAN['id'],
                    'name'=>$WLAN['name'],
                    'accessVlan'=>$WLAN['vlan']['accessVlan']
                ];
            }
        }
        return $GroupMembers;
    }

    public function GetWlans($ZONEID){
        $WlanTable = $this->SNMP->walk($this->OIDMap['WLAN']['WLANS']['TABLE']);
        $WlanList = [];
        foreach($WlanTable as $OID=>$Value){
            $Value = str_replace('"', '', $Value);
            $SNMPId = substr($OID, -1);
            $ArrayIndex = (substr($OID, -1) -1);
            $ReferenceOid = substr($OID, 0, -2);
            $VirtualId = implode('-' , str_split(md5($SNMPId.$this->ZDConfig['App_Secret']), 8));
            $WlanList[$ArrayIndex]['id'] = $SNMPId;
            $WlanList[$ArrayIndex]['zoneId'] = $ZONEID;
            if($ReferenceOid === $this->OIDMap['WLAN']['WLANS']['NAME']){
                $WlanList[$ArrayIndex]['name'] = $Value;
            }
            if($ReferenceOid === $this->OIDMap['WLAN']['WLANS']['SSID']){
                $WlanList[$ArrayIndex]['name'] = $Value;
            }
        }
        return [
            'totalCount'=>count($WlanList),
            'hasMore'=>false,
            'firstIndex'=>0,
            'list'=>$WlanList
        ];
    }

    public function GetWlan($ZONEID, $WLANID){
        $this->SNMP->valueretrieval = SNMP_VALUE_PLAIN;
        $WlanName = $this->SNMP->get("{$this->OIDMap['WLAN']['NAME']}.$WLANID");
        $WlanVlan = $this->SNMP->get("{$this->OIDMap['WLAN']['VLAN']}.$WLANID");
        $WlanSsid = $this->SNMP->get("{$this->OIDMap['WLAN']['SSID']}.$WLANID");
        return [
            'id'=>$WLANID,
            'name'=>$WlanName,
            'ssid'=>$WlanSsid,
            'zoneId'=>$ZONEID,
            'vlan'=>[
                'accessVlan'=>$WlanVlan
            ]
        ];
    }

    public function CreateWlan($Wlan){
        $Wlans = $this->SNMP->walk($this->OIDMap['WLAN']['WLANS']['NAME']);
        $NextWlanId = count($Wlans) + 1;
        var_dump($NextWlanId);
        exit;
        $Res = $this->SNMP->set(
            [
                ".1.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.4.$NextWlanId", // Display Name
                ".1.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.2.$NextWlanId", // SSID
                ".1.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.45.$NextWlanId", // SSID
                ".1.3.6.1.4.1.25053.1.2.2.2.1.1.1.1.63.$NextWlanId" // Row Status
            ],
            [
                's',
                's',
                'i',
                'i'
            ],
            [
                $Wlan['name'],
                $Wlan['ssid'],
                $Wlan['vlan']['accessVlan'],
                4
            ]
        );
        var_dump($Res);
    }

    private function InitSNMP(){
        $this->SNMP = new SNMP(SNMP::VERSION_3, $this->GetSNMPHost(), $this->ZDConfig['SNMP3_User']);
        $this->SNMP->quick_print = true;
        $this->SNMP->setSecurity(
            $this->ZDConfig['SNMP3_SecurityLevel'], // Auth Protocol, // Security Level
            $this->ZDConfig['SNMP3_AuthType'], // Auth Protocol
            $this->ZDConfig['SNMP3_AuthPass'], // Auth Pass
            $this->ZDConfig['SNMP3_AuthPrivacy'], // Privacy Protocol
            $this->ZDConfig['SNMP3_AuthPrivacyPhrase'], // Privacy Passphrase
        );
    }

    private function GetSNMPHost(){
        return $this->ZDConfig['SNMP3_IP'].':'.$this->ZDConfig['SNMP3_Port'];
    }
}