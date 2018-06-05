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

             $this->ConnectParent("{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}");

        }
        
        // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
        // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {
            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->SendDebug('Changes', 'Start', 0);
            
           
            
            $Open = $this->ReadPropertyBoolean('Open');
            $Port = $this->ReadPropertyInteger('Port');
            $this->PingInterval = $this->ReadPropertyInteger('Interval');
            if (!$Open) {
                //-$NewState = IS_INACTIVE;
            } else {
                if (($Port < 1) or ($Port > 65535)) {
                    //-$NewState = IS_EBASE + 2;
                    $Open = false;
                    trigger_error($this->Translate('Port invalid'), E_USER_NOTICE);
                } else {
                    if (($this->PingInterval != 0) and ($this->PingInterval < 5)) {
                        $this->PingInterval = 0;
                        //-$NewState = IS_EBASE + 4;
                        $Open = false;
                        trigger_error($this->Translate('Ping interval to small'), E_USER_NOTICE);
                    }
                }
            }
            //-$ParentID = $this->RegisterParent();
            $ParentID = @IPS_GetInstance($this->InstanceID)['ConnectionID'];    
            
            // Zwangskonfiguration des ServerSocket
            if ($ParentID > 0) {
                if (IPS_GetProperty($ParentID, 'Port') <> $Port) {
                    IPS_SetProperty($ParentID, 'Port', $Port);
                }
                if (IPS_GetProperty($ParentID, 'Open') <> $Open) {
                    IPS_SetProperty($ParentID, 'Open', $Open);
                }
                if (IPS_HasChanges($ParentID)) {
                    @IPS_ApplyChanges($ParentID);
                }
            } else {
                if ($Open) {
                    //-$NewState = IS_INACTIVE;
                    $Open = false;
                }
            }

            if ($Open && !$this->HasActiveParent($ParentID)) {
               //- $NewState = IS_EBASE + 2;
            }

            //-$this->SetStatus($NewState);
            $this->NoNewClients = false;
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
           //unset($data->DataID);
           $this->SendDebug('incoming', $data, 0);
       }
        
        
   
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    } // Ende Klasse
