<?php
//zugehoerige TRAIT-Klassen    TEST xxxy
require_once(__DIR__ . "/SamsungTV_Interface.php");
require_once(__DIR__ . "/../libs/NetworkTraits.php");



class MySamsungTV extends IPSModule
{
    
    //externe Klasse einbinden - ueberlagern mit TRAIT.
    use SamsungUPNP,
        MyDebugHelper;
     
    //*****************************************************************************
    /* Function: Standardfunktinen für ein Modul. 
    ...............................................................................
    *  Create()
     * ApplyChanges()
     * 
    /* **************************************************************************** */
    public function Create()
    {
	//Never delete this line!
        parent::Create();
        //These lines are parsed on Symcon Startup or Instance creation   
        // Variable aus dem Instanz Formular registrieren (um zugänglich zu machen)
        $this->RegisterPropertyBoolean("aktiv", false);
        $this->RegisterPropertyString("ip", "192.168.178.135");
        $this->RegisterPropertyInteger("updateInterval", 10000);	
        $this->RegisterPropertyInteger("devicetype", 1);
        
        //Variable anlegen.
        $this->RegisterVariableString("TVchList", "ChannelList");
        $this->RegisterVariableInteger("TVVolume", "Volume", "");
        $this->RegisterVariableInteger("TVChannel", "Channel", "");
        $this->RegisterVariableString("TVchLName", "ChannelName");
        $this->RegisterVariableString("TVGuide", "Guide");
        $this->RegisterVariableString("TVSource", "Source");
        $this->RegisterVariableString("TVSourceList", "SourceList");
        $this->RegisterVariableString("TVChIcon", "ChannelIcon");
        $this->RegisterVariableBoolean("TVPower", "Power");
        $this->RegisterVariableString("TVchProgList", "ChannelProgList");
        $this->RegisterVariableString("TVProgList", "ProgList");
        
        //Switch Profil zuordnen 
        IPS_SetVariableCustomProfile($this->GetIDForIdent("TVPower"), "~Switch");
      
        // Timer erstellen
        $this->RegisterTimer("update", $this->ReadPropertyInteger("updateInterval"), 'STV_update($_IPS[\'TARGET\']);');
        $this->RegisterTimer("watchdog", 60000, 'STV_watchdog($_IPS[\'TARGET\']);');
        
    }
    
    // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
    // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung.)
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
                $chName = getvalue($this->GetIDForIdent("TVchLName"));
                $this->getChExtTVlist($chName);
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
	Funktion wird über Timer alle x Sekunden gestartet, wenn TV über ping erreichbar = eingeschaltet
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
                $this->SendDebug("TVProg ", "Update gestartet", 0);

             
       
