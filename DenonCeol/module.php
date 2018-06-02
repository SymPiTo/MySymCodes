<?php
//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/DenonCeol_Interface.php");
require_once(__DIR__ . "../lib/XML2Array.php");

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
            
            // Selbsterstellter Code
           
        }
        
        // Create() wird einmalig beim Erstellen einer neuen Instanz ausgeführt
        // Überschreibt die interne IPS_Create($id) Funktion
        public function Create() {
            // Diese Zeile nicht löschen.
            parent::Create();
            
            // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            $this->RegisterPropertyBoolean("active", false);
            $this->RegisterPropertyString("IPAddress", "");
            $this->RegisterPropertyInteger("UpdateInterval", 30);
           
            //Status Variable anlegen
            $this->RegisterVariableInteger("Wert", "Wert vom", "");
            $this->RegisterVariableBoolean("CeolPower", "Power");
            $this->RegisterVariableInteger("CeolVolume", "Volume", "");
            $this->RegisterVariableBoolean("CeolMute", "Mute");
            
            
            
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
                $this->init();
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
        
        
        private function init(){
           SetValueInteger($this->GetIDForIdent("Wert"), 30);
           //$this->SetTimerInterval("Update", $this->ReadPropertyInteger("UpdateInterval"));
        }
        
        public function update() {
            $ip = $this->ReadPropertyString('IPAddress');
            $alive = Sys_Ping($ip, 1000);
            if ($alive){
                $i = getvalue($this->GetIDForIdent("Wert"));   
                $i = $i + 1;
                SetValueInteger($this->GetIDForIdent("Wert"), $i);
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
                /*
                $output = $this->get_audio_status();		
                $sz1 = $output['item']['szLine']['value'][0];
                $sz2 = $output['item']['szLine']['value'][1];
                $sz3 = $output['item']['szLine']['value'][2];
                $sz4 = $output['item']['szLine']['value'][3];
                $sz5 = $output['item']['szLine']['value'][4];
                $sz6 = $output['item']['szLine']['value'][5];
                $sz7 = $output['item']['szLine']['value'][6];
                $sz8 = $output['item']['szLine']['value'][6];
                setvalue(23849   , $sz1);
                setvalue(24839   , $sz2);
                setvalue(28089   , $sz3);
                setvalue(36950   , $sz4);
                setvalue(47520   , $sz5);
                setvalue(54201   , $sz6);
                setvalue(57691   , $sz7);
                setvalue(27778   , $sz8);

                $Input = $output['item']['NetFuncSelect']['value'];
                $value=0;
                switch ($Input)
                {
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
                setvalue(40088  , $value);  
             */      
            }
            else {
                //Keine Netzwerk-Verbindung zun Client
            }
                
        } // function ende
        
	/**************************************************************************************************** 	
	Funktion 	:	Status der MainZone auslesen
					
	Befehl		:	http://192.168.178.29:80/goform/formMainZone_MainZoneXmlStatus.xml
	-------------------------------------------------------------------------------------------
	Include's : 	include ("Denon_Vars.ips.php");
	Parameter:		$host = String = Adresse von DENON CEOL
	Rückgabewert: 	$output [Array]
					- $output['item']['MasterVolume']['value']
					- $output['item']['Mute']['value']
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
	---------------------------------------------------------------------------------------------------*/ 	
	// Testbefehl:
	//$R = Get_MainZone_Status(); 
	//print_r ($R);
	/*****************************************************************************************************/ 
	Public function Get_MainZone_Status()
	{
		$url = "http://$host:80/goform/formMainZone_MainZoneXmlStatus.xml";
		$cmd = "";
		$xml = $this->curl_get($url, $cmd);
		$output = XML2Array::createArray($xml);
		$status = ($output['item']['Power']['value']);
		return $output;
	}		
	/**************************************************************************************************** 	
	Funktion 	:	get_audio_status()
					 
	Befehl		:	$url = "http://$host:80/goform/formNetAudio_StatusXml.xml";
	-------------------------------------------------------------------------------------------
	Include's : 	 
	Parameter:		$host = String = Adresse von DENON CEOL
	Rückgabewert: 	$xml->array = output

		$output['item']['MasterVolume']['value']	=>	Mastervolume Status
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
	
	*******************************************************************************************************/ 	
	Public function get_audio_status(){
		$url = "http://$host:80/goform/formNetAudio_StatusXml.xml";
		$cmd = "";
		$xml = $this->curl_get($url, $cmd);
		//print_r($xml);
		$output = XML2Array::createArray($xml);
																																																																																														
		return $output;
	}	 
        

    }
?>
