<?php

class RuckusZDEmulator{

    public function Session($Username, $Password){
        return hash('sha256', $GLOBALS['App_Key'].date('Y-m-d'));
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

    public function _GetWlanGroupInternal($ZONEID, $WLANGROUP){
        $WlanGroups = $this->GetWlanGroups($ZONEID);
        foreach($WlanGroups['list'] as $WlanGroup){
            if($WlanGroup['id'] === $WLANGROUP){
                return $WlanGroup;
            }
        }
    }

    public function GetWlanGroups($ZONEID){
        $WlanGroupList = $this->GetFromFile('WLAN_GROUPS');
        // For each WlanGroup we need its members
        foreach($WlanGroupList as $Index=>$WlanGroup){
            $VirtualId = implode('-' , str_split(md5($WlanGroup['id'].$GLOBALS['App_Key']), 8));
            $WlanGroupList[$Index] = [
                'id'=>$VirtualId,
                'zoneId'=>$ZONEID,
                'name'=>$WlanGroup['name'],
                'description'=>$WlanGroup['description'],
            ];
            $GroupMembers = $this->GetGroupMembers($ZONEID, $VirtualId);
            $WlanGroupList[$Index]['members'] = $GroupMembers;
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
        $Wlans = $this->GetFromFile('WLANS');
        foreach($Wlans as $Index=>$WLAN){
            if(isset($WLAN['WLAN_GROUP'])){
                $VirtualId = implode('-' , str_split(md5($WLAN['WLAN_GROUP'].$GLOBALS['App_Key']), 8));
                if($VirtualId === $GroupId){
                    // This matches WLAN
                    $GroupMembers[] = [
                        'id'=>$Index+1,
                        'name'=>$WLAN['name'],
                        'accessVlan'=>$WLAN['vlan']['accessVlan']
                    ];
                }
            }
        }
        return $GroupMembers;
    }

    public function GetWlans($ZONEID){
        $Wlans = $this->GetFromFile('WLANS');
        $WlanList = [];
        foreach($Wlans as $Index=>$WLAN){
            $WlanList[]=[
                'id'=>($Index+1),
                'zoneId'=>$ZONEID,
                'name'=>$WLAN['name'],
                'ssid'=>$WLAN['ssid']
            ];
        }
        return [
            'totalCount'=>count($WlanList),
            'hasMore'=>false,
            'firstIndex'=>0,
            'list'=>$WlanList
        ];
    }

    public function GetWlan($ZONEID, $WLANID){
        $WLANS = $this->GetFromFile('WLANS');
        $WLAN = [];
        foreach($WLANS as $Index=>$aWLAN){
            if(($Index+1) === (int)$WLANID){
                unset($aWLAN['WLAN_GROUP']);
                $aWLAN['id'] = $WLANID;
                $aWLAN['zoneId'] = $ZONEID;
                $WLAN = [
                    'id' => $WLANID,
                    'zoneId' => $ZONEID,
                    'name'=>$aWLAN['name'],
                    'ssid'=>$aWLAN['ssid'],
                    'vlan'=>[
                        'accessVlan'=>$aWLAN['vlan']['accessVlan']
                    ]
                ];
                continue;
            }
        }
        return $WLAN;
    }

    public function CreateWlan($Wlan){
        $File = $this->FileRead();
        $NewWlan = [
            'name'=>$Wlan['name'],
            'ssid'=>$Wlan['ssid'],
            'vlan'=>[
                'accessVlan'=>$Wlan['vlan']['accessVlan']
            ]
        ];
        $File['WLANS'][]=$NewWlan;
        $this->FileWrite($File);
    }

    private function GetFromFile($Key){
        $Contents = json_decode(file_get_contents('emulatedZd.json'), true);
        return $Contents[$Key];
    }

    private function FileRead(){
        return json_decode(file_get_contents('emulatedZd.json'), true);
    }
    private function FileWrite($Data){
        file_put_contents('emulatedZd.json', json_encode($Data));
    }
}