            $ip = $this->ReadPropertyString('ip');
            $alive = Sys_Ping($ip, 1000);
            if ($alive){
                setvalue($this->GetIDForIdent("TVPower"), true);
                $vol = $this->getVolume();  

                /*
                  
                $channel = $this->getChannel();                 
                $chName = $this->getCurrentChannelName();
                $source = $this->getCurrentSource();
                $sourceList = $this->getSourceList();
                 
                 */
            }
            else{
                $this->SetTimerInterval("update", 0);
                setvalue($this->GetIDForIdent("TVPower"), false);
                setvalue($this->GetIDForIdent("TVGuide"),"");
                $SourceList = json_decode(getvalue($this->GetIDForIdent("TVSourceList")), true);
                foreach ($SourceList as $key => $value) {
                    $SourceList[$key]["CONNECTED"] = "No";
                    $SourceList[$key]["active"] = "No";
                }
                setvalue($this->GetIDForIdent("TVSourceList"), json_encode($SourceList));
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
    Status:  17.07.2018 - OK  - (TelNet = OK)
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getVolume() {
         $vol = $this->GetVolume_RC($channel = 'Master');
        SetValue($this->GetIDForIdent("TVVolume"), (int)$vol);  
        return (int)$vol;
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
     * und schreibt Ergebnis in die Variable ChannelName. 
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $chName (string)
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getCurrentChannelName() {
        $ch = $this->GetCurrentMainTVChannel_MTVA();
        $such = $ch['MAJORCH'];
        $prop = "MAJORCH";
        $chListSer = getValue($this->GetIDForIdent("TVchList"));
        $chList = unserialize($chListSer);
        
        $key = $this->searchForValue($such, $prop, $chList);
        $chN = $chList[$key]['ChannelName'];
       
        $chName = substr($chN,1,strlen($chN)-2);
        SetValue($this->GetIDForIdent("TVchLName"),(string)$chN );  
        //HTMLBox Variable beschreiben
        $file = '<img src="user/'.$chList[$key]['ICONURL'].' "/>';
        SetValue($this->GetIDForIdent("TVChIcon"), $file);  
        
        return  $chList[$key]['$chName'];
    }   
  
    
     //*****************************************************************************
    /* Function: T_setChannelbyName(string $ChName) 
    ...............................................................................
     * schaltet auf den übergebenen SenderNamen um
     * mit TELNET Befehl 
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $chName (string)
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/  
    public function T_setChannelbyName(string $ChName) {
        $chList = $this->readChannelFile();
        //setValue($this->GetIDForIdent("TVchList"), $chListSer);
         //$chList = unserialize($chListSer); 
        //$this->SendDebug("Such Array ",   $chList, 0);
        $searchvalue = $ChName;
        $key = "NAME";
        $array = $chList;
        //$this->SendDebug("setChannelbyName ", "Suchwert: ". $searchvalue, 0);
        
        $result = $this->searcharray($searchvalue, $key, $array);
        
        if($result){
           $ch =  $chList[(int)$result];
           $this->SendDebug("setChannelbyName ", "found: ".$ChName." in".$result, 0);
           $ChType     = $ch['ChType'];
           $MajorCh    = $ch['MAJORCH'];        
           $MinorCh    = $ch['MINORCH'];       
           $PTC        = $ch['PTC'];  
           $ProgNum    = $ch['PROGNUM'];      
           $channel = "<Channel><ChType>".$ChType."</ChType><MajorCh>".$MajorCh."</MajorCh><MinorCh>".$MinorCh."</MinorCh><PTC>".$PTC."</PTC><ProgNum>".$ProgNum."</ProgNum></Channel>" ;
           setValue($this->GetIDForIdent("TVChannel"), $MajorCh);
           setValue($this->GetIDForIdent("TVchLName"), $ChName);
           //$this->SendDebug("send Telenet Command ", "KEY_".$MajorCh, 0); 
           
            if(intval($MajorCh)<10){
                $key = 'KEY_'.$MajorCh; 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
            elseif(intval($MajorCh)<100){
                $key = 'KEY_'.substr($MajorCh,0,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($MajorCh,1,1); 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
            else {
                $key = 'KEY_'.substr($MajorCh,0,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($MajorCh,1,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($MajorCh,2,1); 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
        }
        else {
           $this->SendDebug("setChannelbyName ",  $searchvalue." not found", 0);  
        }
        
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
        
        
        $searchvalue = $ChName;
        $key = "ChannelName";
        $array = $chList;
        $this->SendDebug("setChannelbyName ", "Suchwert: ". $searchvalue, 0);
        
        $result = $this->searcharray($searchvalue, $key, $array);
        if($result){
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
        else {
           $this->SendDebug("setChannelbyName ",  $searchvalue." not found", 0);  
        }
            
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
        if ($TVGuideURL == false){
            $this->SendDebug("getTVGuide ", "TV ausgeschaltet", 0);
        }else{    
            $this->SendDebug("getTVGuide ", $TVGuideURL, 0);

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

            $my_file ="programmliste.xml"; 
            $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
            fwrite($handle, $dataxml);       

            //xml laden
            //$xml = simplexml_load_file($dataxml);

            //$file = file_get_contents('programmliste.dat');
            $xml = simplexml_load_file('programmliste.xml');


            $channels= array("Das Erste", "ZDF", "RTL Television", "ProSieben", "kabel eins", "RTL2", "SAT.1", "3sat", "VOX", "Tele 5", "ONE", "RTLplus" );
            $i=0;
            $TVGuide = array();
            foreach($channels as $ch){
                foreach($xml->ProgramInfo as $elem){
                    if($elem->DispChName == $ch){
                            $TVGuide[$i]['DispChName'] = (string)($elem->DispChName);
                            $TVGuide[$i]['Time'] = $elem->StartTime." - ".$elem->EndTime;
                            $TVGuide[$i]['ProgTitle'] = (string) $elem->ProgTitle;
                            //$Guide = $Guide.$TVGuide[$i]['DispChName'].";".$TVGuide[$i]['Time'].";".$TVGuide[$i]['ProgTitle'].";";
                            $i=$i+1;
                    }
                }

            }
            
            setvalue($this->GetIDForIdent("TVGuide"), json_encode($TVGuide));
            return $TVGuide;
             
        }
    }

    
    //*****************************************************************************
    /* Function: getSourceList() 
    ...............................................................................
     * liest die vorhandenen Sources aus und welcher aktiv ist
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     *  SourceList
     *      => Array
            (
                [SOURCETYPE] => TV
                [ID] => 0
                [EDITABLE] => No
                [DEVICENAME] => 3
                [CONNECTED] => Yes
                [SUPPORTVIEW] => Yes
                [active] => Yes
            )
     
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getSourceList()  {
        $result = $this->GetSourceList_MTVA();
 
        $i = 0; 
        foreach ($result["SOURCE1"] as $source) {
            $sourceList[$i]["SOURCETYPE"] = $source["SOURCETYPE"];
            $sourceList[$i]["ID"] = $source["ID"];
            $sourceList[$i]["EDITABLE"] = $source["EDITABLE"];
            $sourceList[$i]["DEVICENAME"] = $source["DEVICENAME"];
            $sourceList[$i]["CONNECTED"] = $source["CONNECTED"];
            $sourceList[$i]["SUPPORTVIEW"] = $source["SUPPORTVIEW"];
            if($source["SOURCETYPE"] === $result["CURRENTSOURCETYPE"]){
                $sourceList[$i]["active"] = "yes";
            }
            else {
                $sourceList[$i]["active"] = "no";
            }
            $i = $i +1;
        }
        setvalue($this->GetIDForIdent("TVSourceList"),json_encode($sourceList));
        return $sourceList;
    }
    
    
    //*****************************************************************************
    /* Function: getCurrentSource() 
    ...............................................................................
     * liest die aktuell angewählte source Kanal aus
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     *  Source
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getCurrentSource()  {
        $result = $this->GetCurrentExternalSource_MTVA();
        $source = $result['CurrentExternalSource'];
        setvalue($this->GetIDForIdent("TVSource"),$source);
        return $source;
    }      
    
    //*****************************************************************************
    /* Function: sendWWW(string $URL) 
    ...............................................................................
     * startet browser mit www Seite
     *  
    ...............................................................................
    Parameters: $URL
    --------------------------------------------------------------------------------
    Returns:  
     *  
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/  
    public function sendWWW(string $URL)  {
        $result = $this->RunBrowser_MTVA($URL);
    }  
    
    
    /*//////////////////////////////////////////////////////////////////////////////
    Befehl: ToggleMute()
    ...............................................................................
    Toggled Befehl "Mute"
    ...............................................................................
    Parameter:  none
    --------------------------------------------------------------------------------
    SetValue:   
    --------------------------------------------------------------------------------
    return: none 
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/	
    Public function ToggleMute(){
        $result = $this->GetMuteStatus_MTVA();
        $state = $result['MuteStatus'];
        //$this->SendDebug("ToggleMute ", $state,0);
        if ($state == "Disable"){
            $this->SetMute_MTVA('Enable');
        }
        else{
           $this->SetMute_MTVA('Disable');
        }	
    }   
    
    
    /*//////////////////////////////////////////////////////////////////////////////
    Befehl: incVolume()
    ...............................................................................
    Lautstärke erhöhen
    ...............................................................................
    Parameter:  none
    --------------------------------------------------------------------------------
    SetValue:   -
    --------------------------------------------------------------------------------
    return: none 
    --------------------------------------------------------------------------------
    Status:   25.7.2018
    //////////////////////////////////////////////////////////////////////////////*/	
    Public function incVolume(){   
        $actVol = $this->GetVolume_MTVA();
        $this->SendDebug("incVolume ", $actVol,0);
        $vol = intval($actVol) + 1;
        $this->SendDebug("incVolume ", $vol,0);
        $this->SetVolume_MTVA((string)$vol);
    
    }
    
    
    /*//////////////////////////////////////////////////////////////////////////////
    Befehl: decVolume()
    ...............................................................................
    Lautstärke verringern 
    ...............................................................................
    Parameter:  none
    --------------------------------------------------------------------------------
    SetValue:   
    --------------------------------------------------------------------------------
    return: none 
    --------------------------------------------------------------------------------
    Status:    25.7.2018
    //////////////////////////////////////////////////////////////////////////////*/	
    Public function decVolume(){   
        $actVol = $this->GetVolume_MTVA();
                $this->SendDebug("incVolume ", $actVol,0);
        $vol = intval($actVol) - 1;
                $this->SendDebug("incVolume ", $vol,0);
        $this->SetVolume_MTVA((string)$vol);
    
    }    
    
    
     //*****************************************************************************
    /* Function: setSource
    ...............................................................................
     *  
     *  
    ...............................................................................
    Parameters: $source
    --------------------------------------------------------------------------------
    Returns:  
     * true, false
    --------------------------------------------------------------------------------
    Status:      
    //////////////////////////////////////////////////////////////////////////////*/  
    public function setSource($source) { 
        // read sourcelist, if available as variable otherwise read from TV
        
        $SourceList = json_decode(getvalue($this->GetIDForIdent("TVSourceList")), true);
        if (empty($SourceList)){
            $SourceList = $this->getSourceList();
        }
        try {
            // run your code here
            $i = $this->searcharray($source, "SOURCETYPE", $SourceList);
            if ($i === NULL){
                throw new Exception("Source could not be found!");
            }
            $ID = intval($SourceList[$i]["ID"]);
            $connected = $SourceList[$i]["CONNECTED"];
            if ($connected === "No"){
                throw new Exception("Source is not connected!");
            }
            $this->SetMainTVSource_MTVA($source, $ID);
        }

        catch (Exception $ex) {
            //code to handle the exception
            $this->SendDebug("setSource ", $source."  - ".$ex->getMessage(), 0);
            return false;
        }
 
        return true;  
    }     
    
    
    
    
     //*****************************************************************************
    /* Function: getChExtTVlist(string $ChName) 
    ...............................................................................
     *  COMEDY CENTRAL
     *  ProSieben MAXX
     *  Das Erste
     *  ZDF 
     *  RTL
     *  SAT.1
     *  ProSieben 
     *  kabel eins
     *  RTL II
     *  VOX
     *  TELE 5
     *  3sat
     *  ARTE
     *  ZDFneo
     *  ONE
     *  ServusTV Deutschland
     *  NITRO
     *  DMAX
     *  sixx
     *  SAT.1 Gold
     * 
     *
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * $chName (string)
    --------------------------------------------------------------------------------
    Status:   
    //////////////////////////////////////////////////////////////////////////////*/  
    public function getChExtTVlist(string $ChName) {
        $this->SendDebug("TVProg ", "Lese Programmliste", 0);
        // TV Spielfilm 
        $url = 'http://www.tvspielfilm.de/tv-programm/rss/jetzt.xml';      // TV Programm JETZT 
        //$url = 'http://www.tvspielfilm.de/tv-programm/rss/heute2015.xml';  // TV Programm 20.15 Uhr 
        //$url = 'http://www.tvspielfilm.de/tv-programm/rss/heute2200.xml';  // TV Programm 22.00 Uhr 
        //$url = 'http://www.tvspielfilm.de/tv-programm/rss/filme.xml';      // TV Programm SPIELFILME 
        //$url = 'http://www.tvspielfilm.de/news/rss.xml';                   // TV News 

        switch ($ChName) {
            case "COMEDY CENTRAL":
                $ChName = "COMEDY CENTRAL";
                break;
            case "PRO7MAXX":
                $ChName = "ProSieben MAXX";
                break;
            case "ARD HD":
                $ChName = "Das Erste";
                break;
            case "ZDF HD":
                $ChName = "ZDF";
                break;
            case "RTL":
                $ChName = "RTL";
                break;
            case "SAT1":
                $ChName = "SAT.1";
                break;
            case "PRO7":
                $ChName = "ProSieben";
                break;
            case "KABEL1":
                $ChName = "kabel eins";
                break;
            case "RTL2":
                $ChName = "RTL II";
                break;
            case "VOX":
                $ChName = "VOX";
                break;
            case "TELE5":
                $ChName = "TELE 5";
                break;
            case "3sat":
                $ChName = "3sat";
                break;
            case "ARTE HD":
                $ChName = "ARTE";
                break;
            case "ZDF neo HD":
                $ChName = "ZDFneo";
                break;
            case "ONE HD":
                $ChName = "ONE";
                break;
            case "Servus TV":
                $ChName = "ServusTV Deutschland";
                break;
            case "Nitro":
                $ChName = "NITRO";
                break;
            case "DMAX":
                $ChName = "DMAX";
                break;
            case "SIXX":
                $ChName = "sixx";
                break;
            case "SAT1GOLD":
                $ChName = "SAT.1 Gold";
                break;
            default:
                break;
        }

// AB HIER NICHTS MEHR ÄNDERN 
//////IPS_SetScriptTimer($_IPS['SELF'], $refreshtime); 
        $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA); 
        $str =  "<table width='auto'>"; 
        $strA =  "<table width='auto'>"; 
    // Datenausgabe 
 
   $xmlstring = $xml->channel; 
    
    $json = json_encode($xmlstring);
    $array = json_decode($json,TRUE);
 
    foreach ($array["item"]  as $item) {
         $teile = explode(" | ", $item['title']);
         //$this->SendDebug("TVProg ", $teile, 0);
        if($teile[1]=== $ChName){
            if (is_string($item['title']))  { 
                 $this->SendDebug("TVProg ", "Programm gefunden", 0);
            } 
            else  { 
                 $this->SendDebug("TVProg ", "Programm not founde", 0);
              continue; 
            } 
            $titel = "<b style=color:#C00000;>".$item['title']."</b>"; 

            if (is_string($item['description']))  { 
                $beschreibung = "<small style=color:white;>".$item['description']."</small>"; 
            } 
            else  { 
              $beschreibung = "<small></small>"; 
            } 

            $text = $titel."<br>".$beschreibung."<br>"; 
            //$text = utf8_decode($text); 
            $searchArray = $item; 

            // IF-Abfrage, wenn Array zu Ende, dann abbrechen 
            //if(isset($searchArray['enclosure']) != true)  { 
            //   break; 
            //} 
            if(array_key_exists('enclosure', $searchArray)) 
            { 
                $image = $item['enclosure']['@attributes']['url']; 
               $str .= "<tr>"; 
               $str .= "<td width='auto'height='80px'><div><img src=$image alt='not Found'></div></td>"; 
               $str .= "<td width='980px'><div style='text-align:left; margin-left:10px;'>$text</div>"; 
               $str .= "</td></tr>\n"; 
            } 
            else 
            { 
               $str .= "<tr>"; 
               $str .= "<td></td><td width='980px'><div style='text-align:left; margin-left:10px;'>$text</div></td>"; 
               $str .= "</tr>\n"; 
            } 
        }
            //alle Sender sammeln
            if (is_string($item['title']))  { 
            } 
            else  { 
              continue; 
            } 
            $titelA = "<b style=color:lime;>".$item['title']."</b>"; 

            if (is_string($item['description']))  { 
                $beschreibungA = "<small style=color:white;>".$item['description']."</small>"; 
            } 
            else  { 
              $beschreibungA = "<small></small>"; 
            } 

            $textA = $titelA."<br>".$beschreibungA."<br>"; 
            //$text = utf8_decode($text); 
            $searchArray = $item; 

            // IF-Abfrage, wenn Array zu Ende, dann abbrechen 
            //if(isset($searchArray['enclosure']) != true)  { 
            //   break; 
            //} 
            if(array_key_exists('enclosure', $searchArray)) 
            { 
                $imageA = $item['enclosure']['@attributes']['url']; 
               $strA .= "<tr>"; 
               //$strA .= "<td width='auto'height='80px'><div><img src=$imageA alt='not Found'></div></td>"; 
               $strA .= "<td width='980px'><div style='text-align:left; margin-left:10px;'>$textA</div>"; 
               $strA .= "</td></tr>\n"; 
            } 
            else 
            { 
               $strA .= "<tr>"; 
               $strA .= "<td></td><td width='980px'><div style='text-align:left; margin-left:10px;'>$textA</div></td>"; 
               $strA .= "</tr>\n"; 
            } 
            

        
    }
    
        $str .= "</table>\n"; 
        $this->SendDebug("TVProg ", "Schreibe Prog in Variable".$str, 0);
        setvalue($this->GetIDForIdent("TVchProgList"),$str);
        $strA .= "</table>\n"; 
        setvalue($this->GetIDForIdent("TVProgList"),$strA);
        
        return $strA; 



    }
    
 
    
    
    
    
    //*****************************************************************************
    /* Function: buildChannelList() 
    ...............................................................................
     * erzeugt ein Array aller Channels
     * 
     *  
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
        $chURL =  $this->GetChannelListURL_MTVA();
        $url = $chURL["ChannelListURL"];
        $input = file_get_contents($url);
        $len = strlen($input);
        $offset = 124;
        $anzahl = floor($len / 124)-1;
        $chlist = array();

        for ($i = 0; $i <= $anzahl; $i++) {
            $chlist[$i]['Kanal'] = rtrim(substr($input,16 + $i*$offset, 3));
            $chlist[$i]['ChannelName'] = rtrim(substr($input,28 + $i*$offset, 20));
         }
         
        $n = 0;
 
            
        foreach($chlist as $ch) {
            $kanal = $ch["Kanal"];
            $name = $ch["ChannelName"];
            // auf Kanal schalten und MainChannel XML auslesen
            if(intval($kanal)<10){
                $key = 'KEY_'.$kanal; 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
            elseif(intval($kanal)<100){
                $key = 'KEY_'.substr($kanal,0,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($kanal,1,1); 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
            else {
                $key = 'KEY_'.substr($kanal,0,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($kanal,1,1); 
                $this->sendKey($key);
                $key = 'KEY_'.substr($kanal,2,1); 
                $this->sendKey($key);
                $key = 'KEY_ENTER';
                $result =   $this->sendKey($key);   
            }
            $mc = $this->GetCurrentMainTVChannel_MTVA();
            $chlist[$n]['ChType'] = $mc['ChType'];
            $chlist[$n]['MAJORCH'] = $mc['MAJORCH'];
            $chlist[$n]['MINORCH'] = $mc['MINORCH'];
            $chlist[$n]['PTC'] = $mc['PTC'];
            $chlist[$n]['PROGNUM'] = $mc['PROGNUM'];
            $chlist[$n]['channelXml'] = "<Channel><ChType>".$chlist[$n]['ChType']."</ChType><MajorCh>".$chlist[$n]['MAJORCH']."</MajorCh><MinorCh>".$chlist[$n]['MINORCH']."</MinorCh><PTC>".$chlist[$n]['PTC']."</PTC><ProgNum>".$chlist[$n]['PROGNUM']."</ProgNum></Channel>" ;
            // search for icon
            $chlist[$n]['ICONURL'] = "images/Sender/".$name.".png";
            $this->SendDebug("ChannelList ", $chlist[$n], 0);
            $n = $n + 1;        
            
        } 
        $chListSer = serialize($chlist);
        setvalue($this->GetIDForIdent("TVchList"), $chListSer);
        file_put_contents("/var/lib/symcon/media/channels.json",json_encode($chlist));
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

       
        Protected function searcharray($value, $key, $array) {
           foreach ($array as $k => $val) {
                $this->SendDebug("searcharray vergleiche: ", $val[$key]." mit ".  $value  , 0);
               if ($val[$key] == $value) {
                   $this->SendDebug("Suchergebnis ", $key, 0);
                   return $k;
               }
           }
           $this->SendDebug("searcharray ", "nicht GEFUNDEN!", 0);
           return null;
        }
  
        
        
        
        Protected function searchForValue($value, $prop, $array) {
           $this->SendDebug("searchForValue ", $value." in Prop ".$prop, 0);
           if ($prop=="ChannelName"){
               // $this->SendDebug("searchForValue ", $array, 0);
           }
           foreach ($array as $key => $val) {
               $x =  $val[$prop]; 
               //$this->SendDebug("searchForValue vergleiche: ", $x." mit ".strval($value), 0);
               if ( $x == $value ) {
                   
                   $this->SendDebug("searchForValue ", $value." Wert  gefunden.", 0);
                   return $key;
               }
           }
           $this->SendDebug("searchForValue ",  " Wert  nicht gefunden.", 0);
           return "null" ;
        }
        
        
        Protected function readChannelFile() {
            // Read JSON file
            $dataPath = IPS_GetKernelDir() . '/modules/MySymCodes/MySamsungTV/';
            $json = file_get_contents($dataPath.'channels.json');
            //Decode JSON
            // true = json als array ausgeben
            $json_data = json_decode($json,true);
            return $json_data;
        }    
}
