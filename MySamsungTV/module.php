<?php
//zugehoerige TRAIT-Klassen    TEST xxxy
require_once(__DIR__ . "/SamsungTV_Interface.php");
require_once(__DIR__ . "/../libs/NetworkTraits.php");

class MySamsungTV extends IPSModule
{
    
    //externe Klasse einbinden - ueberlagern mit TRAIT.
    use SamsungUPNP,
        DebugHelper;
     
    //*****************************************************************************
    /* Function: Standardfunktinen für ein Modul. 
    ...............................................................................
    *  Create()
     * ApplyChanges()
     */
    /* **************************************************************************** */
    public function Create()
    {
	//Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation   
        // Variable aus dem Instanz Formular registrieren (um zugänglich zu machen)
        $this->RegisterPropertyBoolean("aktiv", false);
        $this->RegisterPropertyString("ip", "192.168.178.35");
        $this->RegisterPropertyInteger("updateInterval", 10000);	
        $this->RegisterPropertyInteger("devicetype", 1);
        $this->RegisterPropertyString("FileData", "");
        
        //Variable anlegen.
        $this->RegisterVariableString("TVchList", "ChannelList");
        $this->RegisterVariableInteger("TVVolume", "Volume", "");
        $this->RegisterVariableInteger("TVChannel", "Channel", "");
        $this->RegisterVariableString("TVchLName", "ChannelName");
        
        
		
      
        // Timer erstellen
        $this->RegisterTimer("update", $this->ReadPropertyInteger("updateInterval"), 'STV_update($_IPS[\'TARGET\']);');
        $this->RegisterTimer("watchdog", 60000, 'STV_watchdog($_IPS[\'TARGET\']);');
        
    }
    
    // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
    // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
    // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    /* **************************************************************************** */
    public function ApplyChanges() {
	//Never delete this line!
        parent::ApplyChanges();
            if($this->ReadPropertyBoolean("aktiv")){
                
                $this->SetTimerInterval("update", $this->ReadPropertyInteger("updateInterval"));
                $this->SetTimerInterval("watchdog", 60000);
            }
            else {
                $this->SetTimerInterval("update", 0);
                $this->SetTimerInterval("watchdog", 0);
            }
    }
    
    //*****************************************************************************
    /* Function: Hilfs funktinen für ein Modul
    ...............................................................................
    *  SendToSplitter(string $payload)
     * GetIPSVersion()
     * RegisterProfile()
     * RegisterProfileAssociation()
     *  SetValue($Ident, $Value)
     */
    /* **************************************************************************** */
    protected function SendToSplitter(string $payload)
		{						
			//an Splitter schicken
			$result = $this->SendDataToParent(json_encode(Array("DataID" => "{F1BA0997-BDDD-2732-1C5C-4C61BFD36F21}", "Buffer" => $payload))); // Interface GUI
			return $result;
		}
		
	/**
	 * gets current IP-Symcon version
	 * @return float|int
	 */
	protected function GetIPSVersion()
	{
		$ipsversion = floatval(IPS_GetKernelVersion());
		if ($ipsversion < 4.1) // 4.0
		{
			$ipsversion = 0;
		} elseif ($ipsversion >= 4.1 && $ipsversion < 4.2) // 4.1
		{
			$ipsversion = 1;
		} elseif ($ipsversion >= 4.2 && $ipsversion < 4.3) // 4.2
		{
			$ipsversion = 2;
		} elseif ($ipsversion >= 4.3 && $ipsversion < 4.4) // 4.3
		{
			$ipsversion = 3;
		} elseif ($ipsversion >= 4.4 && $ipsversion < 5) // 4.4
		{
			$ipsversion = 4;
		} else   // 5
		{
			$ipsversion = 5;
		}

		return $ipsversion;
	}

	//Profile
	protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
	{

		if (!IPS_VariableProfileExists($Name)) {
			IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
		} else {
			$profile = IPS_GetVariableProfile($Name);
			if ($profile['ProfileType'] != $Vartype)
				$this->SendDebug("BMW:", "Variable profile type does not match for profile " . $Name, 0);
		}

		IPS_SetVariableProfileIcon($Name, $Icon);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
	}

	protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
	{
		if (sizeof($Associations) === 0) {
			$MinValue = 0;
			$MaxValue = 0;
		}

		$this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

		//boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
		foreach ($Associations as $Association) {
			IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
		}

	}
        
        /*
  	//Add this Polyfill for IP-Symcon 4.4 and older
	protected function SetValue($Ident, $Value)
	{

		if (IPS_GetKernelVersion() >= 5) {
			parent::SetValue($Ident, $Value);
		} else {
			SetValue($this->GetIDForIdent($Ident), $Value);
		}
	}

*/
        
        
	/*//////////////////////////////////////////////////////////////////////////////
	Function:  watchdog()
	...............................................................................
	Funktion wird über Timer alle 60 Sekunden gestartet
         *  call SubFunctions:   
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable:    
	--------------------------------------------------------------------------------
	return: none  
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/       
        public function watchdog() {
            $ip = $this->ReadPropertyString('ip');
            $alive = Sys_Ping($ip, 1000);
           if ($alive){
               $this->SetTimerInterval("update", $this->ReadPropertyInteger("updateInterval"));
           }
           else {
               $this->SetTimerInterval("update", 0);
           }
        }

	/*//////////////////////////////////////////////////////////////////////////////
	Function:  update()
	...............................................................................
	Funktion wird über Timer alle x Sekunden gestartet
         *  call SubFunctions:   
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable:    
	--------------------------------------------------------------------------------
	return: none  
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/       
        public function update() {
            $ip = $this->ReadPropertyString('ip');
            $alive = Sys_Ping($ip, 1000);
            if ($alive){
                $vol = $this->getVolume();    
                 
                $channel = $this->getChannel();
                $chName = $this->getChannelName();
            }
            else{
                $this->SetTimerInterval("update", 0);
            }
        }


    //*****************************************************************************
    /* Function: Eigene Public Funktionen
    /* **************************************************************************** */	
        
