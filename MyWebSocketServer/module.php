<?php

//zugehoerige Unter-Klassen    
require_once(__DIR__ . "/../libs/XML2Array.php");
//require_once(__DIR__ . "/../libs/BasisTrait.php");
require_once(__DIR__ . "/../libs/WebsocketClass.php");  // diverse Klassen





      
    // --- BASE MESSAGE
    define('IPS_BASE', 10000);                             //Base Message
    define('IPS_KERNELSTARTED', IPS_BASE + 1);             //Post Ready Message
    define('IPS_KERNELSHUTDOWN', IPS_BASE + 2);            //Pre Shutdown Message, Runlevel UNINIT Follows
 
 
    // --- KERNEL
    define('IPS_KERNELMESSAGE', IPS_BASE + 100);           //Kernel Message
    define('KR_CREATE', IPS_KERNELMESSAGE + 1);            //Kernel is beeing created
    define('KR_INIT', IPS_KERNELMESSAGE + 2);              //Kernel Components are beeing initialised, Modules loaded, Settings read
    define('KR_READY', IPS_KERNELMESSAGE + 3);             //Kernel is ready and running
    define('KR_UNINIT', IPS_KERNELMESSAGE + 4);            //Got Shutdown Message, unloading all stuff
    define('KR_SHUTDOWN', IPS_KERNELMESSAGE + 5);          //Uninit Complete, Destroying Kernel Inteface

     // --- DATA HANDLER
    define('IPS_FLOWMESSAGE', IPS_BASE + 1100);             //Data Handler Message
    define('FM_CONNECT', IPS_FLOWMESSAGE + 1);             //On Instance Connect
    define('FM_DISCONNECT', IPS_FLOWMESSAGE + 2);          //On Instance Disconnect 
        
    // --- INSTANCE MANAGER
    define('IPS_INSTANCEMESSAGE', IPS_BASE + 500);         //Instance Manager Message
    define('IM_CREATE', IPS_INSTANCEMESSAGE + 1);          //Instance created
    define('IM_DELETE', IPS_INSTANCEMESSAGE + 2);          //Instance deleted
    define('IM_CONNECT', IPS_INSTANCEMESSAGE + 3);         //Instance connectged
    define('IM_DISCONNECT', IPS_INSTANCEMESSAGE + 4);      //Instance disconncted
    define('IM_CHANGESTATUS', IPS_INSTANCEMESSAGE + 5);    //Status was Changed
    define('IM_CHANGESETTINGS', IPS_INSTANCEMESSAGE + 6);  //Settings were Changed
    define('IM_CHANGESEARCH', IPS_INSTANCEMESSAGE + 7);    //Searching was started/stopped
    define('IM_SEARCHUPDATE', IPS_INSTANCEMESSAGE + 8);    //Searching found new results
    define('IM_SEARCHPROGRESS', IPS_INSTANCEMESSAGE + 9);  //Searching progress in %
    define('IM_SEARCHCOMPLETE', IPS_INSTANCEMESSAGE + 10); //Searching is complete        
 
    // --- STATUS CODES
    define('IS_SBASE', 100);
    define('IS_CREATING', IS_SBASE + 1); //module is being created
    define('IS_ACTIVE', IS_SBASE + 2); //module created and running
    define('IS_DELETING', IS_SBASE + 3); //module us being deleted
    define('IS_INACTIVE', IS_SBASE + 4); //module is not beeing used
