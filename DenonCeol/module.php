<?php
//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/DenonCeol_Interface.php");

    // Klassendefinition
    class DenonCeol extends IPSModule {
        //externe Klasse einbinden - ueberlagern mit TRAIT
        use CEOLupnp;
        
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
                }
        }

    }
?>
