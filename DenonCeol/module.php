<?php
//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/DenonCeol_Interface.php");
require_once(__DIR__ . "/../libs/XML2Array.php");
require_once(__DIR__ . "/../libs/NetworkTraits.php");

    // Klassendefinition
    class DenonCeol extends IPSModule {
        //externe Klasse einbinden - ueberlagern mit TRAIT
        use CEOLupnp;
        use XML2Array;
        use MyDebugHelper;
                        
        
        // Der Konstruktor des Moduls
        // Überschreibt den Standard Kontruktor von IPS
        public function __construct($InstanceID) {
            // Diese Zeile nicht löschen
            parent::__construct($InstanceID);
            
                        
           
        }
        
        // Create() wird einmalig beim Erstellen einer neuen Instanz ausgeführt
        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht löschen.
            parent::Create();
            
            // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            $this->RegisterPropertyBoolean("active", false);
            $this->RegisterPropertyString("IPAddress", "");
            $this->RegisterPropertyInteger("UpdateInterval", 5000);
           
            
            //Status Variable anlegen
            $this->RegisterVariableInteger("CeolSource", "Source", "");
            $this->RegisterVariableBoolean("CeolPower", "Power");
            $this->RegisterVariableInteger("CeolVolume", "Volume", "");
            $this->RegisterVariableBoolean("CeolMute", "Mute");
            $this->RegisterVariableString("CeolSZ1", "Line1");
            $this->RegisterVariableString("CeolSZ2", "Line2");
            $this->RegisterVariableString("CeolSZ3", "Line3");
            $this->RegisterVariableString("CeolSZ4", "Line4");      
            $this->RegisterVariableString("CeolSZ5", "Line5");
            $this->RegisterVariableString("CeolSZ6", "Line6");
            $this->RegisterVariableString("CeolSZ7", "Line7");
            $this->RegisterVariableString("CeolSZ8", "Line8"); 
            $this->RegisterVariableInteger("CeolFavChannel", "FavChannel", "");
            $this->RegisterVariableString("CeolArtPicUrl", "ArtPicUrl"); 
            
                
            //UPNP Variable
            $this->RegisterVariableString("Ceol_ServerArray", "Server:Array");
            $this->RegisterVariableString("Ceol_ServerContentDirectory", "Server:ContentDirectory");
            $this->RegisterVariableString("Ceol_ServerIcon", "Server:Icon");
            $this->RegisterVariableString("Ceol_ServerIP", "Server:IP");
            $this->RegisterVariableInteger("Ceol_ServerKey", "Server:Key", "");
            $this->RegisterVariableString("Ceol_ServerName", "Server:Name");
            $this->RegisterVariableString("Ceol_ServerPort", "Server:Port");

            
            $this->RegisterVariableString("Ceol_Artist", "DIDL_Artist [dc:creator]");
            $this->RegisterVariableString("Ceol_Album", "DIDL_Album [upnp:album]");
            $this->RegisterVariableString("Ceol_Title", "DIDL_Titel [dc:title]");
            $this->RegisterVariableString("Ceol_Actor", "DIDL_Actor [upnp:actor]");
            $this->RegisterVariableString("Ceol_AlbumArtUri", "DIDL_AlbumArtURI [upnp:albumArtURI]");
            $this->RegisterVariableString("Ceol_Genre", "DIDL_Genre [upnp:genre]");
            $this->RegisterVariableString("Ceol_Date", "DIDL_Date [dc:date]");
            
            $this->RegisterVariableInteger("Ceol_PlayMode", "PlayMode", "UPNP_Playmode");
            $this->RegisterVariableInteger("Ceol_NoTracks", "No of tracks", "");
            $this->RegisterVariableString("Ceol_PlaylistName", "PlaylistName");
            $this->RegisterVariableString("Ceol_Playlist_XML", "Playlist_XML");  
            $this->RegisterVariableInteger("Ceol_Progress", "Progress", "UPNP_Progress");
            $this->RegisterVariableInteger("Ceol_Track", "Pos:Track", "");
            $this->RegisterVariableString("Ceol_Transport_Status", "Pos:Transport_Status");
            $this->RegisterVariableString("Ceol_RelTime", "RelTime");
            $this->RegisterVariableString("Ceol_TrackDuration", "TrackDuration");
            
            // Aktiviert die Standardaktion der Statusvariable im Webfront
            $this->EnableAction("CeolPower");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("CeolPower"), "~Switch");
            $this->EnableAction("CeolMute");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("CeolMute"), "~Switch");
            $this->EnableAction("CeolSource");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("CeolSource"), "DenonCEOL_Source");
            $this->EnableAction("CeolVolume");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("CeolVolume"), "DenonCEOL_Volume");
            $this->EnableAction("CeolFavChannel");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("CeolFavChannel"), "");
            $this->EnableAction("Ceol_PlayMode");
            IPS_SetVariableCustomProfile($this->GetIDForIdent("Ceol_PlayMode"), "UPNP_Playmode");
            
            // Objekte unsichbar machen in webfront
            IPS_SetHidden($this->GetIDForIdent("Ceol_ServerArray"), true); //Objekt verstecken
            IPS_SetHidden($this->GetIDForIdent("Ceol_ServerContentDirectory"), true); //Objekt verstecken
            IPS_SetHidden($this->GetIDForIdent("Ceol_ServerKey"), true); //Objekt verstecken
            IPS_SetHidden($this->GetIDForIdent("Ceol_ServerArray"), true); //Objekt verstecken
            IPS_SetHidden($this->GetIDForIdent("Ceol_Playlist_XML"), true); //Objekt verstecken
            
            
            
            
            //
            // Timer erstellen
            $this->RegisterTimer("Update", $this->ReadPropertyInteger("UpdateInterval"), 'CEOL_update($_IPS[\'TARGET\']);');
            // Progress Timer erstellen
            $this->RegisterTimer("Ceol_PlayInfo", 1000,  'CEOL_GetPosInfo(' . $this->InstanceID . ');');

        }
        
        // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
        // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            
            if($this->ReadPropertyBoolean("active")){
                $this->SetTimerInterval("Update", $this->ReadPropertyInteger("UpdateInterval"));
                $this->CeolInit();
            }
            else {
                $this->SetTimerInterval("Update", 0);
            }
        }
 
        public function RequestAction($Ident, $Value) {
            switch($Ident) {
                case "CeolPower":
                    //Hier würde normalerweise eine Aktion z.B. das Schalten ausgeführt werden
                    //Ausgaben über 'echo' werden an die Visualisierung zurückgeleitet
                
                    //$this->SetPower($Value);
                    //Neuen Wert in die Statusvariable schreiben
                    //SetValue($this->GetIDForIdent($Ident), $Value);
                        if($Value){
                            $host = $this->ReadPropertyString('IPAddress');
                            $url = "http://$host:80/goform/formiPhoneAppPower.xml";
                            $cmd = '1+PowerOn';
                            $xml = $this->curl_get($url, $cmd);
                        }
                        else{
                            $host = $this->ReadPropertyString('IPAddress');
                            $url = "http://$host:80/goform/formiPhoneAppPower.xml";
                            $cmd = '1+PowerStandby';
                            $xml = $this->curl_get($url, $cmd);
                        }
                    break;
                case "CeolSource":
                    break;
                case "CeolVolume":
                    break;
                case "CeolMute":
                        if($Value){
                            $this->SetMute_AV('1');
                            SetValueBoolean($this->GetIDForIdent("CeolMute"), true);
                        }
                        else{
                            $this->SetMute_AV('0');
                            SetValueBoolean($this->GetIDForIdent("CeolMute"), false);
                        }
                    break;
                case "CeolFavChannel":
                    break;
                default:
                    throw new Exception("Invalid Ident");
            }

        } 
        
        
        
        
        
        
        
        
        
        
        /**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
        *
        * CEOL_XYFunktion($id);
        *
        */

        
        private function CeolInit(){
            
            $this->ip = $this->ReadPropertyString('IPAddress');
        }
        
	/*//////////////////////////////////////////////////////////////////////////////
	Funktion:  update()
	...............................................................................
	Funktion wird über Timer alle x Sekunden gestartet
         *  call SubFunctions:  $this->Get_MainZone_Status()
         *                      $this->get_audio_status(
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable:   CeolMute
                       CeolPower
                       CeolVolume
                       CeolSource
	--------------------------------------------------------------------------------
	return: none  
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/       
        public function update() {
            $ip = $this->ReadPropertyString('IPAddress');
            $alive = Sys_Ping($ip, 1000);
            if ($alive){
                //MainZoneStatus auslesen   
                $output = $this->Get_MainZone_Status();
                $power = ($output['item']['Power']['value']);
                $InputFuncSelect = ($output['item']['InputFuncSelect']['value']);
                $MasterVolume = ($output['item']['MasterVolume']['value']);
                $Mute = ($output['item']['Mute']['value']);
                if ($power == 'ON'){
                        $_power = true;
                }
                else{
                        $_power = false;
                        SetValueString($this->GetIDForIdent("CeolSZ2"), 'Denon CEOL Picolo');
                        SetValueString($this->GetIDForIdent("CeolSZ3"), 'ausgeshaltet');
                }
                SetValueBoolean($this->GetIDForIdent("CeolPower"), $_power);
                SetValueInteger($this->GetIDForIdent("CeolVolume"), $MasterVolume);
                if ($Mute == 'off'){
                        $_mute = false;
                }
                else{
                        $_mute = true;
                }
                SetValueBoolean($this->GetIDForIdent("CeolMute"), $_mute);
                //AudioStatus auslesen
                $output = $this->get_audio_status();		
                $sz1 = $output['item']['szLine']['value'][0];
                $sz2 = $output['item']['szLine']['value'][1];
                if (empty($sz2)){$sz2 = '- - - -';}
                $sz3 = $output['item']['szLine']['value'][2];
                if (empty($sz3)){$sz2 = '- - - -';}
                $sz4 = $output['item']['szLine']['value'][3];
                $sz5 = $output['item']['szLine']['value'][4];
                $sz6 = $output['item']['szLine']['value'][5];
                $sz7 = $output['item']['szLine']['value'][6];
                $sz8 = $output['item']['szLine']['value'][6];
                SetValueString($this->GetIDForIdent("CeolSZ1"), $sz1);
                SetValueString($this->GetIDForIdent("CeolSZ2"), substr($sz2, 0,60));
                SetValueString($this->GetIDForIdent("CeolSZ3"), $sz3);
                SetValueString($this->GetIDForIdent("CeolSZ4"), $sz4);
                SetValueString($this->GetIDForIdent("CeolSZ5"), $sz5);
                SetValueString($this->GetIDForIdent("CeolSZ6"), $sz6);
                SetValueString($this->GetIDForIdent("CeolSZ7"), $sz7);
                SetValueString($this->GetIDForIdent("CeolSZ8"), $sz8);
                
                $Source = $output['item']['NetFuncSelect']['value'];
                $this->SendDebug("get Source: ", $Source, 0);
                switch ($Source){
                        case "IRADIO":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 0);
                            //ArtistPicture suchen
                            $artistTitel = getvalue($this->GetIDForIdent("CeolSZ2"));
                            $dispLine2 = explode(" - ", $artistTitel);
                            $this->SendDebug("Line 2 array: ", $dispLine2, 0);
                            $size = 3;
                            $url = $this->getImageFromLastFM($dispLine2[0], $size);
                            setvalue($this->GetIDForIdent("CeolArtPicUrl"), $url);
                            setvalue($this->GetIDForIdent("Ceol_Artist"), $dispLine2[0]);
                            setvalue($this->GetIDForIdent("Ceol_Title"), $dispLine2[1]);
                        break;	
                        case "MediaServer":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 1);
                        break;	
                        case "USB":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 2);
                        break;	
                        case "IPOD":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 3);
                        break;	
                        case "AUX_A":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 4);
                        break;	
                        case "AUX_D":
                            SetValueInteger($this->GetIDForIdent("CeolSource"), 5);
                        break;		
                }

                        
            }
            else {
                //Keine Netzwerk-Verbindung zun Client
                $this->SendDebug("Meldung: ", "Keine Netzwerkverbindung zu Denon Ceol.", 0);
            }
        }
        
	/*//////////////////////////////////////////////////////////////////////////////	
	Befehl 	: Get_MainZone_Status()
	...............................................................................
	Liest MainZone Status aus
        HTTP Befehl:	http://192.168.178.29:80/goform/formMainZone_MainZoneXmlStatus.xml     
	...............................................................................				 
	setVariable: 
        --------------------------------------------------------------------------------
	Parameter:	host = String = Adresse von DENON CEOL
        --------------------------------------------------------------------------------
	Rückgabewert: 	$xml->array = output
	 	$output['item']['Zone']['value']		=>	MainZone
                $output['item']['Power']['value']		=>	ON // STANDBY
                $output['item']['Model']['value']		=>	
                $output['item']['MasterVolume']['value']	=>	NET
                $output['item']['MasterVolume']['value']	=>	-74.0
                $output['item']['Mute']['value']		=>	on // off
					output['item']['Mute']['value']
					---------------------------------------------
					<?xml version="1.0" encoding="UTF-8"?>
					<item>
						<Zone>
							<value>MainZone</value>
						</Zone>
						<Power>
							<value>ON</value>
						</Power>
						<Model>
							<value/>
						</Model>
						<InputFuncSelect>
							<value>NET</value>
						</InputFuncSelect>
						<MasterVolume>
							<value>-74.0</value>
						</MasterVolume>
						<Mute>
							<value>off</value>
						</Mute>
					</item>
	//////////////////////////////////////////////////////////////////////////////*/ 
	Public function Get_MainZone_Status(){
                $host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formMainZone_MainZoneXmlStatus.xml";
		$cmd = "";
		$xml = $this->curl_get($url, $cmd);
		$output = XML2Array::createArray($xml);
                //$this->SendDebug("MainZone: ", $output, 0);
		//$status = ($output['item']['Power']['value']);
                $this->SendDebug("MainZoneStatus: ", $xml, 0);
		return $output;
	}	
        
	/*//////////////////////////////////////////////////////////////////////////////	
	Befehl 	:	get_audio_status()
	...............................................................................
	Liest Audio Status aus
        HTTP Befehl:	$url = "http://192.168.178.29:80/goform/formNetAudio_StatusXml.xml";     
	...............................................................................				 
	setVariable: 
        --------------------------------------------------------------------------------
        Parameter:	none
	--------------------------------------------------------------------------------
        Rückgabewert: 	$xml->array = output
		$output['item']['MasterVolume']['value']        =>	Mastervolume Status
	 	$output['item']['szLine']['value'][0]		=>	Display Line 1
		$output['item']['szLine']['value'][1]		=>	Display Line 2
		$output['item']['szLine']['value'][2]		=>	Display Line 3
		$output['item']['szLine']['value'][3]		=>	Display Line 4
		$output['item']['szLine']['value'][4]		=>	Display Line 5
		$output['item']['szLine']['value'][5]		=>	Display Line 6
		$output['item']['szLine']['value'][6]		=>	Display Line 7
		$output['item']['szLine']['value'][7]		=>	Display Line 8
		$output['item']['NetFuncSelect']['value']	=>	Selected Input 
		$output['item']['Mute']['value']			=>	Mute Status
	
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function get_audio_status(){
                $host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formNetAudio_StatusXml.xml";
		$cmd = "";
		$xml = $this->curl_get($url, $cmd);
		//$this->SendDebug("AudioStatus: ", $xml, 0);
		$output = XML2Array::createArray($xml);
		return $output;
	}	 

	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: Navigate($Direction)
	...............................................................................
	Menü Navigation
        HTTP Befehl:  http://192.168.178.29:80/goform/formiPhoneAppNetAudioCommand.xml?CurLeft       
	Telnet Befehl: CurLeft // CurRight // CurUp // CurDown
	...............................................................................
	Parameter:  $value = left" // "right" // "up" // "down" // "ok"
	--------------------------------------------------------------------------------

	return:  
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
 	Public function Navigate($Direction){
		$host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formiPhoneAppNetAudioCommand.xml?";
		switch($Direction){
                    case 'left':
			$cmd = 'CurLeft';
			break;
                    case 'right':
			$cmd = 'CurRight';
			break;
                    case 'up':
			$cmd = 'CurUp';
			break;
                    case 'down':
			$cmd = 'CurDown';
			break;
                    case 'ok':
			$cmd = 'Enter';
			break;
		}
		
                $xml = $this->curl_get($url, $cmd);
		//$output = XML2Array::createArray($xml);
		return $xml;
	}	       

	/*//////////////////////////////////////////////////////////////////////////////
	Funktion SetPower($status)
	...............................................................................
	Denon Ceol Ein / Aus Schalten
	...............................................................................
	Parameter:  $status = "On" // "Standby" 
	--------------------------------------------------------------------------------
	HTTP-Command: http://192.168.178.29:80/goform/formiPhoneAppPower.xml?1+PowerOn
	--------------------------------------------------------------------------------
	return: $status = 'on' / 'Standby'   
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/        
	Public function GetPower(){
            $PowerStatus = GetValueBoolean($this->GetIDForIdent("CeolPower"));
            return $PowerStatus;
        }        
        
	/*//////////////////////////////////////////////////////////////////////////////
	Funktion SetPower($status)
	...............................................................................
	Denon Ceol Ein / Aus Schalten
	...............................................................................
	Parameter:  $status = "On" // "Standby" 
	--------------------------------------------------------------------------------
	HTTP-Command: http://192.168.178.29:80/goform/formiPhoneAppPower.xml?1+PowerOn
	--------------------------------------------------------------------------------
	return: $status = 'on' / 'Standby'  // 0 / 1 
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/        
	Public function SetPower($status){
		$host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formiPhoneAppPower.xml";
		if ($status == "On"){
                    $this->SendDebug('SetPower', 'Power: '.'einschalten', 0);
			$cmd = '1+PowerOn';
			$power=true;
		}
		if ($status == "Standby"){
			$cmd = '1+PowerStandby';
			$power=false;
		}
		$xml = $this->curl_get($url, $cmd);
		$output = XML2Array::createArray($xml);
 		$status = ($output['item']['Power']['value']);
		SetValueBoolean($this->GetIDForIdent("CeolPower"), $power);
		return $status;	
	}        

	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: SelectSource($Source)
	...............................................................................
	Funktion schaltet die Eingangs Quelle des Denon CEOL um:
				 0 = iRadio
				 1 = MediaServer
				 2 = USB
				 3 = IPOD
				 4 = AUX_A
				 5 = AUX_D
	...............................................................................
	Parameter:  $Source = "Radio" // "Server" // "USB" // "IPOD" // "AUX_A" // "AUX_D"
	--------------------------------------------------------------------------------
	HTTP Command: http://192.168.178.29:80/goform/formiPhoneAppDirect.xml?SIIRADIO
	Telnet Befehle: SIIRADIO // SISERVER // SIUSB // SIIPOD // SIAUXA // SIAUXD
        --------------------------------------------------------------------------------
        return: $command / false  
	--------------------------------------------------------------------------------
	Status: checked 2018-06 -03
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function SelectSource($Source){
            switch ($Source){
		case 'Radio':
                    $command = "SIIRADIO";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 0);
		break;
		case 'Server':
                    $command = "SISERVER";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 1);
		break;
		break;	
		case 'USB':
                    $command = "SIUSB";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 2);
		break;
		case 'IPOD':
                    $command = "SIIPOD";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 3);
		break;			
		case 'AUX_A':
                    $command = "SIAUXA";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 4);
		break;
		case 'AUX_D':
                    $command = "SIAUXD";
                    SetValueInteger($this->GetIDForIdent("CeolSource"), 5);
		break;
		default:
                    $this->SendDebug("Error: ", "Falscher Parameter", 0);
		break;		
            }
            $this->send_cmd($command);
            return $command;
	}	

	/*//////////////////////////////////////////////////////////////////////////////
	Funktion IncVolume() DecVolume()
	...............................................................................
	erhöht/senkt Lautstärke-Level um 1 an Denon CEOL
				 	- Lautstärke in % [0-100]   =  [-79dB ... -69dB] 
        HTTP Befehl: http://192.168.178.29t:80/goform/formiPhoneAppDirect.xml?MVUP
        Telnet Befehl  MDUP  / MVDOWN
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetValue:  CeolVolume
	--------------------------------------------------------------------------------
	return: true  
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function IncVolume(){
            $MasterVolume = getvalue($this->GetIDForIdent("CeolVolume")) + 1;
            if($MasterVolume < -65){
                SetValueInteger($this->GetIDForIdent("CeolVolume"), $MasterVolume);
                $this->send_cmd('MVUP');
                return true;
            }
            else{
                //oberer Einstellwert von -65db erreicht.  /Lautstärkebegrenzung
                return false;
            }            
	}	
	
	Public function DecVolume(){	
            $MasterVolume = getvalue($this->GetIDForIdent("CeolVolume")) - 1;
            if($MasterVolume > -80){
                SetValueInteger($this->GetIDForIdent("CeolVolume"), $MasterVolume);
                $this->send_cmd('MVDOWN');
                return true;
            }
            else{
                //unterer Einstellwert von -79db erreicht.
                return false;
            }
	}
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: ToggleMute()
	...............................................................................
	Toggled Befehl "Mute"
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetValue:  CeolMute
	--------------------------------------------------------------------------------
	return: true  
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function ToggleMute(){
            $state = GetValueBoolean($this->GetIDForIdent("CeolMute"));
            if ($state){
		$this->SetMute_AV('0');
		SetValueBoolean($this->GetIDForIdent("CeolMute"), false);
            }
            else{
		$this->SetMute_AV('1');
		SetValueBoolean($this->GetIDForIdent("CeolMute"), true);
            }	
            return true;	
	}


	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: SetVolumeDB($Volume)
	...............................................................................
        sendet Lautstärke-Level an Denon CEOL
		- Lautstärke in % [0-100]   =  [-79dB ... -69dB] 
                - Begrenzung auf -69dB
        HTTP Befehl:   http://192.168.178.29:80/goform/formiPhoneAppVolume.xml?1+-72.0      
	...............................................................................
	Parameter:  $Volume = Integer = [0 - 100] 
	--------------------------------------------------------------------------------
	Variable:  none
	--------------------------------------------------------------------------------
	return: $output['item']['MasterVolume']['value']
		$output['item']['Mute']['value']  
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function SetVolumeDB($Volume){
            $VoldB = -79.0 + ($Volume/10);
            $Wert =intval($VoldB);
            $Wert = str_replace(',', '.',$Wert);
            $cmd = '1+'.$Wert;
            $host = $this->ReadPropertyString('IPAddress');
            $url = "http://$host:80/goform/formiPhoneAppVolume.xml";
            $xml = $this->curl_get($url, $cmd);
            $output = XML2Array::createArray($xml);
            $VolDB = ($output['item']['MasterVolume']['value']);
            return $VolDB;
	}        
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: setBass($value)
	...............................................................................
	Erhöht Bass Level (Range: -10 ... +10) (40...60)
        HTTP Befehl: http://192.168.178.29:80/goform/formiPhoneAppDirect.xml?UP
	Telnet Befehl: PSBASS_UP // PSBAS_DOWN // PSBAS_50
	...............................................................................
	Parameter:  $value = "UP" // "DOWN" // "50"
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return  
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/
	Public function setBass($value){ 
		$cmd = 'PSBAS_'.$value;
		$xml = $this->send_cmd($cmd);
		return $xml;
	}	
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: setTreble($value)
	...............................................................................
	Erhöht Trebble Level (Range: -10 ... +10) (40...60)
        HTTP Befehl: http://192.168.178.29:80/goform/formiPhoneAppDirect.xml?UP
	Telnet Befehl: PSTRE_UP // PSTRE_DOWN // PSTRE_50
	...............................................................................
	Parameter:  $value = "UP" // "DOWN" // "50"
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return  
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/
	Public function setTreble($value){ 
		$cmd = 'PSTRE_'.$value;
		$xml = $this->send_cmd($cmd);
		return $xml;
	}	
	
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: setBalance($value)
	...............................................................................
	Verandert den Balance Level (Range: 00 ... 99)  
        HTTP Befehl: http://192.168.178.29:80/goform/formiPhoneAppDirect.xml?PSBAL_LEFT
	Telnet Befehl: PSBAL_LEFT // PSBAL_RIGHT // PSBAL_50 = Center
	...............................................................................
	Parameter:  $value = "LEFT" // "RIGHT" // "50"
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return  
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/
	Public function setBalance($value){ 
		$cmd = 'PSBAL_'.$value;
		$xml = $this->send_cmd($cmd);
		return $xml;
	}	
	

        
        
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: showClock()
	...............................................................................
	zeigt die Uhrzeit im Display .
	...............................................................................
	Parameter:   
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return  
	--------------------------------------------------------------------------------
	Status: checked 2018.06.18
	//////////////////////////////////////////////////////////////////////////////*/
	Public function showClock(){ 
		$cmd = 'CLK';
		$xml = $this->send_cmd($cmd);
		return $xml;
	}
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: setTimer()
	...............................................................................
	Wecker stellen und starten
	...............................................................................
	Parameter:  $mode = once/ every 
                    $startTime  = 07:30
                    $endTime    = 21:30    
                    $funct      = FA (Favorite)/ IP (IPOD) / US (USB)    
                    $n          = 01    (Favoriten Nummer)
                    $state      = on / off
         
         
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return : 
	--------------------------------------------------------------------------------
	Status: funktioniert nicht
	//////////////////////////////////////////////////////////////////////////////*/
	Public function setTimer($mode, $startTime, $endTime, $funct = 'FA', $n = '01', $volT = '03', $state){    
            $mode = strtoupper ($mode);
            $sT= explode(':', $startTime);
            $periodS = '2';

            $eT= explode(':', $endTime);
            $periodE = '2';
            if ($state == 'on'){
                $ts = '1';
            }
            else {
                $ts = '0';
            }
		$cmd = 'TS'.$mode.' '.$periodS.$sT[0].$sT[1].'-'.$periodE.$eT[0].$eT[1].' '.$funct.$n.' '.$volT.' '.$ts;
		$xml = $this->send_cmd($cmd);
		return $cmd;
	}     
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: switchTimer($stateTimerOnce,$stateTimerAlways)
	...............................................................................
	Wecker ein/aus schalten
	...............................................................................
	Parameter:  $stateTimerOnce      = on / off
                    $stateTimerAlways    = on / off
         
	--------------------------------------------------------------------------------
	Variable: 
	--------------------------------------------------------------------------------
	return : 
	--------------------------------------------------------------------------------
	Status: funktioniert nicht
	//////////////////////////////////////////////////////////////////////////////*/
	Public function switchTimer($stateTimerOnce,$stateTimerAlways){    
            $stateTimerOnce = strtoupper ($stateTimerOnce);
            $stateTimerAlways = strtoupper ($stateTimerAlways);
            $cmd = 'TO'.$stateTimerAlways.' '.$stateTimerAlways;
            
            $host = $this->ReadPropertyString('IPAddress');
            $url = "http://$host:80/goform/formiPhoneAppNetAudioCommand.xml";
            $xml = $this->curl_get($url, $cmd);
            //$xml = $this->send_cmd($cmd);
            return $cmd;
	}  
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: SetRadioChannel($Channel)
	...............................................................................
	schaltet Radiosender (Favoriten) um:
        HTTP Befehl: http://192.168.178.29:80/goform/formiPhoneAppDirect.xml?FV 01
	Telnet Befehl: FV 01
	...............................................................................
	Parameter:  $Channel = String = ['0" - '50'] 
	--------------------------------------------------------------------------------
	SetVariable: 
	--------------------------------------------------------------------------------
	return:  
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/	
	Public function SetRadioChannel($Channel){
            $this->SendDebug('Switch Radio to Channel:', $Channel, 0);
            $cmd = 'FV'.'%20'.$Channel;
            $this->send_cmd($cmd);
            SetValueInteger($this->GetIDForIdent("CeolFavChannel"), intval($Channel)-1);
            return $Channel;
	}	        
       

	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: GetCover()
	...............................................................................
	Holt Cover Bilder des abgespielten Streams
        HTTP Befehl: http://192.168.2.99/NetAudio/art.asp-jpg
o                    http://192.168.2.99/img/album%20art_S.png
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable: 
	--------------------------------------------------------------------------------
	return:  $xml->array = output
	--------------------------------------------------------------------------------
	Status: checked 2018-05-31
	//////////////////////////////////////////////////////////////////////////////*/ 	
	Public function GetCover(){
            $host = $this->ReadPropertyString('IPAddress');
            $url = "http://$host:80/NetAudio/art.asp-jpg";
            //$url = "http://$host:80/img/album%20art_S.png";
            $cmd = "";
            $xml = $this->curl_get($url, $cmd);
            $Cover ='<img src='.$url. ' width=320px height=280px scrolling="no">';	
            setvalue(38066 /*[Denon-CEOL\_Cover]*/, $Cover);	
            return $xml;
	}	

        
	//*****************************************************************************
	/* Function: setServer($serverName)
	...............................................................................
	Umschalten auf Client
        ...............................................................................
	Parameters:  
            $serverName - "Friendly Name des Servers"  = "Plex" // "AVM"
	--------------------------------------------------------------------------------
	Returns:
            $key - Nummer des Client Arrays
        --------------------------------------------------------------------------------
	Status: 14.7.2018 checked
	//////////////////////////////////////////////////////////////////////////////*/
	public function setServer(string $serverName){
		//IPSLog("Starte Funktion : ", 'setServer');
		$which_key = "FriendlyName";
		$which_value = $serverName;
		$array = getvalue($this->GetIDForIdent("Ceol_ServerArray"));
		$Server_Array = unserialize($array);
		$key = $this->search_key($which_key, $which_value, $Server_Array);

		$Server_Array[$key]['ServerActiveIcon'] = "image/button_ok_blue_80x80.png";
		$ServerIP                   = $Server_Array[$key]['ServerIP'];
		$ServerPort                 = $Server_Array[$key]['ServerPort'];
		$friendlyName               = $Server_Array[$key]['FriendlyName'];
		$ServerServiceType          = $Server_Array[$key]['ServerServiceType'];
		$ServerContentDirectory     = $Server_Array[$key]['ServerContentDirectory'];
		$ServerActiveIcon           = $Server_Array[$key]['ServerActiveIcon'];
		$ServerIconURL              = $Server_Array[$key]['IconURL'];
		SetValue($this->GetIDForIdent("Ceol_ServerIP"), $ServerIP);
		SetValue($this->GetIDForIdent("Ceol_ServerPort"), $ServerPort);
		SetValue($this->GetIDForIdent("Ceol_ServerName"), $friendlyName);
		setvalue($this->GetIDForIdent("Ceol_ServerKey"), $key);
		//SetValue(UPNP_Server_ServiceType, $ServerServiceType);
		SetValue($this->GetIDForIdent("Ceol_ServerContentDirectory"), $ServerContentDirectory);
		SetValue($this->GetIDForIdent("Ceol_ServerIcon"), $ServerIconURL);
		return $key;
	}   
 
        
	//*****************************************************************************
	/* Function: loadPlaylist($AlbumNo)
	...............................................................................
	Playlist aus Datei laden (XML) und in Variable Playlist_XML schreiben
	...............................................................................
	Parameters:  
            $AlbumNo - Album Nummer = '0001'.
	--------------------------------------------------------------------------------
	Returns:  
            $xml - Playlist as XML 
	--------------------------------------------------------------------------------
	Status:  14.7.2018 checked
	//////////////////////////////////////////////////////////////////////////////*/
	public function loadPlaylist(string $AlbumNo){	
            $this->SendDebug('Send','lade Play Liste' , 0);
            $Server = getvalue($this->GetIDForIdent("Ceol_ServerName"));
            $PlaylistName = $Server.$AlbumNo;
            setvalue($this->GetIDForIdent("Ceol_PlaylistName"), $PlaylistName);
            $PlaylistFile = $PlaylistName.'.xml';

            $Playlist = file_get_contents($this->Kernel()."media/Multimedia/Playlist/Musik/".$PlaylistFile);
            // Playlist abspeichern
            setvalue($this->GetIDForIdent("Ceol_Playlist_XML"), $Playlist);
            // neue Playlist wurde geladen - TrackNo auf 0 zurücksetzen
            setvalue($this->GetIDForIdent("Ceol_Track"), 1);

            $vars 				= explode(".", $PlaylistFile);
            $PlaylistName 			= $vars[0];
            $PlaylistExtension		= $vars[1];

            $xml = new SimpleXMLElement($Playlist);

            return $xml;
	}
  
        
	//*****************************************************************************
	/* Function: play()
	...............................................................................
	vorgewählte Playlist abspielen
        ...............................................................................
	Parameters: 
            none.
	--------------------------------------------------------------------------------
	Returns:  
            none
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function play(){	
                
		$Playlist   = getvalue($this->GetIDForIdent("Ceol_Playlist_XML"));
		
		$xml = new SimpleXMLElement($Playlist);
		$tracks = $xml->count();
		setvalue($this->GetIDForIdent("Ceol_NoTracks"),$tracks);
 		$TrackNo = getvalue($this->GetIDForIdent("Ceol_Track"));
                if ($TrackNo < 1){
                    $TrackNo = 1;
                    setvalue($this->GetIDForIdent("Ceol_Track"), 1);
                }
		$track = ("Track".strval($TrackNo-1));
			
		$res = $xml->$track->resource; // gibt resource des Titels aus

		$metadata = $xml->$track->metadata; // gibt resource des Titels aus
		//UPNP_GetPositionInfo_Playing abschalten zum Ausführen des Transitioning
		//IPS_SetScriptTimer($this->GetIDForIdent("upnp_PlayInfo"), 0);
		$this->SetTimerInterval('Ceol_PlayInfo', 0);
                $this->SendDebug("PLAY ", 'Timer Position Deaktivieren', 0);
                 //Transport zuruecksetzen  wenn Media Stream verlinkt
                            $TransStatus = $this->GetTransportInfo_AV();                       
                            setvalue($this->GetIDForIdent("Ceol_Transport_Status"), $TransStatus); 
                if ($TransStatus != 'NO_MEDIA_PRESENT') {          
                    $this->Stop_AV();
                }    
		//Transport starten 
                $this->SetAVTransportURI_AV((string) $res, (string) $metadata);
                $this->SendDebug("PLAY ", 'SetAVTransportURI', 0);              
                            $TransStatus = $this->GetTransportInfo_AV();                       
                            setvalue($this->GetIDForIdent("Ceol_Transport_Status"), $TransStatus); 
		//Stream ausführen	
		$this->Play_AV();
                $this->SendDebug("PLAY ", 'Play_AV', 0);
                            $TransStatus = $this->GetTransportInfo_AV();                       
                            setvalue($this->GetIDForIdent("Ceol_Transport_Status"), $TransStatus);               

                IPS_Sleep(2000);
		// Postion Timer starten                 
                $this->SetTimerInterval('Ceol_PlayInfo', 1000);
                $this->SendDebug("PLAY ", 'Timer Position aktivieren', 0);  
	}

	//*****************************************************************************
	/* Function: PlayNextTrack()
	...............................................................................
	nächsten Track aus der vorgewählten Playlist abspielen
	...............................................................................
	Parameters:  
            none.
	--------------------------------------------------------------------------------
	Returns:  
            none
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function PlayNextTrack(){	
            $track 	= getvalue($this->GetIDForIdent("Ceol_Track"));
            $this->SendDebug("PlayNextTrack ", $track, 0);
            setvalue($this->GetIDForIdent("Ceol_Track"),$track+1);
            $trackNo 	= ("Track".strval($track));
            $Playlist 	= getvalue($this->GetIDForIdent("Ceol_Playlist_XML"));
            $xml = new SimpleXMLElement($Playlist);

            $res = $xml->$trackNo->resource; // gibt resource des Titels aus
            $metadata = $xml->$trackNo->metadata; // gibt resource des Titels aus

            $this->SetAVTransportURI_AV((string) $res, (string) $metadata);
            $this->Play_AV();
	}
  

	//*****************************************************************************
	/* Function: Stop()
        --------------------------------------------------------------------------------
        * Stream stoppen
        * Track Zähler zurücksetzen
        * Positions - Timer ausschalten
        ...............................................................................
	Parameters: 
            none.
	--------------------------------------------------------------------------------
	Returns:  
              none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function stop(){	

            
            $this->SendDebug('STOP', 'Stream stoppen', 0);
            /*Timer abschalten--------------------------------------------------------*/
            $this->SetTimerInterval('Ceol_PlayInfo', 0);
            $this->SendDebug("STOP ", 'Timer Position Deaktivieren', 0);
            /*Stram stoppen--------------------------------------------------------*/
            $this->Stop_AV();
            //Track Zähler auf Anfang zurücksetzen
            setvalue($this->GetIDForIdent("Ceol_Track"), 0);
            //Transport Status zurücksetzen auf Anfang zurücksetzen
            setvalue($this->GetIDForIdent("Ceol_Transport_Status"), '');
         
	}
	
	//*****************************************************************************
	/* Function: Pause()
        --------------------------------------------------------------------------------
         Pause
        ...............................................................................
	Parameters: 
            none.
	--------------------------------------------------------------------------------
	Returns:
            none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function pause()
	{	
		$this->Pause_AV();
	}


	
	//*****************************************************************************
	/* Function: Next()
        --------------------------------------------------------------------------------
        ...............................................................................
	Parameters: 
            none.
	--------------------------------------------------------------------------------
	Returns:
            none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function next()
	{	
		$Playlist = getvalue($this->GetIDForIdent("Ceol_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		//$count = count($xml->children()); 
		//IPSLog("Anzahl XML Elemente : ", $count);
		
		$SelectedFile = GetValue($this->GetIDForIdent("Ceol_Track")); 
		
		$track = ("Track".($SelectedFile+1));

		//Aktueller Track = Selected File-----------------------------------------
		SetValue($this->GetIDForIdent("Ceol_Track"), ($SelectedFile+1));

		$this->play();	

	}	
	
	
	
	//*****************************************************************************
	/* Function: Previous()
        -------------------------------------------------------------------------------
        nächste 
        ...............................................................................
	Parameters:
            none.
        --------------------------------------------------------------------------------
	Returns:
            none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function previous()
	{	
	
		$Playlist = getvalue($this->GetIDForIdent("Ceol_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		$SelectedFile = GetValue($this->GetIDForIdent("Ceol_Track")); 
		$track = ("Track".($SelectedFile-1));

		//Aktueller Track = Selected File-----------------------------------------
		SetValue($this->GetIDForIdent("Ceol_Track"), ($SelectedFile-1));
		
		$this->play();

	}	
        
 	//*****************************************************************************
	/* Function: seekForward()
        -------------------------------------------------------------------------------
        spult Lied um 20 Sekunden vor
        ...............................................................................
	Parameters:
            none.
        --------------------------------------------------------------------------------
	Returns:
            none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function seekForward(){	
 
            $postime = getvalue($this->GetIDForIdent("Ceol_RelTime"));
            $seconds = 20;
            $time_now = "00:00:00.000";
            $this->SendDebug('seekForward', $postime, 0);
            $position = date("H:i:s.000", (strtotime(date($postime)) + $seconds));

            $this->SendDebug('seekForward', $position, 0);
            $this->Seek_AV('REL_TIME', $position);
	}
	//*****************************************************************************
	/* Function: seekForward()
        -------------------------------------------------------------------------------
        spult Lied um 20 Sekunden vor
        ...............................................................................
	Parameters:
            none.
        --------------------------------------------------------------------------------
	Returns:
            none.
        //////////////////////////////////////////////////////////////////////////////*/
	public function seekBackward(){	
            $postime = getvalue($this->GetIDForIdent("Ceol_RelTime"));
            $seconds = 20;
            $time_now = "00:00:00.000";
            $this->SendDebug('seekForward', $postime, 0);
            $position = date("H:i:s.000", (strtotime(date($postime)) - $seconds));
            $this->SendDebug('seekBackward', $position, 0);
            $this->Seek_AV('REL_TIME', $position);
	}
        
	//*****************************************************************************
	/* Function: seekPos($Seek)
        -------------------------------------------------------------------------------
        spult Lied auf $position %  der Lieddauer
        ...............................................................................
	Parameters:
            $Seek = Angabe 0 ...100 wird in % der Duration umgerechnet
        --------------------------------------------------------------------------------
	Returns:
            none.
	--------------------------------------------------------------------------------
	Status:   checked 5.7.2018  nur für TV und MusikPal CEOL funktiniert nicht
        //////////////////////////////////////////////////////////////////////////////*/
	public function seekPos(integer $Seek){	
            $GetPositionInfo = $this->GetPositionInfo_AV();
            $Duration = $GetPositionInfo['TrackDuration'];
            $duration = explode(":", $Duration);
            $seconds = round(((($duration[0] * 3600) + ($duration[1] * 60) + ($duration[2])) * ($Seek/100)), 0, PHP_ROUND_HALF_UP);
            $position = gmdate('H:i:s', $seconds);
            $this->Seek_AV('REL_TIME', $position );
	}       
        
	//*****************************************************************************
	/* Function: GetPosInfo()
	...............................................................................
	Aufruf durch Timer jede Sekunde
	überprüft 'CurrentTransportState' und PositionInfo
	...............................................................................
	Parameters:
            none.
	--------------------------------------------------------------------------------
	Returns:  
            none.
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function GetPosInfo(){ 
		//IPAdresse und Port des gewählten Device---------------------------------------
                $ClientPort = '8080';
		$host = $this->ReadPropertyString('IPAddress');
		$fsock = fsockopen($host, $ClientPort, $errno, $errstr, $timeout = '1');
		if ( !$fsock ){
                    //nicht erreichbar --> Timer abschalten--------------------------------
                    $this->SendDebug('Send', $host.'ist nicht erreichbar!', 0);
		}
		else{
			/*///////////////////////////////////////////////////////////////////////////
			Auswertung nach CurrentTransportState "PLAYING" oder "STOPPED"
			bei "PLAYING" -> GetPositionInfo -> Progress wird angezeigt
			bei "STOPPED" -> nächster Titel wird aufgerufen
			/*///////////////////////////////////////////////////////////////////////////
			$Playlist = getvalue($this->GetIDForIdent("Ceol_Playlist_XML"));
			$xml = new SimpleXMLElement($Playlist);
                        $TNo = GetValue($this->GetIDForIdent("Ceol_Track"));
                        $this->SendDebug("GetPosInfo ", 'Track Nummer '.$TNo, 0);
			$SelectedFile = $TNo -1; 
                        if ($TNo === 0){
                            $this->SetTimerInterval('Ceol_PlayInfo', 0);  // DeAktivert Ereignis
                            $this->SendDebug("GetPosInfo ", 'Timer Position Deaktivieren weil Track = 0', 0);
                        }
                        else {
                            $track = ("Track".($SelectedFile));
                            $DIDL_Lite_Class = $xml->$track->class;
                            $this->SendDebug("GetPosInfo ", 'class des Tracks abfragen: '.$DIDL_Lite_Class , 0);
                            /* Transport Status abfragen */
                            $PlayModeIndex = $this->GetTransportSettings_AV();
                            //$this->IPSLog("Playmode Array", $PlayMode); 
                            $this->SendDebug("GetPosInfo ", 'Playmode: '.$PlayModeIndex , 0);

                            setvalue($this->GetIDForIdent("Ceol_PlayMode"), $PlayModeIndex);
                            /* Transport Status abfragen */
                            $TransStatus = $this->GetTransportInfo_AV();                       
                            setvalue($this->GetIDForIdent("Ceol_Transport_Status"), $TransStatus);
                             $this->SendDebug("GetPosInfo ", 'Transport Status abfragen: '.$TransStatus , 0);
                            //Transport Status auswerten.
                            switch ($TransStatus){
                                case 'TRANSITIONING':
                                    IPS_Sleep(1000);
                                    break;
                                case 'NO_MEDIA_PRESENT':
                                    $this->SetTimerInterval('Ceol_PlayInfo', 0);  // DeAktivert Ereignis
                                    $this->SendDebug("GetPosInfo ", 'Timer Position Deaktivieren weil NO MEDIA', 0);
                                    setvalue($this->GetIDForIdent("Ceol_Progress"),0);
                                    setvalue($this->GetIDForIdent("Ceol_Track"),0);
                                    break;
                                case 'STOPPED':
                                    $lastTrack = getvalue($this->GetIDForIdent("Ceol_Track"));
                                    $maxTrack = getvalue($this->GetIDForIdent("Ceol_NoTracks"));
                                    if ($lastTrack > 0  AND $lastTrack < $maxTrack){
                                            $this->PlayNextTrack();		
                                    }
                                    else {
                                        $this->SetTimerInterval('Ceol_PlayInfo', 0);  // DeAktivert Ereignis
                                        $this->SendDebug("GetPosInfo ", 'Timer Position Deaktivieren weil STOPPED und TRACK = 0', 0);
                                        setvalue($this->GetIDForIdent("Ceol_Progress"),0);
                                        setvalue($this->GetIDForIdent("Ceol_Track"),0);
                                    }
                                    break;
                                case 'PLAYING':
                                    if($DIDL_Lite_Class == "object.item.audioItem.musicTrack"){
                                        $this->SendDebug("GetPosInfo ", 'progress aufrufen', 0);
                                        $fortschritt = $this->progress();
                                    }
                                    else if($DIDL_Lite_Class == "object.item.videoItem"){
                                            //include_once ("35896 /*[Multimedia\Core\UPNP_Progress]*/.ips.php"); //UPNP_Progress
                                    }
                                    else if($DIDL_Lite_Class == "object.item.imageItem.photo"){
                                            //include_once ("57444 /*[Multimedia\Core\UPNP_SlideShow]*/.ips.php"); //UPNP_SlideShow
                                    }
                                    else {$this->Stop_AV();}
                                    break;
                                default:

                            }
                        }    
		}
	}
            
        

	//*****************************************************************************
	/* Function: progress ($ClientIP, $ClientPort, $ControlURL)
	...............................................................................
	Fortschrittsanzeige
	Liest PositionInfo aus aktuellem Stream aus:
		['TrackDuration']
		['RelTime']
		['TrackMetaData'] = DIDL =
									'dc:creator'
									'dc:title'
									'upnp:album'
									'upnp:originalTrackNumber'
									'dc:description'
									'upnp:albumArtURI'
									'upnp:genre'
									'dc:date'
	...............................................................................
	Parameters:  
            $ClientIP - Client IP auf dem wiedergeben wird.
            $ClientPort - IP des Clients.
            $ControlURL - Control URL des Clients.
	--------------------------------------------------------------------------------
	Returns:  
            $Progress - Integer Wert 0 - 100 
        -------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function progress(){	
            $PositionInfo = $this->GetPositionInfo_AV();
             
            $Duration = (string) $PositionInfo['TrackDuration']; //Duration
            setvalue($this->GetIDForIdent("Ceol_TrackDuration"), (string) $Duration);           
            $RelTime = (string) $PositionInfo['RelTime']; //RelTime
            setvalue($this->GetIDForIdent("Ceol_RelTime"), (string) $RelTime);          
            $this->SendDebug("progress ", ' GetRelTIME PositionInfo: '.$RelTime, 0);
            /*
            $TrackMeta = (string) $GetPositionInfo['TrackMetaData'];
            $b = htmlspecialchars_decode($TrackMeta);
            //$this->IPSLog('HTML: ', $b);
            $didlXml = simplexml_load_string($b); 
            $this->SendDebug("progress-DIDL INFO ", $didlXml , 0);
            $creator = (string)$didlXml->item[0]->xpath('dc:creator')[0];
            $title = (string) $didlXml->item[0]->xpath('dc:title')[0];
            $album = (string)$didlXml->item[0]->xpath('upnp:album')[0];
            $TrackNo = (string)$didlXml->item[0]->xpath('upnp:originalTrackNumber')[0];
            $actor = (string)$didlXml->item[0]->xpath('upnp:actor')[0];
            $AlbumArtURI = (string)$didlXml->item[0]->xpath('upnp:albumArtURI')[0];
            $genre = (string)$didlXml->item[0]->xpath('upnp:genre')[0];
            $date = (string)$didlXml->item[0]->xpath('dc:date')[0];
            */
                                
            setvalue($this->GetIDForIdent("Ceol_Artist"),  $PositionInfo["artist"]);
            setvalue($this->GetIDForIdent("Ceol_Title"),  $PositionInfo["title"]);
            setvalue($this->GetIDForIdent("Ceol_Album"),  $PositionInfo["album"]);		
            //setvalue($this->GetIDForIdent("Ceol_TrackNo"),  $PositionInfo["TrackNo"]);
            
            //setvalue($this->GetIDForIdent("Ceol_Actor"),  $PositionInfo["album"]);
            setvalue($this->GetIDForIdent("Ceol_Date"),  $PositionInfo["album"]);
            setvalue($this->GetIDForIdent("Ceol_AlbumArtUri"), $PositionInfo["albumArtURI"]);
            //setvalue($this->GetIDForIdent("Ceol_Genre"),  $PositionInfo["genre"]);
                function get_time_difference($Duration, $RelTime){
                        $duration = explode(":", $Duration);
                        $reltime = explode(":", $RelTime);
                        $time_difference = round((((($reltime[0] * 3600) + ($reltime[1] * 60) + ($reltime[2]))* 100) / (($duration[0] * 3600) + ($duration[1] * 60) + ($duration[2]))), 0, PHP_ROUND_HALF_UP);
                        return ($time_difference);
                }
            if($Duration == "0:00:00"){
                    $Duration = (string) $PositionInfo['AbsTime']; //AbsTime
            }
            $Progress = get_time_difference($Duration, $RelTime);
            SetValueInteger($this->GetIDForIdent("Ceol_Progress"), $Progress);
            return $Progress;
	}

        
        
        
        
        
        
        
        
        
        
        
 
        
	//*****************************************************************************
	/* Function:  search_key($which_key, $which_value, $array)
        ...............................................................................
        den $key des Elternelementes in einem mehrdimensionalen Array finden
        ...............................................................................
        Parameter:  
            * $which_key =    = zu durchsuchedes ArrayFeld = ['FriendlyName']
            * $which_value    = Suchwert = z.Bsp "CEOL"
            * $array          = zu durchsuchendes Array
        --------------------------------------------------------------------------------
        return:  
            * key = gefundener Datensatz index
        --------------------------------------------------------------------------------
        Status  checked  
        //////////////////////////////////////////////////////////////////////////////*/
        Protected function search_key($which_key, $which_value, $array){
            foreach ($array as $key => $value){
                if($value[$which_key] === $which_value){
                    return $key;
                }
                else{
                    //$this->SendDebug('Send', $which_value.' in Key: '.$key.' not found', 0);

                }
            }
        }

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
        
      
        
       
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: PlayFiles(array $files)
	...............................................................................
	 
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable: 
	--------------------------------------------------------------------------------
	return:  
	--------------------------------------------------------------------------------
	Status: not implemented
	//////////////////////////////////////////////////////////////////////////////*/ 
        public function PlayFile($file){
   

           // $positionInfo       = $ceol->GetPositionInfo();

            //$mediaInfo          = $ceol->GetMediaInfo();

            //$transportInfo      = $ceol->GetTransportInfo();


           // foreach ($files as $key => $file) {

                // only files on SMB share or http server can be used
                if (preg_match('/^\/\/[\w,.,\d,-]*\/\S*/',$file) == 1){
                   $uri = "x-file-cifs:".$file;
                  
                }elseif (preg_match('/^https{0,1}:\/\/[\w,.,\d,-,:]*\/\S*/',$file) == 1){
                    $uri = $file;
                }else{
                   throw new Exception("File (".$file.") has to be located on a Samba share (e.g. //ipsymcon.fritz.box/tts/text.mp3) or a HTTP server (e.g. http://ipsymcon.fritz.box/tts/text.mp3)");
                }
                $this->SendDebug("Spiele File: ", $uri, 0);
                $this->SetAVTransportURI_AV($uri, "") ;
                $this->SetPlayMode_AV('NORMAL');	
                $this->Play_AV();
                IPS_Sleep(500);
/*
              $fileTransportInfo = $sonos->GetTransportInfo();

              while ($fileTransportInfo==1 || $fileTransportInfo==5){ 

                IPS_Sleep(200);

                $fileTransportInfo = $ceol->GetTransportInfo();

              }

           // }



            // reset to what was playing before

            $ceol->SetAVTransportURI($mediaInfo["CurrentURI"],$mediaInfo["CurrentURIMetaData"]);

            if($positionInfo["TrackDuration"] != "0:00:00" && $positionInfo["Track"] > 1)

              try {

                $ceol->Seek("TRACK_NR",$positionInfo["Track"]);

              } catch (Exception $e) { }

            if($positionInfo["TrackDuration"] != "0:00:00" && $positionInfo["RelTime"] != "NOT_IMPLEMENTED" )

              try {

                $ceol->Seek("REL_TIME",$positionInfo["RelTime"]);

              } catch (Exception $e) { }




            if ($transportInfo==1){

              $ceol->Play();

            }

  */              
            
        }
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: PlayFiles(array $files)
	...............................................................................
	 
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	SetVariable: 
	--------------------------------------------------------------------------------
	return:  
	--------------------------------------------------------------------------------
	Status: not implemented
	//////////////////////////////////////////////////////////////////////////////*/ 
        public function PlayM3U($Playlist){

            
        }     
        
	/*//////////////////////////////////////////////////////////////////////////////
	Befehl: getImageFromLastFM()
	...............................................................................
	 
	...............................................................................
	Parameter:   
           $artist
           $size
              =  "small" => 0, "medium" => 1, "large" => 2, "extralarge" => 3, "mega" => 4 

        
	--------------------------------------------------------------------------------
	SetVariable: 
	--------------------------------------------------------------------------------
	return:  
	--------------------------------------------------------------------------------
	Status: 03.10.2018
	//////////////////////////////////////////////////////////////////////////////*/ 
        public function getImageFromLastFM($artist, $size){
            $artisDec = urlencode($artist);
            $url    = "http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist={$artisDec}&api_key=91770645e54b138f5187003fcb830865";
            
            if ($url == ""){
                $this->SendDebug("getImageFromLastFM: ", "URL für image not found.", 0);
                $url = "/var/lib/symcon/webfront/user/images/INetRadio1.png";
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

            $data = curl_exec($ch); // execute curl request
            curl_close($ch);

            $xml = simplexml_load_string($data); 
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $imageUrl = $array["artist"]["image"][$size];
            $this->SendDebug("getImageFromLastFM: ", $imageUrl, 0);
            return $imageUrl;
        } 
        
    } // Ende Klasse
?>
