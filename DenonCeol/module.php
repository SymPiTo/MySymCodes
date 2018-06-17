<?php
//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/DenonCeol_Interface.php");
require_once(__DIR__ . "/../libs/XML2Array.php");

    // Klassendefinition
    class DenonCeol extends IPSModule {
        //externe Klasse einbinden - ueberlagern mit TRAIT
        use CEOLupnp;
        use XML2Array;
        
                        
        
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
            
            // Timer erstellen
            $this->RegisterTimer("Update", $this->ReadPropertyInteger("UpdateInterval"), 'CEOL_update($_IPS[\'TARGET\']);');
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
	Funktion function update()
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
                $sz3 = $output['item']['szLine']['value'][2];
                $sz4 = $output['item']['szLine']['value'][3];
                $sz5 = $output['item']['szLine']['value'][4];
                $sz6 = $output['item']['szLine']['value'][5];
                $sz7 = $output['item']['szLine']['value'][6];
                $sz8 = $output['item']['szLine']['value'][6];
                SetValueString($this->GetIDForIdent("CeolSZ1"), $sz1);
                SetValueString($this->GetIDForIdent("CeolSZ2"), $sz2);
                SetValueString($this->GetIDForIdent("CeolSZ3"), $sz3);
                SetValueString($this->GetIDForIdent("CeolSZ4"), $sz4);
                SetValueString($this->GetIDForIdent("CeolSZ5"), $sz5);
                SetValueString($this->GetIDForIdent("CeolSZ6"), $sz6);
                SetValueString($this->GetIDForIdent("CeolSZ7"), $sz7);
                SetValueString($this->GetIDForIdent("CeolSZ8"), $sz8);

                $Source = $output['item']['NetFuncSelect']['value'];
                $value=0;
                switch ($Source){
                        case "IRadio":
                                $value = 0;
                        break;	
                        case "MediaServer":
                                $value = 1;
                        break;	
                        case "USB":
                                $value = 2;
                        break;	
                        case "IPOD":
                                $value = 3;
                        break;	
                        case "AUX_A":
                                $value = 4;
                        break;	
                        case "AUX_D":
                                $value = 5;
                        break;		
                }
                SetValueInteger($this->GetIDForIdent("CeolSource"), $value);  
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
	Parameter:  $value = left" // "right" // "up" // "down"
	--------------------------------------------------------------------------------

	return:  
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
 	Public function Navigate($Direction){
		$host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formiPhoneAppNetAudioCommand.xml";
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
	return: $status = 'on' / 'Standby'   
	--------------------------------------------------------------------------------
	Status: checked 2018-06-03
	//////////////////////////////////////////////////////////////////////////////*/        
	Public function SetPower($status){
		$host = $this->ReadPropertyString('IPAddress');
		$url = "http://$host:80/goform/formiPhoneAppPower.xml";
		if ($status == "On"){
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
		SetValueBoolean($this->GetIDForIdent("CeolPower"), $_power);
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
		$this->SetMute('0');
		SetValueBoolean($this->GetIDForIdent("CeolMute"), false);
            }
            else{
		$this->SetMute('1');
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
	return  
	--------------------------------------------------------------------------------
	Status: checked 2018.06.18
	//////////////////////////////////////////////////////////////////////////////*/
	Public function setTimer($mode, $startTime, $endTime, $funct = 'FA', $n = '01', $volT = '03', $state){    
            $mode = strtoupper ($mode);
            $sT= explode(':', $startTime);
            if ((int)$sT[0] > 12){
                $periodS = 'A';
            }
            else{
                $periodS="P";
            }
            $eT= explode(':', $endTime);
            if ((int)$eT[0] > 12){
                $periodE = 'A';
            }
            else{
                $periodE="P";
            }
            if ($state == 'on'){
                $ts = '1';
            }
            else {
                $ts = '0';
            }
		$cmd = 'TS'.$mode.' '.$periodS.$sT[0].$sT[1].'-'.$periodE.$eT[0].$eT[1].' '.$funct.$n.' '.$volT.' '.$ts;
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
                $this->SetAVTransportURI($uri, "") ;
                $this->SetPlayMode('NORMAL');	
                $this->Play();
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
        
        
    } // Ende Klasse
?>
