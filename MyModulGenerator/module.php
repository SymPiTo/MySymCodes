<?php

class MyModuleGenerator extends IPSModule
{

    public function Create()
    {
//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
        $this->RegisterPropertyString("author", "");
        $this->RegisterPropertyString("modulename", "");
        $this->RegisterPropertyString("url", "");
        $this->RegisterPropertyString("version", "0.1");
        $this->RegisterPropertyInteger("build", 0);
        $this->RegisterPropertyInteger("CategoryID", 0);
        $this->RegisterPropertyInteger("generateguid", 0);
        $this->RegisterPropertyString("library_guid", "");
        $this->RegisterPropertyString("io_guid", "");
        $this->RegisterPropertyString("rx_guid", "");
        $this->RegisterPropertyString("tx_guid", "");
        $this->RegisterPropertyString("splitter_guid", "");
        $this->RegisterPropertyString("splitterinterface_guid", "");
        $this->RegisterPropertyString("device_guid", "");
        $this->RegisterPropertyString("deviceinterface_guid", "");
        $this->RegisterPropertyString("vendor", "");
        $this->RegisterPropertyString("prefix", "");
        $this->RegisterPropertyString("aliases", "");
        $this->RegisterPropertyInteger("ownio", 0);
        $this->RegisterPropertyInteger("typeio", 0);
        $this->RegisterPropertyInteger("dataflowtype", 0);
        $this->RegisterPropertyString("virtual_io_rx_guid", "{018EF6B5-AB94-40C6-AA53-46943E824ACF}"); // Kann für die Kommunikation von ClientSocket, MulticastSocket, SerialPort, UDPSocket und ServerSocket (nur Buffer) genutzt werden
        $this->RegisterPropertyString("virtual_io_tx_guid", "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}"); // Kann für die Kommunikation von ClientSocket, MulticastSocket, SerialPort, UDPSocket und ServerSocket (nur Buffer) genutzt werden
        $this->RegisterPropertyString("hid_rx_guid", "{FD7FF32C-331E-4F6B-8BA8-F73982EF5AA7}"); // Kann für HID (Human Interface Device) Instanzen genutzt werden
        $this->RegisterPropertyString("hid_tx_guid", "{4A550680-80C5-4465-971E-BBF83205A02B}"); // Kann für HID (Human Interface Device) Instanzen genutzt werden
        $this->RegisterPropertyString("server_rx_guid", "{7A1272A4-CBDB-46EF-BFC6-DCF4A53D2FC7}"); // Kann für ServerSocket genutzt werden. Liefert Buffer, ClientIP und ClientPort
        $this->RegisterPropertyString("server_tx_guid", "{C8792760-65CF-4C53-B5C7-A30FCC84FEFE}"); // Kann für ServerSocket genutzt werden. Liefert Buffer, ClientIP und ClientPort
        $this->RegisterPropertyString("www_reader_rx_guid", "{4CB91589-CE01-4700-906F-26320EFCF6C4}"); // Kann für WWW Reader Instanzen genutzt werden
        $this->RegisterPropertyString("www_reader_tx_guid", "{D4C1D08F-CD3B-494B-BE18-B36EF73B8F43}"); // Kann für WWW Reader Instanzen genutzt werden
        $this->RegisterPropertyString("io_clientsocket_guid", "{3CFF0FD9-E306-41DB-9B5A-9D06D38576C3}"); // Client Socket
        $this->RegisterPropertyString("io_multicast_guid", "{BAB408E0-0A0F-48C3-B14E-9FB2FA81F66A}"); // MulticastSocket
        $this->RegisterPropertyString("io_serialport_guid", "{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}"); // SerialPort
        $this->RegisterPropertyString("io_serversocket_guid", "{8062CF2B-600E-41D6-AD4B-1BA66C32D6ED}"); // Server Socket
        $this->RegisterPropertyString("io_udpsocket_guid", "{82347F20-F541-41E1-AC5B-A636FD3AE2D8}"); // UDPSocket
        $this->RegisterPropertyString("io_hid_guid", "{E6D7692A-7F4C-441D-827B-64062CFE1C02}"); // HID
        $this->RegisterPropertyString("io_wwwreader_guid", "{4CB91589-CE01-4700-906F-26320EFCF6C4}"); // WWW Reader
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();

        $this->RegisterVariableString ( "GUID", "GUID", "~HTMLBox", 0 );
		$this->ValidateConfiguration();
	
    }

		/**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wiefolgt zur Verfügung gestellt:
        *
        *
        */
		
	private function ValidateConfiguration()
	{
        $author = $this->ReadPropertyString("author");
        $modulename = $this->ReadPropertyString("modulename");
        $url = $this->ReadPropertyString("url");
        $version = $this->ReadPropertyString("version");
        // $build = $this->ReadPropertyInteger("build");
        $ModuleCategoryID = $this->ReadPropertyInteger("CategoryID");
        $generateguid = $this->ReadPropertyInteger("generateguid");
        $library_guid = $this->ReadPropertyString("library_guid");
        $io_guid = $this->ReadPropertyString("io_guid");
        $rx_guid = $this->ReadPropertyString("rx_guid");
        $tx_guid = $this->ReadPropertyString("tx_guid");
        $splitter_guid = $this->ReadPropertyString("splitter_guid");
        $splitterinterface_guid = $this->ReadPropertyString("splitterinterface_guid"); // Interface GUI
        $device_guid = $this->ReadPropertyString("device_guid");
        $deviceinterface_guid = $this->ReadPropertyString("deviceinterface_guid"); // Interface GUI
        $vendor = $this->ReadPropertyString("vendor");
        $prefix = $this->ReadPropertyString("prefix");
        $aliases = $this->ReadPropertyString("aliases");
        $ownio = $this->ReadPropertyInteger("ownio");
        $dataflowtype = $this->ReadPropertyInteger("dataflowtype");

        if ($author == "" || $modulename == "" || $url == "" || $version == "" || $vendor == "" || $prefix == "" || $aliases == "")
        {
            $this->SetStatus(202);
        }
        if($ModuleCategoryID == 0)
        {
            $this->SetStatus(206);
        }
        if($generateguid == 1) // Existing GUID
        {
            if(($dataflowtype == 1) && ($splitter_guid == "" || $splitterinterface_guid == ""))
            {
                $this->SetStatus(202);
            }

            if($library_guid == "" || $device_guid == "" || $deviceinterface_guid == "")
            {
                $this->SetStatus(202);
            }

            if($ownio == 1)
            {
                if($io_guid == "" || $rx_guid == "" || $tx_guid == "")
                {
                    $this->SetStatus(205);
                }
            }
        }

				// Status Aktiv
				$this->SetStatus(102);	

	}