     //*****************************************************************************
    /* Function: getVolume()
    ...............................................................................
     * gibt den Lautstärke Wert als Integer zurück.
     * und schreibt Ergebnis in die Variable Volume
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $volume (integer)
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getVolume() {
        $vol = $this->GetVolume_MTVA();
        SetValue($this->GetIDForIdent("TVVolume"), (int)$vol['Volume']);  
        return (int)$vol['Volume'];
    }   
  
   
        
     //*****************************************************************************
    /* Function: getChannel()
    ...............................................................................
     * gibt den aktuell eingestellten SendeKanal zurück
     * und schreibt Ergebnis in die Variable Channel 
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $channel (xml-string)
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  v
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getChannel() {
        $ch = $this->GetCurrentMainTVChannel_MTVA();
        $this->SendDebug("getChannel ", $ch['MAJORCH'], 0);
        SetValue($this->GetIDForIdent("TVChannel"), (int)$ch['MAJORCH']);  
        $ChType     = $ch['ChType'];
        $MajorCh    = $ch['MAJORCH'];        
        $MinorCh    = $ch['MINORCH'];       
        $PTC        = $ch['PTC'];  
        $ProgNum    = $ch['PROGNUM'];      
        $channel = "<Channel><ChType>".$ChType."</ChType><MajorCh>".$MajorCh."</MajorCh><MinorCh>".$MinorCh."</MinorCh><PTC>".$PTC."</PTC><ProgNum>".$ProgNum."</ProgNum></Channel>" ;
        return $channel;
    } 

     //*****************************************************************************
    /* Function: getChannelName()
    ...............................................................................
     * gibt den Namen des aktuellen Senders zurück 
     * und schreibt Ergebnis in die Variable ChannelName
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $chName (string)
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getChannelName() {
        $ch = $this->GetCurrentMainTVChannel_MTVA();
        $such = $ch['MAJORCH'];
        $prop = "MAJORCH";
        $chListSer = getValue($this->GetIDForIdent("TVchList"));
        $chList = unserialize($chListSer);
        
        $key = $this->searchForValue($such, $prop, $chList);
        $chN = $chList[$key]['ChannelName'];
        
        $chName = substr($chN,1,strlen($chN)-2);
        SetValue($this->GetIDForIdent("TVchLName"),$chName );  
        return  $chList[$key]['$chName'];
    }   
    
     //*****************************************************************************
    /* Function: setChannelbyName(string $ChName)
    ...............................................................................
     * schaltet auf den übergebenen SenderNamen um
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $chName (string)
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function setChannelbyName(string $ChName) {
        $prop = "ChannelName";
        $chListSer = getValue($this->GetIDForIdent("TVchList"));
        $chList = unserialize($chListSer);
         
        
        $searchvalue = '"'.$ChName.'"';
        $key = "ChannelName";
        $array = $chList;

        $result = $this->searcharray($searchvalue, $key, $array);
        
         $ch =  $chList[(int)$result];
        $this->SendDebug("setChannelbyName ", "found: ".$ChName." in".$result, 0);
        $ChType     = $ch['ChType'];
        $MajorCh    = $ch['MAJORCH'];        
        $MinorCh    = $ch['MINORCH'];       
        $PTC        = $ch['PTC'];  
        $ProgNum    = $ch['PROGNUM'];      
        $channel = "<Channel><ChType>".$ChType."</ChType><MajorCh>".$MajorCh."</MajorCh><MinorCh>".$MinorCh."</MinorCh><PTC>".$PTC."</PTC><ProgNum>".$ProgNum."</ProgNum></Channel>" ;
        $this->SendDebug("setChannelbyName ", $channel, 0);
        $this->SetMainTVChannel_MTVA($channel,  2,  '0x01',  0);
    }   
    
    
     //*****************************************************************************
    /* Function: getTVGuide(channel as array)
    ...............................................................................
     *  
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $channel (array)
     * $channels= array("Das Erste HD", 
     *                  "ZDF HD", 
     *                  "RTL Television", 
     *                  "ProSieben", 
     *                  "kabel eins", 
     *                   "RTL2", 
     *                   "SAT.1", 
     *                   "3sat", 
     *                   "VOX", 
     *                   "Tele 5", 
     *                   "ONE HD", 
     *                   "RTLplus" );
     *
    --------------------------------------------------------------------------------
    Status:      
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getTVGuide() {  
        //URL des TV Guides holen
        $TVGuideURL = $this->GetCurrentProgramInformationURL_MTVA();
        $url = $TVGuideURL['CurrentProgInfoURL'];
	//TV Guide file auslesen
        $ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
        //XML file bereinigen, da sonst nicht als xml lesbar (&)
        $dataxml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $data);	
        //xml laden
        $xml = simplexml_load_file($dataxml);


$channels= array("Das Erste HD", "ZDF HD", "RTL Television", "ProSieben", "kabel eins", "RTL2", "SAT.1", "3sat", "VOX", "Tele 5", "ONE HD", "RTLplus" );
$i=0;
        
        foreach($channels as $ch){
            foreach($xml->ProgramInfo as $elem){
                if($elem->DispChName == $ch){
                        $TVGuide[$i]['DispChName'] = (string)($elem->DispChName);
                        $TVGuide[$i]['Time'] = $elem->StartTime." - ".$elem->EndTime;
                        $TVGuide[$i]['ProgTitle'] = (string) $elem->ProgTitle;
                        $i=$i+1;
                }
            }

        }
	
	$this-IPSLog("gg",$TVGuide );
 
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    //*****************************************************************************
    /* Function: buildChannelList() 
    ...............................................................................
     * erzeugt ein Array aller Channels
     * Vorraussetzung: es muss eine channellist.txt erzeugt werden mit ChanSort.exe
     * (siehe Manuals)
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Pr#] =>  
     * [ChannelName] =>  
     * [ChType] =>  
     * [MAJORCH] =>  
     * [MINORCH] =>  
     * [PTC] =>  
     * [PROGNUM] =>  
     * -
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function buildChannelList() {
        $Channellist = base64_decode($this->ReadPropertyString("FileData"));
        $channel = explode("\n", $Channellist);
        $n =  0;
        //auf Ferseh Kanal 1 schalten!
        $key = 'KEY_1';
        $result =   $this->sendKey($key);
        $key = 'KEY_ENTER';
        $result =   $this->sendKey($key);
        foreach($channel as $ch) {
                $kanal = explode("\t", $ch);
                if ($kanal[0] == 'List'){
                        $head = $kanal;
                }else{
                    $chlist[$n]['Pr#'] = $kanal[1];
                    $chlist[$n]['ChannelName'] = $kanal[2];
                    // auf Kanal schalten und MainChannel XML auslesen
                    $key = 'KEY_CHUP'; 
                    $mc = $this->GetCurrentMainTVChannel_MTVA();
                    $chlist[$n]['ChType'] = $mc['ChType'];
                    $chlist[$n]['MAJORCH'] = $mc['MAJORCH'];
                    $chlist[$n]['MINORCH'] = $mc['MINORCH'];
                    $chlist[$n]['PTC'] = $mc['PTC'];
                    $chlist[$n]['PROGNUM'] = $mc['PROGNUM'];
                    $chlist[$n]['channelXml'] = "<Channel><ChType>".$chlist[$n]['ChType']."</ChType><MajorCh>".$chlist[$n]['MAJORCH']."</MajorCh><MinorCh>".$chlist[$n]['MINORCH']."</MinorCh><PTC>".$chlist[$n]['PTC']."</PTC><ProgNum>".$chlist[$n]['PROGNUM']."</ProgNum></Channel>" ;
                    $this->SendDebug("ChannelList ", $chlist[$n], 0);
                    $this->sendKey($key);
                    $n = $n + 1;
                }
                
        } 
        $chListSer = serialize($chlist);
        setvalue($this->GetIDForIdent("TVchList"), $chListSer);
        return  $chlist;
    }    
        
        
    //*****************************************************************************
    /* Function: Eigene Interne Funktionen.
    /* **************************************************************************** */	        
      
        
        
