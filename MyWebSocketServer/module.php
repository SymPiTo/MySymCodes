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
             $this->SendDebug('Create', 'Start', 0);
            //Falls Server Socket nicht vorhanden wird ein Neuer erstellt
            $this->RequireParent("{C32C9869-1743-45DC-A22C-F74738F6D61F}"); // Modul ID des Server Sockets
            //-$this->Multi_Clients = new WebSocket_ClientList();
            //-$this->NoNewClients = true;
            $this->RegisterPropertyBoolean("Open", false);
            $this->RegisterPropertyInteger("Port", 8080);
            $this->RegisterPropertyInteger("Interval", 0);
            $this->RegisterPropertyString("URI", "/");
            $this->RegisterPropertyBoolean("BasisAuth", false);
            $this->RegisterPropertyString("Username", "");
            $this->RegisterPropertyString("Password", "");
            $this->RegisterPropertyBoolean("TLS", false);
            $this->RegisterPropertyBoolean("Plain", true);
            $this->RegisterPropertyString("CertFile", "");
            $this->RegisterPropertyString("KeyFile", "");
            $this->RegisterPropertyString("KeyPassword", "");
            $this->RegisterTimer('KeepAlivePing', 0, 'WSS_KeepAlive($_IPS[\'TARGET\']);');
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
        
       
        
        private function CeolInit(){
            
             
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
        
        
        
    /**
     * Pr�ft den Parent auf vorhandensein und Status.
     *
     * @access protected
     * @return bool True wenn Parent vorhanden und in Status 102, sonst false.
     */
    protected function HasActiveParent()
    {
        $instance = @IPS_GetInstance($this->InstanceID);
        if ($instance['ConnectionID'] > 0) {
            $parent = IPS_GetInstance($instance['ConnectionID']);
            if ($parent['InstanceStatus'] == 102) {
                return true;
            }
        }
        return false;
    }

        
        
        public function KeepAlive(){
            $this->SendDebug('KeepAlive', 'start', 0);
            $this->SetTimerInterval('KeepAlivePing', 0);
            $Client = true;
/*
            while ($Client) {
                $Clients = $this->Multi_Clients;
                $Client = $Clients->GetNextTimeout(1);
                if ($Client === false) {
                    break;
                }
                if (@$this->SendPing($Client->ClientIP, $Client->ClientPort, "") === false) {
                    $this->SendDebug('TIMEOUT ' . $Client->ClientIP . ':' . $Client->ClientPort, "Ping timeout", 0);
                    $Clients->Remove($Client);
                    $this->Multi_Clients = $Clients;
                }
            }
 */
            $this->SendDebug('KeepAlive', 'end', 0);

            
         }


        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    } // Ende Klasse
?>