	public function GenerateGUID()
    {
        $ownio = $this->ReadPropertyInteger("ownio");
        $library_guid = $this->getGUID(); // Verweis im Überverzeichnis
        $dataflowtype = $this->ReadPropertyInteger("dataflowtype");
        if($ownio == 1)
        {
            $io_guid = $this->getGUID(); //
            $rx_guid = $this->getGUID(); //
            $tx_guid = $this->getGUID(); //
        }
        else
        {
            $typeio = $this->ReadPropertyInteger("typeio");
            $ioguids = $this->GetIOGUID($typeio);
            $io_guid = $ioguids["io_guid"];
            $rx_guid = $ioguids["rx_guid"];
            $tx_guid = $ioguids["tx_guid"];

        }
        $splitter_guid = $this->getGUID(); //
        $splitterinterface_guid = $this->getGUID(); // Interface GUI
        $device_guid = $this->getGUID(); //
        $deviceinterface_guid = $this->getGUID(); // Interface GUI
        $guids = array ("library_guid" => $library_guid, "io_guid" => $io_guid, "rx_guid" => $rx_guid, "tx_guid" => $tx_guid, "splitter_guid" => $splitter_guid, "splitterinterface_guid" => $splitterinterface_guid, "device_guid" => $device_guid, "deviceinterface_guid" => $deviceinterface_guid);
        $wfguidid = $this->GetIDForIdent("GUID");
        $content = '<table>
<tr><td>Library GUID</td><td>'.$library_guid.'</td></tr>
<tr><td>IO GUID</td><td>'.$io_guid.'</td></tr>
<tr><td>RX GUID</td><td>'.$rx_guid.'</td></tr>
<tr><td>TX GUID</td><td>'.$tx_guid.'</td></tr>';
        if($dataflowtype == 0)
        {
            $content .= '<tr><td>Splitter GUID</td><td>'.$splitter_guid.'</td></tr>
<tr><td>Splitter Interface GUID</td><td>'.$splitterinterface_guid.'</td></tr>';
        }
        $content .= '<tr><td>Device GUID</td><td>'.$device_guid.'</td></tr>
<tr><td>Device Interface GUID</td><td>'.$deviceinterface_guid.'</td></tr>
</table>';
        SetValue($wfguidid, $content);

        return $guids;
    }