	//*****************************************************************************
	/* Function: Kernel()
        ...............................................................................
        Stammverzeichnis von IP Symcon
        ...............................................................................
        Parameter:  

        --------------------------------------------------------------------------------
        return:  

        --------------------------------------------------------------------------------
        Status  checked 11.6.2018
        //////////////////////////////////////////////////////////////////////////////*/
        Protected function Kernel(){ 
            $Kernel = str_replace("\\", "/", IPS_GetKernelDir());
            return $Kernel;
        }
        
        
	Protected function IPSLog($Text, $array) {
		$Directory=""; 
		$File="";
		
		if (!$array){
		
			$array = '-';
		}
		
		
		if ($File == ""){
		
			$File = 'IPSLog.log';
		}
		if ($Directory == "") {
			$Directory = "/home/pi/pi-share/";
			//$Directory = IPS_GetKernelDir().'/';
			//if (function_exists('IPS_GetLogDir'))
			//	$Directory = IPS_GetLogDir();
		}
		
		if(($FileHandle = fopen($Directory.$File, "a")) === false) {
			//SetValue($ID_OutEnabled, false);
			Exit;
		}
		if (is_array($array)){
			//$comma_seperated=implode("\r\n",$array);
			$comma_seperated=print_r($array, true);
		}
		else {
			$comma_seperated=$array;
		}
		fwrite($FileHandle, $Text.": ");
		fwrite($FileHandle, $comma_seperated."\r\n");
		fclose($FileHandle);
                
        }        

       
        protected function searcharray($value, $key, $array) {
           foreach ($array as $k => $val) {
               if ($val[$key] == $value) {
                   return $k;
               }
           }
           $this->SendDebug("searcharray ", "nicht GEFUNDEN!", 0);
           return null;
        }
  
        
        
        
        protected function searchForValue($value, $prop, $array) {
           $this->SendDebug("searchForValue ", $value." in Prop ".$prop, 0);
           if ($prop=="ChannelName"){
               // $this->SendDebug("searchForValue ", $array, 0);
           }
           foreach ($array as $key => $val) {
               $x =  $val[$prop]; 
               //$this->SendDebug("searchForValue vergleiche: ", $x." mit ".strval($value), 0);
               if ( $x == $value ) {
                   
                   $this->SendDebug("searchForValue ", $key." Wert  gefunden.", 0);
                   return $key;
               }
           }
           $this->SendDebug("searchForValue ",  " Wert  nicht gefunden.", 0);
           return "null" ;
        }
        
}
