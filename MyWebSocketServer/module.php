<?php
//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/../libs/XML2Array.php");

    // Klassendefinition
    class MyWebSocketServer extends IPSModule {
        //externe Klasse einbinden - ueberlagern mit TRAIT
       
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
            $this->RegisterPropertyInteger("UpdateInterval", 30);
           
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
  
        }
        

        
    } // Ende Klasse
?>