	protected function getGUID()
    {
        if (function_exists('com_create_guid'))
            {
                return com_create_guid();
            }
        else
            {
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = chr(123)// "{"
                .substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12)
                .chr(125);// "}"
            return $uuid;
            }
    }

    public function CreateScripts()
    {
        $generateguid = $this->ReadPropertyInteger("generateguid");
        $aliases = $this->ReadPropertyString("aliases");
        $ownio = $this->ReadPropertyInteger("ownio");
        $dataflowtype = $this->ReadPropertyInteger("dataflowtype");
        if ($generateguid == 1)
        {
            // GUID übernehmen
            $library_guid = $this->ReadPropertyString("library_guid");
            $io_guid = $this->ReadPropertyString("io_guid");
            $rx_guid = $this->ReadPropertyString("rx_guid");
            $tx_guid = $this->ReadPropertyString("tx_guid");
            $splitter_guid = $this->ReadPropertyString("splitter_guid");
            $splitterinterface_guid = $this->ReadPropertyString("splitterinterface_guid"); // Interface GUI
            $device_guid = $this->ReadPropertyString("device_guid");
            $deviceinterface_guid = $this->ReadPropertyString("deviceinterface_guid"); // Interface GUI

        }
        else
        {
            // GUID anlegen
            $library_guid = $this->getGUID(); // Verweis im Überverzeichnis
            $io_guid = $this->getGUID(); //
            $rx_guid = $this->getGUID(); //
            $tx_guid = $this->getGUID(); //
            $splitter_guid = $this->getGUID(); //
            $splitterinterface_guid = $this->getGUID(); // Interface GUI
            $device_guid = $this->getGUID(); //
            $deviceinterface_guid = $this->getGUID(); // Interface GUI
        }
        $guids = array ("library_guid" => $library_guid, "io_guid" => $io_guid, "rx_guid" => $rx_guid, "tx_guid" => $tx_guid, "splitter_guid" => $splitter_guid, "splitterinterface_guid" => $splitterinterface_guid, "device_guid" => $device_guid, "deviceinterface_guid" => $deviceinterface_guid);
        $this->CreateLibraryJSON($guids);
        $this->CreateReadme($guids);
        if($ownio == 1)
        {
            $this->CreateIO($guids);
        }
        if($dataflowtype == 0)
        {
            $this->CreateSplitter($guids);
        }
        $this->CreateDevice($guids);
    }

    protected function CreateIO($guids)
    {
        $library_guid = $guids["library_guid"];
        $aliases = $this->ReadPropertyString("aliases");
        $type = "IO";
        $ident = $this->CreateGUIDIdent($library_guid, $type);
        $CategoryID = $this->CreateCategory($ident, $type, $aliases);
        $io_guid = $guids["io_guid"];
        $ioidentjson = $this->CreateGUIDIdent($io_guid, $type."JSON");
        $ioidentphp = $this->CreateGUIDIdent($io_guid, $type."PHP");
        $this->CreateIOModuleJSONScript($guids, $ioidentjson, $CategoryID);
        $this->CreateIOModulePHPScript($guids, $ioidentphp, $CategoryID);
    }


    protected function CreateSplitter($guids)
    {
        $library_guid = $guids["library_guid"];
        $aliases = $this->ReadPropertyString("aliases");
        $type = "Splitter";
        $ident = $this->CreateGUIDIdent($library_guid, $type);
        $CategoryID = $this->CreateCategory($ident, $type, $aliases);
        $splitter_guid = $guids["splitter_guid"];
        $splitteridentjson = $this->CreateGUIDIdent($splitter_guid, $type."JSON");
        $splitteridentphp = $this->CreateGUIDIdent($splitter_guid, $type."PHP");
        $this->CreateSplitterModuleJSONScript($guids, $splitteridentjson, $CategoryID);
        $this->CreateSplitterModulePHPScript($guids, $splitteridentphp, $CategoryID);
    }

    protected function CreateDevice($guids)
    {
        $library_guid = $guids["library_guid"];
        $aliases = $this->ReadPropertyString("aliases");
        $type = "Device";
        $ident = $this->CreateGUIDIdent($library_guid, $type);
        $CategoryID = $this->CreateCategory($ident, $type, $aliases);
        $device_guid = $guids["device_guid"];
        $deviceidentjson = $this->CreateGUIDIdent($device_guid, $type."JSON");
        $deviceidentphp = $this->CreateGUIDIdent($device_guid, $type."PHP");
        $this->CreateDeviceModuleJSONScript($guids, $deviceidentjson, $CategoryID);
        $this->CreateDeviceModulePHPScript($guids, $deviceidentphp, $CategoryID);
    }

    protected function CreateCategory($ident, $type, $aliases)
    {
        $ModuleCategoryID = $this->ReadPropertyInteger("CategoryID");
        //Prüfen ob Kategorie schon existiert
        $CategoryID = @IPS_GetObjectIDByIdent($ident, $ModuleCategoryID);
        if ($CategoryID === false)
        {
            $CategoryID = IPS_CreateCategory();
            IPS_SetName($CategoryID, $type." ".$aliases);
            IPS_SetIdent($CategoryID, $ident);
            IPS_SetInfo($CategoryID, $type);
            IPS_SetParent($CategoryID, $ModuleCategoryID);
        }
        return $CategoryID;
    }

    protected function GetIOGUID($typeio)
    {
        if($typeio == 1) // Multicast Socket (Virtual I/O)
        {
            $io_guid = $this->ReadPropertyString("io_multicast_guid");
            $rx_guid = $this->ReadPropertyString("virtual_io_rx_guid");
            $tx_guid = $this->ReadPropertyString("virtual_io_tx_guid");
        }
        elseif ($typeio == 2) // Server Socket (Virtual I/O)
        {
            $io_guid = $this->ReadPropertyString("io_serversocket_guid");
			$rx_guid = $this->ReadPropertyString("virtual_io_rx_guid");
			$tx_guid = $this->ReadPropertyString("virtual_io_tx_guid");
        }
        elseif ($typeio == 3) // UDP Socket (Virtual I/O)
        {
            $io_guid = $this->ReadPropertyString("io_udpsocket_guid");
            $rx_guid = $this->ReadPropertyString("virtual_io_rx_guid");
            $tx_guid = $this->ReadPropertyString("virtual_io_tx_guid");
        }
        elseif ($typeio == 4) // Serial Port
        {
            $io_guid = $this->ReadPropertyString("io_serialport_guid");
            $rx_guid = $this->ReadPropertyString("virtual_io_rx_guid");
            $tx_guid = $this->ReadPropertyString("virtual_io_tx_guid");
        }
        elseif ($typeio == 5) // HID
        {
            $io_guid = $this->ReadPropertyString("io_hid_guid");
            $rx_guid = $this->ReadPropertyString("hid_rx_guid");
            $tx_guid = $this->ReadPropertyString("hid_tx_guid");
        }
        elseif ($typeio == 5) // WWW Reader
        {
            $io_guid = $this->ReadPropertyString("io_wwwreader_guid");
            $rx_guid = $this->ReadPropertyString("www_reader_rx_guid");
            $tx_guid = $this->ReadPropertyString("www_reader_tx_guid");
        }
		elseif ($typeio == 5) // WWW Reader
		{
			$io_guid = $this->ReadPropertyString("io_wwwreader_guid");
			$rx_guid = $this->ReadPropertyString("www_reader_rx_guid");
			$tx_guid = $this->ReadPropertyString("www_reader_tx_guid");
		}
		elseif ($typeio == 6) // WWW Reader
		{
			$io_guid = $this->ReadPropertyString("io_wwwreader_guid");
			$rx_guid = $this->ReadPropertyString("www_reader_rx_guid");
			$tx_guid = $this->ReadPropertyString("www_reader_tx_guid");
		}
		elseif ($typeio == 7) // Multicast Socket
		{
			$io_guid = $this->ReadPropertyString("io_multicast_guid");
			$rx_guid = $this->ReadPropertyString("server_rx_guid");
			$tx_guid = $this->ReadPropertyString("server_tx_guid");
		}
		elseif ($typeio == 8) // Server Socket
		{
			$io_guid = $this->ReadPropertyString("io_serversocket_guid");
			$rx_guid = $this->ReadPropertyString("server_rx_guid");
			$tx_guid = $this->ReadPropertyString("server_tx_guid");
		}
		elseif ($typeio == 9) // UDP Socket
		{
			$io_guid = $this->ReadPropertyString("io_udpsocket_guid");
			$rx_guid = $this->ReadPropertyString("server_rx_guid");
			$tx_guid = $this->ReadPropertyString("server_tx_guid");
		}
        else // Clientsocket
        {
            $io_guid = $this->ReadPropertyString("io_clientsocket_guid");
            $rx_guid = $this->ReadPropertyString("virtual_io_rx_guid");
            $tx_guid = $this->ReadPropertyString("virtual_io_tx_guid");
        }
        $io_guids = array ("io_guid" => $io_guid, "rx_guid" => $rx_guid, "tx_guid" => $tx_guid);
        return $io_guids;
    }

    protected function CreateReadme($guids)
    {
        $library_guid = $guids["library_guid"];
        $ModuleCategoryID = $this->ReadPropertyInteger("CategoryID");
        $prefix = $this->ReadPropertyString("prefix");
        $device_guid = $guids["device_guid"];
        $modulename = $this->ReadPropertyString("modulename");

        $ident = $this->CreateGUIDIdent($library_guid, "readme");
        $Scriptname = "README_md";
        $ScriptID = @IPS_GetObjectIDByIdent($ident, $ModuleCategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $ModuleCategoryID);
            IPS_SetIdent($ScriptID, $ident);
            $content = '# '.$modulename.'

Modul für IP-Symcon ab Version 4.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguartion)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Beschreibung 