// --- ERROR CODES
    define('IS_EBASE', 200);          //default errorcode
    define('IS_NOTCREATED', IS_EBASE + 1); //instance could not be created
    
    
    
    


    // Klassendefinition
    class MyWebSocketServer extends IPSModule {
        
    
        //externe Klasse einbinden - ueberlagern mit TRAIT
        use XML2Array;
       // use Definitionen;
                        
        
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
            $this->RequireParent("{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}"); // Modul ID des Server Sockets
             //$this->ConnectParent("{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}");
            $this->Multi_Clients = new WebSocket_ClientList();
            $this->NoNewClients = true;
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

        

    /**
     * Interne Funktion des SDK.
     *Registriete Meldungen werden abgefangen und rufen die Funktion MessageSink auf
     * Registriern in APPLYCHANGES()
     * @access public
     */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
         $this->SendDebug('MesaageSink', 'Starte das', 0);   
        //wenn keine aktive Verbindung dann werden alle Clients gelöscht
        switch ($Message) {
            case IPS_KERNELMESSAGE:
                if ($Data[0] != KR_READY) {
                    break;
                }
            case IPS_KERNELSTARTED:
                $this->ApplyChanges();
                break;
            case IPS_KERNELSHUTDOWN:
                $this->DisconnectAllClients();
                break;
            case FM_DISCONNECT:
                $this->NoNewClients = true;
                $this->RemoveAllClients();
                $this->RegisterParent();
                break;
            case FM_CONNECT:
                $this->ApplyChanges();
                break;
            case IM_CHANGESTATUS:
                if ($SenderID == $this->ParentID) {
                    if ($Data[0] == IS_ACTIVE) {
                        $this->NoNewClients = false;
                    } else {
                        $this->NoNewClients = true;
                        $this->RemoveAllClients();
                    }
                }
                break;
        }
    }        
        
        
        // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
        // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() {

            // Diese Zeile nicht löschen
            parent::ApplyChanges();
            $this->SendDebug('Changes', 'Start', 0);
           
            // WebsocketServer startet!
            $this->NoNewClients = true;
            
            //Meldung Kernelstatus 
            if ((float) IPS_GetKernelVersion() < 4.2) {
                $this->RegisterMessage(0, IPS_KERNELMESSAGE);
            } else {
                $this->RegisterMessage(0, IPS_KERNELSTARTED);
                $this->RegisterMessage(0, IPS_KERNELSHUTDOWN);
            }
            //Meldung Instance connected
            $this->RegisterMessage($this->InstanceID, FM_CONNECT);
            $this->RegisterMessage($this->InstanceID, FM_DISCONNECT);
            
            //Kernel nicht bereit System started
            if (IPS_GetKernelRunlevel() <> KR_READY) {
                return;
            }
            
            parent::ApplyChanges();

            $NewState = IS_ACTIVE;
            $this->UseTLS = $this->ReadPropertyBoolean('TLS');
            $this->UsePlain = $this->ReadPropertyBoolean('Plain');
            
            
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
             $this->SendDebug('InstanceID = ', $this->InstanceID, 0);
             
             $this->SendDebug('$ParentID = ', $ParentID, 0);
             
             
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
        * - IPS FUNKTION - 
        * Funtion wird automatisch aufgerufen wenn Verbindung zum Parent und dieser Daten sendet
        * Empfaengt Daten vom Parent .
        *
        * @access public
        * @param string $JSONString Das empfangene JSON-kodierte Objekt vom Parent.
        */
       public function ReceiveData($JSONString)
       {
           IPS_LogMessage($_IPS['SELF'], "Empfangene Daten aus dem Puffer  von Funktion Receive");
            //Empfangene Daten aus dem Puffer 
           $data = json_decode($JSONString);
            unset($data->DataID);
            $this->SendDebug('incoming', utf8_decode($data->Buffer), 0);
            $Data = utf8_decode($data->Buffer);
           
            //$Clients= Klasse class WebSocket_ClientList 
            //array aller Clients ist am Anfang ein leeres Array
            
            $this->Multi_Clients = new WebSocket_ClientList();
            $this->NoNewClients = false;
            $this->UsePlain = $this->ReadPropertyBoolean('Plain');
            
            $Clients = $this->Multi_Clients;
            //Funktion aus derKlasse class WebSocket_ClientList  aufrufen
            //public function GetByIpPort(Websocket_Client $Client)
            //new Websocket_Client  erstellt einen neuen Client (array mit den Feldern:
            //        $this->ClientIP = $ClientIP;
            //    $this->ClientPort = $ClientPort;
            //    $this->State = $State;
            //    $this->Timestamp = 0;
             //   $this->UseTLS = $UseTLS;
            // Püfen ob Client schon vorhanden ist
            $Client = $Clients->GetByIpPort(new Websocket_Client($data->ClientIP, $data->ClientPort));
            $this->SendDebug("Check - Client vorhanden", $Client, 0);    
            //Neuer Client? oder Neu mit Client Verbunden = Client sendet Handshake Request
            if (($Client === false) or (preg_match("/^GET ?([^?#]*) HTTP\/1.1\r\n/", $Data, $match)) or ((ord($Data[0]) == 0x16) && (ord($Data[1]) == 0x03) && (ord($Data[5]) == 0x01))) { // neu oder neu verbunden!
            
                if ($this->NoNewClients) { //Server start neu... keine neuen Verbindungen annehmen.
                    return;
                }

                $this->SendDebug(($Client ? "RECONNECT" : "NEW") . ' CLIENT', $Data, 0);            
                if ($this->UsePlain and (preg_match("/^GET ?([^?#]*) HTTP\/1.1\r\n/", $Data, $match))) { //valid header wenn Plain is active
                    //neuer ClientVerbindung - Client Datensatz
                    $Client = new Websocket_Client($data->ClientIP, $data->ClientPort);
                    //zu Clients Sammulung hinzufügen
                    $Clients->Update($Client);
                    $this->Multi_Clients = $Clients;
                    
                    $this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} = "";
                }
                if ($Client === false) { // Paket verwerfen, da Daten nicht zum Protocol passen.
                    return;
                }
            }
            // Client jetzt bekannt.
            //Client 
            $this->SendDebug('Client Status = ', WebSocketState::HandshakeReceived, 0);
            if ($Client->State == WebSocketState::HandshakeReceived) {
                $NewData = $this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} . $Data;
                $CheckData = $this->ReceiveHandshake($NewData);
                if ($CheckData === true) { // Daten komplett und heil.
                    $Client->State = WebSocketState::Connected; // jetzt verbunden
                    $Client->Timestamp = time() + $this->ReadPropertyInteger("Interval");
                    $Clients->Update($Client);
                    $this->Multi_Clients = $Clients;
                    $this->SendHandshake(101, $NewData, $Client); //Handshake senden
                    $this->SendDebug('SUCCESSFULLY CONNECT', $Client, 0);
                    $this->SetNextTimer();
                } elseif ($CheckData === false) { // Daten nicht komplett, buffern.
                    $this->Multi_Clients = $Clients;
                    $this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} = $CheckData;
                } else { // Daten komplett, aber defekt.
                    $this->SendHandshake($CheckData, $NewData, $Client);
                    //$Clients->Remove($Client);
                    $this->Multi_Clients = $Clients;
                    //$this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} = "";
                }
            } elseif ($Client->State == WebSocketState::Connected) { // bekannt und verbunden
                $Client->Timestamp = time() + $this->ReadPropertyInteger("Interval");
                $Clients->Update($Client);
                $this->Multi_Clients = $Clients;
                $NewData = $this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} . $Data;
                $this->SendDebug('ReceivePacket ' . $Client->ClientIP . $Client->ClientPort, $NewData, 1);
                while (true) {
                    if (strlen($NewData) < 2) {
                        break;
                    }
                    $Frame = new WebSocketFrame($NewData);
                    if ($NewData == $Frame->Tail) {
                        break;
                    }
                    $NewData = $Frame->Tail;
                    $Frame->Tail = null;
                    $this->DecodeFrame($Frame, $Client);
                    $this->SetNextTimer();
                }
                $this->{'Buffer' . $Client->ClientIP . $Client->ClientPort} = $NewData;
            } elseif ($Client->State == WebSocketState::CloseSend) {
                $this->SendDebug('Receive', 'client answer server stream close !', 0);
                $this->{'WaitForClose' . $Client->ClientIP . $Client->ClientPort} = true;
            }
        
       }
       
    /**
      * Leert die ClientListe und alle entsprechenden Buffer der Clients.
      *
      * @access private
      */
     protected function RemoveAllClients()
     {
         $Clients = $this->Multi_Clients;
         foreach ($Clients->GetClients() as $Client) {
             $this->RemoveClient($Client);
         }
         $this->Multi_Clients = new WebSocket_ClientList();
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
