 <?php

    class MyWebSocketServer extends IPSModule {
        //externe Klasse einbinden - ueberlagern mit TRAIT
       

        
                        
        
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
             $this->SendDebug('Create', 'Start', 0);
            //Falls Server Socket nicht vorhanden wird ein Neuer erstellt

             $this->ConnectParent("{6179ED6A-FC31-413C-BB8E-1204150CF376}");

        }
        
        // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
        // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->SendDebug('Changes', 'Start', 0);
            
           
            

            //-$ParentID = $this->RegisterParent();
            $ParentID = @IPS_GetInstance($this->InstanceID)['ConnectionID'];    
            

        }
 
        /**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
        *
        * CEOL_XYFunktion($id);
        *
        */
        
       
  
        

        ################## DATAPOINTS PARENT

       /**
        * Empf�ngt Daten vom Parent.
        *
        * @access public
        * @param string $JSONString Das empfangene JSON-kodierte Objekt vom Parent.
        */
       public function ReceiveData($JSONString)
       {
           $data = json_decode($JSONString);
   
           $this->SendDebug('incoming', $data, 0);
       }
        
        
   
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    } // Ende Klasse