### Funktionen:  

 - Funktion 1 
 - funktion 2
	  

## 2. Voraussetzungen

 - IPS 4.x
 - weitere Vorraussetzungen.

## 3. Installation

### a. Laden des Moduls

Die IP-Symcon (min Ver. 4.x) Konsole öffnen. Im Objektbaum unter Kerninstanzen die Instanz __*Modules*__ durch einen doppelten Mausklick öffnen.

In der _Modules_ Instanz rechts oben auf den Button __*Hinzufügen*__ drücken.
 
In dem sich öffnenden Fenster folgende URL hinzufügen:

	
    `https://github.com/Git_Name/IPSymconXXX`  
    
und mit _OK_ bestätigen.    
        
Anschließend erscheint ein Eintrag für das Modul in der Liste der Instanz _Modules_    


### b. Einrichtung in IPS

In IP-Symcon nun _Instanz hinzufügen_ (_CTRL+1_) auswählen unter der Kategorie, unter der man die Instanz hinzufügen will, und _xxx_ auswählen.


## 4. Funktionsreferenz

### Überschrift:

Beschreibungstext
	


## 5. Konfiguration:

### Überschrift:

| Eigenschaft | Typ     | Standardwert | Funktion                                  |
| :---------: | :-----: | :----------: | :---------------------------------------: |
| Wert 1      | string  |              | Beschreibung                              |
| Wert 2      | integer |    20        | Beschreibung                              |






## 6. Anhang

###  a. Funktionen:

#### Überschrift:

`'.$prefix.'_Function(integer $InstanceID)`

Beschreibung Funktion


###  b. GUIDs und Datenaustausch:

#### Überschrift:

GUID: `'.$device_guid.'` ';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateLibraryJSON($guids)
    {
        $library_guid = $guids["library_guid"];
        $ModuleCategoryID = $this->ReadPropertyInteger("CategoryID");
        $author = $this->ReadPropertyString("author");
        $modulename = $this->ReadPropertyString("modulename");
        $url = $this->ReadPropertyString("url");
        $version = $this->ReadPropertyString("version");
        $build = $this->ReadPropertyInteger("build");
        $date = 0;

        $ident = $this->CreateGUIDIdent($library_guid, "lib");
        $Scriptname = "library_json";
        $ScriptID = @IPS_GetObjectIDByIdent($ident, $ModuleCategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $ModuleCategoryID);
            IPS_SetIdent($ScriptID, $ident);
            $content = '{
	"id": "'.$library_guid.'",
	"author": "'.$author.'",
	"name": "'.$modulename.'",
	"url": "'.$url.'",
	"compatibility": {
    "version": "4.2",
    "date": 1491343200
	},
	"version": "'.$version.'",
	"build": '.$build.',
	"date": '.$date.'
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateIOModuleJSONScript($guids, $ioident, $CategoryID)
    {
    	$vendor = $this->ReadPropertyString("vendor");
        $prefix = $this->ReadPropertyString("prefix");
        $aliases = $this->ReadPropertyString("aliases");
        $modulename = $this->ReadPropertyString("modulename");
        $io_guid = $guids["io_guid"];
        $rx_guid = $guids["rx_guid"];
        $tx_guid = $guids["tx_guid"];

        $Scriptname = "module_json";
        $ScriptID = @IPS_GetObjectIDByIdent($ioident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $ioident);
			$content = '{
	"id": "'.$io_guid.'",
	"name": "'.$modulename.'",
	"type": 3,
	"vendor": "'.$vendor.'",
	"aliases": ["'.$aliases.'"],
	"parentRequirements": [],
	"childRequirements": ["'.$rx_guid.'"],
	"implemented": ["'.$tx_guid .'"],
	"prefix": "'.$prefix.'"
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateIOModulePHPScript($guids, $ioident, $CategoryID)
    {
        $modulename = $this->ReadPropertyString("modulename");
        $ownio = $this->ReadPropertyInteger("ownio");
        if($ownio == 1)
        {
            $rx_guid = $guids["rx_guid"];
        }
        else
        {
            $typeio = $this->ReadPropertyInteger("typeio");
            $ioguids = $this->GetIOGUID($typeio);
            $rx_guid = $ioguids["rx_guid"];
        }


        $Scriptname = "module_php";
        $ScriptID = @IPS_GetObjectIDByIdent($ioident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $ioident);
            $content = '<?

class '.$modulename.'IO extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
       
    }
    
    // $data wird von einer anderen Funktion geliefert z.B. Webhook, Curl usw.
    protected function SendJSON ($data)
	{
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "'.$rx_guid.'", "Buffer" => $data))); //  I/O RX GUI
	}
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateSplitterModuleJSONScript($guids, $splitterident, $CategoryID)
    {
        // Später auslesen
        $vendor = $this->ReadPropertyString("vendor");
        $prefix = $this->ReadPropertyString("prefix");
        $aliases = $this->ReadPropertyString("aliases");
        $modulename = $this->ReadPropertyString("modulename");
        $ownio = $this->ReadPropertyInteger("ownio");
        if($ownio == 1)
        {
            $rx_guid = $guids["rx_guid"]; //
            $tx_guid = $guids["tx_guid"]; //
        }
        else
        {
            $typeio = $this->ReadPropertyInteger("typeio");
            $ioguids = $this->GetIOGUID($typeio);
            $rx_guid = $ioguids["rx_guid"];
            $tx_guid = $ioguids["tx_guid"];
        }

        $splitter_guid = $guids["splitter_guid"]; //
        $splitterinterface_guid = $guids["splitterinterface_guid"]; // Interface GUI
        $deviceinterface_guid = $guids["deviceinterface_guid"]; // Interface GUI

        $Scriptname = "module_json";
        $ScriptID = @IPS_GetObjectIDByIdent($splitterident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $splitterident);
            $content = '{
    "id": "'.$splitter_guid.'",
    "name": "'.$modulename.'Splitter",
    "type": 2,
    "vendor": "'.$vendor.'",
    "aliases": ["'.$aliases.' Splitter"],
    "parentRequirements": ["'.$tx_guid.'"],
    "childRequirements": ["'.$splitterinterface_guid.'"],
    "implemented": ["'.$rx_guid.'", "'.$deviceinterface_guid.'"],
    "prefix": "'.$prefix.'S"
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateSplitterModulePHPScript($guids, $splitterident, $CategoryID)
    {
        $modulename = $this->ReadPropertyString("modulename");
        $ownio = $this->ReadPropertyInteger("ownio");
        if($ownio == 1)
        {
            $io_guid = $guids["io_guid"];
            $tx_guid = $guids["tx_guid"];
        }
        else
        {
            $typeio = $this->ReadPropertyInteger("typeio");
            $ioguids = $this->GetIOGUID($typeio);
            $io_guid = $ioguids["io_guid"];
            $tx_guid = $ioguids["tx_guid"];
        }
        $splitterinterface_guid = $guids["splitterinterface_guid"]; // Interface GUI

        $Scriptname = "module_php";
        $ScriptID = @IPS_GetObjectIDByIdent($splitterident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $splitterident);
            $content = '<?

class '.$modulename.'Splitter extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		$this->RequireParent("'.$io_guid.'"); //  I/O
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
       
    }
    
    // Data an Child weitergeben
    // Type String, Declaration can be used when PHP 7 is available
    //public function ReceiveData(string $JSONString)
	public function ReceiveData($JSONString)
	{
	 
		// Empfangene Daten vom I/O
		$data = json_decode($JSONString);
		$dataio = json_encode($data->Buffer);
		$this->SendDebug("Splitter ReceiveData:",$dataio,0);
			
		// Hier werden die Daten verarbeitet
		
	 
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "'.$splitterinterface_guid.'", "Buffer" => $data->Buffer))); // Splitter Interface GUI
	}
	
	// Type String, Declaration can be used when PHP 7 is available
    //public function ForwardData(string $JSONString)
    public function ForwardData($JSONString)
	{
	 
		// Empfangene Daten von der Device Instanz
		$data = json_decode($JSONString);
		$datasend = $data->Buffer;
		$datasend = json_encode($datasend);
		$this->SendDebug("Splitter Forward Data:",$datasend,0);
			
		// Hier würde man den Buffer im Normalfall verarbeiten
		// z.B. CRC prüfen, in Einzelteile zerlegen
		
		// Weiterleiten zur I/O Instanz
		$result = $this->SendDataToParent(json_encode(Array("DataID" => "'.$tx_guid.'", "Buffer" => $data->Buffer))); // TX GUI
			
		return $result;
	 
	}
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateDeviceModuleJSONScript($guids, $deviceident, $CategoryID)
    {
	$dataflowtype = $this->ReadPropertyInteger("dataflowtype");
    	$vendor = $this->ReadPropertyString("vendor");
        $prefix = $this->ReadPropertyString("prefix");
        $aliases = $this->ReadPropertyString("aliases");
        $modulename = $this->ReadPropertyString("modulename");
		$tx_guid = $guids["rx_guid"]; // TX
        $rx_guid = $guids["rx_guid"]; // RX
		$splitterinterface_guid = $guids["splitterinterface_guid"]; // Interface GUI
        $device_guid = $guids["device_guid"];
        $deviceinterface_guid = $guids["deviceinterface_guid"]; // Interface GUI

        $Scriptname = "module_json";
        $ScriptID = @IPS_GetObjectIDByIdent($deviceident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $deviceident);
            if($dataflowtype == 0) // I/O, Splitter
			{
				$content = '{
	"id": "'.$device_guid.'",
	"name": "'.$modulename.'",
	"type": 3,
	"vendor": "'.$vendor.'",
	"aliases": ["'.$aliases.'"],
	"parentRequirements": ["'.$deviceinterface_guid.'"],
	"childRequirements": [],
	"implemented": ["'.$splitterinterface_guid.'"],
	"prefix": "'.$prefix.'"
}';
			}
			elseif($dataflowtype == 1) // I/O
			{
				$content = '{
	"id": "'.$device_guid.'",
	"name": "'.$modulename.'",
	"type": 3,
	"vendor": "'.$vendor.'",
	"aliases": ["'.$aliases.'"],
	"parentRequirements": ["'.$tx_guid.'"],
	"childRequirements": [],
	"implemented": ["'.$rx_guid .'"],
	"prefix": "'.$prefix.'"
}';
			}
			else{
				$content = '{
	"id": "'.$device_guid.'",
	"name": "'.$modulename.'",
	"type": 3,
	"vendor": "'.$vendor.'",
	"aliases": ["'.$aliases.'"],
	"parentRequirements": [],
	"childRequirements": [],
	"implemented": [],
	"prefix": "'.$prefix.'"
}';
			}

            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateDeviceModulePHPScript($guids, $deviceident, $CategoryID)
    {
		$dataflowtype = $this->ReadPropertyInteger("dataflowtype");
    	$modulename = $this->ReadPropertyString("modulename");
		$io_guid = $guids["io_guid"];
		$splitter_guid = $guids["splitter_guid"];
        $deviceinterface_guid = $guids["deviceinterface_guid"]; // Interface GUI

        $Scriptname = "module_php";
        $ScriptID = @IPS_GetObjectIDByIdent($deviceident, $CategoryID);

        if ($ScriptID === false)
        {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $Scriptname);
            IPS_SetParent($ScriptID, $CategoryID);
            IPS_SetIdent($ScriptID, $deviceident);
            
            
            $content = '<?
/**
 * Title: FS20 RSU Shutter Control
  *
 * author PiTo
 * 
 * GITHUB = <https://github.com/SymPiTo/MySymCodes/tree/master/MyFS20_SC>
 * 
 * Version:1.0.2018.08.21
 */
//Class: '.$modulename.'
class '.$modulename.' extends IPSModule
{
    /* 
    _______________________________________________________________________ 
     Section: Internal Modul Funtions
     Die folgenden Funktionen sind Standard Funktionen zur Modul Erstellung.
    _______________________________________________________________________ 
     */
            
    /* ------------------------------------------------------------ 
    Function: Create  
    Create() wird einmalig beim Erstellen einer neuen Instanz und 
    neu laden der Modulesausgeführt. Vorhandene Variable werden nicht veändert, auch nicht 
    eingetragene Werte (Properties).
    Variable können hier nicht verwendet werden nur statische Werte.
    Überschreibt die interne IPS_Create(§id)  Funktion
   
     CONFIG-VARIABLE:
      FS20RSU_ID   -   ID des FS20RSU Modules (selektierbar).
     
    STANDARD-AKTIONEN:
      FSSC_Position    -   Position (integer)

    ------------------------------------------------------------- */
    public function Create()
    {
	//Never delete this line!
        parent::Create();
'.' 
        // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
        // Aufruf dieser Form Variable mit  $this->ReadPropertyFloat("IDENTNAME")
        //$this->RegisterPropertyInteger("IDENTNAME", 0);
        //$this->RegisterPropertyFloat("IDENTNAME", 0.5);
        //$this->RegisterPropertyBoolean("IDENTNAME", false);
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string $Ident, string $Name, string $Profil, integer $Position )
        //Aufruf dieser Variable mit $this->GetIDForIdent("IDENTNAME")
        //$this->RegisterVariableInteger("FSSC_Position", "Position", "Rollo.Position");
      
        //Boolean Variable anlegen
        //integer RegisterVariableBoolean ( string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit $this->GetIDForIdent("IDENTNAME")
        //$this->RegisterVariableBoolean("FSSC_Mode", "Mode");
        
        //String Variable anlegen
        //RegisterVariableString ($Ident,  $Name, $Profil, $Position )
        //Aufruf dieser Variable mit $this->GetIDForIdent("IDENTNAME")
        //$this->RegisterVariableString("SZ_MoFr", "SchaltZeiten Mo-Fr");
 
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        //$this->EnableAction("IDENTNAME");
        
        //IPS_SetVariableCustomProfile(§this->GetIDForIdent("Mode"), "Rollo.Mode");
        
        //anlegen eines Timers
        //$this->RegisterTimer("TimerName", 0, "FSSC_reset($_IPS[!TARGET!>]);");
            


    .';
            if($dataflowtype == 0)
			{
				$content .= '$this->ConnectParent("'.$splitter_guid.'"); // Splitter';
			}
			elseif($dataflowtype == 1)
			{
				$content .= '$this->ConnectParent("'.$io_guid.'"); // I/O';
			}
    $content .='}
   /* ------------------------------------------------------------ 
     Function: ApplyChanges 
      ApplyChanges() Wird ausgeführt, wenn auf der Konfigurationsseite "Übernehmen" gedrückt wird 
      und nach dem unittelbaren Erstellen der Instanz.
     
    SYSTEM-VARIABLE:
        InstanceID - $this->InstanceID.

    EVENTS:
        SwitchTimeEvent".$this->InstanceID   -   Wochenplan (Mo-Fr und Sa-So)
        SunRiseEvent".$this->InstanceID       -   cyclice Time Event jeden Tag at SunRise
    ------------------------------------------------------------- */
    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
       
    }
    
   /* ------------------------------------------------------------ 
      Function: RequestAction  
      RequestAction() Wird ausgeführt, wenn auf der Webfront eine Variable
      geschaltet oder verändert wird. Es werden die System Variable des betätigten
      Elementes übergeben.
      Ausgaben über echo werden an die Visualisierung zurückgeleitet
     
   
    SYSTEM-VARIABLE:
      $this->GetIDForIdent($Ident)     -   ID der von WebFront geschalteten Variable
      $Value                           -   Wert der von Webfront geänderten Variable

   STANDARD-AKTIONEN:
      FSSC_Position    -   Slider für Position
      UpDown           -   Switch für up / Down
      Mode             -   Switch für Automatik/Manual
     ------------------------------------------------------------- */
    public function RequestAction($Ident, $Value) {
         switch($Ident) {
            case "UpDown":
                SetValue($this->GetIDForIdent($Ident), $Value);
                if(getvalue($this->GetIDForIdent($Ident))){
                    $this->SetRolloDown();  
                }
                else{
                    $this->SetRolloUp();
                }
                break;
             case "Mode":
                $this->SetMode($Value);  
                break;
            default:
                throw new Exception("Invalid Ident");
        }
 
    }

  /* ______________________________________________________________________________________________________________________
     Section: Public Funtions
     Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
    
     FSSC_XYFunktion($Instance_id, ... );
     ________________________________________________________________________________________________________________________ */
    //-----------------------------------------------------------------------------
    /* Function: xxxx
    ...............................................................................
    Beschreibung
    ...............................................................................
    Parameters: 
        none
    ...............................................................................
    Returns:    
        none
    ------------------------------------------------------------------------------  */
    public function xxxx(){
       
    }  


   /* _______________________________________________________________________
    * Section: Private Funtions
    * Die folgenden Funktionen sind nur zur internen Verwendung verfügbar
    *   Hilfsfunktionen
    * _______________________________________________________________________
    */  

    protected function SendToSplitter(string $payload)
		{						
			//an Splitter schicken
			$result = $this->SendDataToParent(json_encode(Array("DataID" => "'.$deviceinterface_guid.'", "Buffer" => $payload))); // Interface GUI
			return $result;
		}
		
        /* ----------------------------------------------------------------------------
         Function: GetIPSVersion
        ...............................................................................
        gibt die instalierte IPS Version zurück
        ...............................................................................
        Parameters: 
            none
        ..............................................................................
        Returns:   
            $ipsversion (floatint)
        ------------------------------------------------------------------------------- */
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

 
    /* --------------------------------------------------------------------------- 
    Function: RegisterEvent
    ...............................................................................
    legt einen Event an wenn nicht schon vorhanden
      Beispiel:
      ("Wochenplan", "SwitchTimeEvent".$this->InstanceID, 2, $this->InstanceID, 20);  
      ...............................................................................
    Parameters: 
      $Name        -   Name des Events
      $Ident       -   Ident Name des Events
      $Typ         -   Typ des Events (1=cyclic 2=Wochenplan)
      $Parent      -   ID des Parents
      $Position    -   Position der Instanz
    ...............................................................................
    Returns:    
        none
    -------------------------------------------------------------------------------*/
    private function RegisterEvent($Name, $Ident, $Typ, $Parent, $Position)
    {
            $eid = @$this->GetIDForIdent($Ident);
            if($eid === false) {
                    $eid = 0;
            } elseif(IPS_GetEvent($eid)[!EventType!] <> $Typ) {
                    IPS_DeleteEvent($eid);
                    $eid = 0;
            }
            //we need to create one
            if ($eid == 0) {
                    $EventID = IPS_CreateEvent($Typ);
                    IPS_SetParent($EventID, $Parent);
                    IPS_SetIdent($EventID, $Ident);
                    IPS_SetName($EventID, $Name);
                    IPS_SetPosition($EventID, $Position);
                    IPS_SetEventActive($EventID, false);  
            }
    }
    
 
    /* ----------------------------------------------------------------------------------------------------- 
    Function: RegisterScheduleAction
    ...............................................................................
     *  Legt eine Aktion für den Event fest
     * Beispiel:
     * ("SwitchTimeEvent".$this->InstanceID), 1, "Down", 0xFF0040, "FSSC_SetRolloDown(\$_IPS[!TARGET!]);");
    ...............................................................................
    Parameters: 
      $EventID
      $ActionID
      $Name
      $Color
      $Script
    .......................................................................................................
    Returns:    
        none
    -------------------------------------------------------------------------------------------------------- */
    private function RegisterScheduleAction($EventID, $ActionID, $Name, $Color, $Script)
    {
            IPS_SetEventScheduleAction($EventID, $ActionID, $Name, $Color, $Script);
    }



		
}';
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateGUIDIdent($guid, $type)
    {
        $search = array("-", "{", "}");
        $replace = array("", "", "");
        $guid = str_replace($search, $replace, $guid);
        $ident = $guid."_".$type;
        return $ident;
    }
	
	public function RequestAction($Ident, $Value)
    {
        switch($Ident) {
            case "CreateScripts":
                $this->CreateScripts();
				break;
            default:
                throw new Exception("Invalid ident");
        }
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


    //Configuration Form

    public function GetConfigurationForm()
    {
        $formhead = $this->FormHead();
        $formactions = $this->FormActions();
        $formelementsend = '{ "type": "Label", "label": "__________________________________________________________________________________________________" }';
        $formstatus = $this->FormStatus();
        return	'{ '.$formhead.$formelementsend.'],'.$formactions.$formstatus.' }';
    }


    // Show GUID field. Otherwise create GUID
    private function UseExistingGUID()
    {
        $generateguid = $this->ReadPropertyInteger("generateguid");
        $dataflowtype = $this->ReadPropertyInteger("dataflowtype");
        $ownio = $this->ReadPropertyInteger("ownio");
        if($generateguid == 1)
        {
            $form = '{ "type": "ValidationTextBox", "name": "library_guid", "caption": "GUID library" },';
            if($ownio == 1)
            {
                $form .= '{ "type": "ValidationTextBox", "name": "io_guid", "caption": "GUID IO" },
                { "type": "ValidationTextBox", "name": "rx_guid", "caption": "GUID RX" },
                { "type": "ValidationTextBox", "name": "tx_guid", "caption": "GUID TX" },';
            }

            if($dataflowtype == 0) // IO / Splitter / Device
            {
                $form .= '{ "type": "ValidationTextBox", "name": "splitter_guid", "caption": "GUID splitter" },
                { "type": "ValidationTextBox", "name": "splitterinterface_guid", "caption": "GUID splitter interface" },';
            }
            $form .= '{ "type": "ValidationTextBox", "name": "device_guid", "caption": "GUID device" },
                { "type": "ValidationTextBox", "name": "deviceinterface_guid", "caption": "GUID device interface" },';
        }
        else
        {
            $form = '';
        }
        return $form;
    }

    private function ChooseDataFlowType()
    {
        $form = '{ "type": "Label", "label": "Choose type of dataflow" },
                { "type": "Select", "name": "dataflowtype", "caption": "Dataflow Type",
                  "options": [
                    { "label": "IO / Splitter / Device", "value": 0 },
                    { "label": "IO / Device", "value": 1 },
                    { "label": "Device", "value": 2 }
                  ]
                },
                { "type": "Label", "label": "Use own IO or standard IP-Symcon IO" },
                { "type": "Select", "name": "ownio", "caption": "Own IO",
                  "options": [
                    { "label": "Standard IO", "value": 0 },
                    { "label": "Own IO", "value": 1 }
                  ]
                },';
        return $form;
    }

    // Choose IO
    private function SelectIOType()
    {
        $ownio = $this->ReadPropertyInteger("ownio");
        if($ownio == 0)
        {
            $form = '{ "type": "Label", "label": "Select Type of IO" },
                { "type": "Select", "name": "typeio", "caption": "IO type",
                  "options": [
                    { "label": "Clientsocket", "value": 0 },
                    { "label": "Multicast Socket (Virtual I/O)", "value": 1 },
                    { "label": "Server Socket (Virtual I/O)", "value": 2 },
                    { "label": "UDP Socket (Virtual I/O)", "value": 3 },
                    { "label": "Serial Port", "value": 4 },
                    { "label": "HID", "value": 5 },
                    { "label": "WWW Reader", "value": 6 },
                    { "label": "Multicast Socket", "value": 7 },
                    { "label": "Server Socket", "value": 8 },
                    { "label": "UDP Socket", "value": 9 }
                  ]
                },';
        }
        else
        {
            $form = '';
        }
        return $form;
    }

    protected function FormHead()
    {
        $form = '"elements":
            [
                { "type": "Label", "label": "This module is creating templates for the dataflow in a PHP module for IP-Symcon" },
                { "type": "Label", "label": "Select category for the scripts" },
                { "type": "SelectCategory", "name": "CategoryID", "caption": "Category" },
                { "type": "ValidationTextBox", "name": "author", "caption": "author" },
                { "type": "ValidationTextBox", "name": "modulename", "caption": "module name" },
                { "type": "ValidationTextBox", "name": "url", "caption": "url" },
                { "type": "ValidationTextBox", "name": "version", "caption": "version" },
                { "type": "NumberSpinner", "name": "build", "caption": "build", "digits": 1},
                { "type": "ValidationTextBox", "name": "vendor", "caption": "vendor" },
                { "type": "ValidationTextBox", "name": "prefix", "caption": "prefix" },
                { "type": "ValidationTextBox", "name": "aliases", "caption": "aliases" },
                { "type": "Label", "label": "Use existing GUID or generate GUID" },
                { "type": "Select", "name": "generateguid", "caption": "GUID",
                  "options": [
                    { "label": "Generate GUID", "value": 0 },
                    { "label": "Existing GUID", "value": 1 }
                  ]
                },
               '.$this->ChooseDataFlowType().$this->UseExistingGUID().$this->SelectIOType();

        return $form;
    }

    protected function FormActions()
    {
        $form = '"actions":
			[
				{ "type": "Label", "label": "Generate GUIDs" },
				{ "type": "Button", "label": "Generate GUID", "onClick": "MG_GenerateGUID($id);" },
				{ "type": "Label", "label": "Generate scripts" },
				{ "type": "Button", "label": "Generate Scripts", "onClick": "MG_CreateScripts($id);" }
			],';
        return  $form;
    }

    protected function FormStatus()
    {
        $form = '"status":
            [
                {
                    "code": 101,
                    "icon": "inactive",
                    "caption": "Creating instance."
                },
				{
                    "code": 102,
                    "icon": "active",
                    "caption": "Module active"
                },
                {
                    "code": 104,
                    "icon": "inactive",
                    "caption": "interface closed."
                },
                {
                    "code": 202,
                    "icon": "error",
                    "caption": "field must not empty."
                },
				{
                    "code": 203,
                    "icon": "error",
                    "caption": "Not valid."
                },
				{
                    "code": 205,
                    "icon": "error",
                    "caption": "field must not be empty."
                },
				{
                    "code": 206,
                    "icon": "error",
                    "caption": "category must not be empty."
                }
            ]';
        return $form;
    }

}

?>
