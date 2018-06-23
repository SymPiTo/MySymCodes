<?php

require_once(__DIR__ . "/UpnpClassTrait.php");
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the  editor.
 */

/**
 * Description of module
 *f
 * @author Torsten Beck
 */
class MyUpnp extends IPSModule {

    //Traits verbinden
    
    use upnp;
    
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
        
        $this->RegisterPropertyBoolean("active", false);

        // Category anlegen
        // Anlegen einer neuen Kategorie 
       // $KategorieID = @IPS_GetCategoryIDByName("DIDL", $this->InstanceID);
        //if ($KategorieID === false){
            //$CatID = IPS_CreateCategory();       // Kategorie anlegen
            //IPS_SetName($CatID, "DIDL"); // Kategorie benennen
            //IPS_SetParent($CatID, $this->InstanceID); // Kategorie einsortieren unter dem Objekt mit der ID "12345"  
              //Status Variable anlegen
            $this->RegisterVariableString("upnp_Artist", "Artist [dc:creator]");
            $this->RegisterVariableString("upnp_Album", "Album [upnp:album]");
            $this->RegisterVariableString("upnp_Title", "Titel [dc:title]");
            $this->RegisterVariableString("upnp_Description", "Description [dc:description]");
            $this->RegisterVariableString("upnp_AlbumArtUri", "AlbumArtURI [upnp:albumArtURI]");
            $this->RegisterVariableString("upnp_Genre", "Genre [upnp:genre]");
            $this->RegisterVariableString("upnp_Date", "Date [dc:date]");
            $this->RegisterVariableString("upnp_TrackNo", "TrackNumber [upnp:originalTrackNumber]");
            //$ID_CatDIDL =  IPS_GetCategoryIDByName("DIDL", $this->InstanceID);
            //Verschieben der Variable unter Ordner DIDL
            //IPS_SetParent($this->GetIDForIdent("upnp_Album"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_Title"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_Description"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_AlbumArtUri"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_Genre"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_Date"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_TrackNo"), $ID_CatDIDL);
            //IPS_SetParent($this->GetIDForIdent("upnp_Artist"),$ID_CatDIDL);              
        //}

        //$KategorieID = @IPS_GetCategoryIDByName("PositionInfo", $this->InstanceID);
        //if ($KategorieID === false){        
            //$CatID = IPS_CreateCategory();       // Kategorie anlegen
            //IPS_SetName($CatID, "PositionInfo"); // Kategorie benennen
            //IPS_SetParent($CatID, $this->InstanceID); 
            //Status Variable anlegen;
        
            $this->RegisterVariableInteger("upnp_Progress", "Progress", "UPNP_Progress");
            $this->RegisterVariableInteger("upnp_Track", "Pos:Track", "");
            $this->RegisterVariableString("upnp_Transport_Status", "Pos:Transport_Status");
            $this->RegisterVariableString("upnp_TrackDuration", "Pos:TrackDuration [upnp:album]");
            $this->RegisterVariableString("upnp_TrackMetaData", "Pos:TrackMetaData");
            $this->RegisterVariableString("upnp_TrackURI", "Pos:TrackURI");
            $this->RegisterVariableString("upnp_RelTime", "Pos:RelTime");
            $this->RegisterVariableString("upnp_AbsTime", "Pos:AbsTime");
            $this->RegisterVariableString("upnp_RelCount", "Pos:RelCount");
            $this->RegisterVariableString("upnp_AbsCount", "Pos:AbsCount");
            //$ID_PosInfo =  IPS_GetCategoryIDByName("PositionInfo", $this->InstanceID);
            //Verschieben der Variable unter Ordner PositionInfo
            //IPS_SetParent($this->GetIDForIdent("upnp_Progress"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_Transport_Status"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_TrackDuration"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_TrackMetaData"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_TrackURI"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_RelTime"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_AbsTime"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_RelCount"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_AbsCount"), $ID_PosInfo);
            //IPS_SetParent($this->GetIDForIdent("upnp_Track"), $ID_PosInfo);       
        //}
        // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            //$this->RegisterPropertyBoolean("active", false);
            //$this->RegisterPropertyString("IPAddress", "");
            //$this->RegisterPropertyInteger("UpdateInterval", 30);
           

        

                
        $this->RegisterVariableString("upnp_ClientArray", "Client:Array");
        $this->RegisterVariableString("upnp_ClientControlURL", "Client:ControlURL");
        $this->RegisterVariableString("upnp_ClientIcon", "Client:Icon");
        $this->RegisterVariableString("upnp_ClienIP", "Client:IP");  
        $this->RegisterVariableInteger("upnp_ClientKey", "Client:Key", "");
        $this->RegisterVariableString("upnp_ClientName", "Client:Name");
        $this->RegisterVariableString("upnp_ClientPort", "Client:Port");
        $this->RegisterVariableString("upnp_ClientRenderingControlURL", "Client:RenderingControlURL");
        
        $this->RegisterVariableString("upnp_ServerArray", "Server:Array");
        $this->RegisterVariableString("upnp_ServerContentDirectory", "Server:ContentDirectory");
        $this->RegisterVariableString("upnp_ServerIcon", "Server:Icon");
        $this->RegisterVariableString("upnp_ServerIP", "Server:IP");
        $this->RegisterVariableInteger("upnp_ServerKey", "Server:Key", "");
        $this->RegisterVariableString("upnp_ServerName", "Server:Name");
        $this->RegisterVariableString("upnp_ServerPort", "Server:Port");
        
        $this->RegisterVariableInteger("upnp_NoTracks", "No of tracks", "");
        $this->RegisterVariableString("upnp_PlaylistName", "PlaylistName");
        $this->RegisterVariableString("upnp_Playlist_XML", "Playlist_XML");       
        
        
            //$this->RegisterVariableBoolean("CeolPower", "Power");        
         
            
        // Timer erstellen
        $this->RegisterTimer("upnp_PlayInfo", 1000, 'GetPosInfo();');
    }
        
    // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
     // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
    // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    public function ApplyChanges() {
        // Diese Zeile nicht löschen
        parent::ApplyChanges();
            

    }
    

        


    
    
        /**
        * Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
        * Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
        *
        * CEOL_XYFunktion($id);
        *
        */
	/*//////////////////////////////////////////////////////////////////////////////
	Funktion searchUPNP($member)
	...............................................................................
	Sucht alle UPNP Clients / Server
	...............................................................................
	Parameter:  $member = "client" // "server"
	--------------------------------------------------------------------------------
	Variable: SetValue(ID_CLIENT_ARRAY, $DeviceArray); // ID_SERVER_ARRAY
	--------------------------------------------------------------------------------
	return Device Array[]
						$Device_Array[$i]['DeviceDescription'] 
						$Device_Array[$i]['Root']
						$Device_Array[$i]['DeviceIP']
						$Device_Array[$i]['DevicePort']
						$Device_Array[$i]['ModelName'] 
						$Device_Array[$i]['UDN']
						$Device_Array[$i]['FriendlyName'] 
						$Device_Array[$i]['IconURL'] = $iconurl;
						$Device_Array[$i]['DeviceControlServiceType']
						$Device_Array[$i]['DeviceControlURL']
						$Device_Array[$i]['DeviceRenderingServiceType']
						$Device_Array[$i]['DeviceRenderingControlURL']
						$Device_Array[$i]['DeviceActiveIcon']
	--------------------------------------------------------------------------------
	Status: 
	//////////////////////////////////////////////////////////////////////////////*/
	public function searchUPNP($member){
		/*mögliche Aufrufe:
		$ST_ALL = "ssdp:all";
		$ST_RD = "upnp:rootdevice";
		$ST_AV = "urn:dial-multiscreen-org:service:dial:1"; 
		*/
		$ST_MR = "urn:schemas-upnp-org:device:MediaRenderer:1";
		$ST_MS = "urn:schemas-upnp-org:device:MediaServer:1";
		/*
		$ST_CD = "urn:schemas-upnp-org:service:ContentDirectory:1";
		$ST_RC = "urn:schemas-upnp-org:service:RenderingControl:1";
		--------------------------------------------------------------------------------
		*/
		if ($member == "client"){
			 SetValue($this->GetIDForIdent("upnp_ClientArray"), '');
			$SSDP_Search_Array = $this->mSearch($ST_MR);
			//IPSLog('mSearch Ergebnis ',$SSDP_Search_Array);
			
			$SSDP_Array = $this->array_multi_unique($SSDP_Search_Array);
			//IPSLog('bereinigtes mSearch Ergebnis ',$SSDP_Array);
			
		 	$UPNP_Device_Array = $this->create_UPNP_Device_Array($SSDP_Array); 
			//IPSLog('create Device Ergebnis ',$UPNP_Device_Array);
			//Ergebnis wird als ARRAY in ID_CLIENT_ARRAY in Subfunctions gespeichert;
			$array = getvalue(self::ID_CLIENT_ARRAY);
			if ($array){$result = true;}
			else{$result = false;}
		}
		if ($member == "server"){
			setvalue(self::ID_SERVER_ARRAY, '');
			$SSDP_Search_Array = $this->mSearch($ST_MS);
			$SSDP_Array = $this->array_multi_unique($SSDP_Search_Array);
			//IPSLog('bereinigtes mSearch Ergebnis ',$SSDP_Array);
		 	$UPNP_Server_Array = $this->create_UPNP_Server_Array($SSDP_Array); 
			//Ergebnis wird als ARRAY in ID_Server_ARRAY in Subfunctions gespeichert;
			$array = getvalue(self::ID_SERVER_ARRAY);
			if ($array){$result = true;}
			else{$result = false;}
		}
		//IPSLog('FERTIG ', $result);
		return($result);
	}


	/*//////////////////////////////////////////////////////////////////////////////
	Funktion setClient($clientIP)
	...............................................................................
	Umschalten auf Client
	...............................................................................
	Parameter:  $ClientIP = IP des Clients
	--------------------------------------------------------------------------------
	Variable:
	--------------------------------------------------------------------------------
	return $key - Nummer des Client Arrays
	--------------------------------------------------------------------------------
	Status: checked 2018-06-10
	//////////////////////////////////////////////////////////////////////////////*/
	public function setClient($ClientName){
            $which_key = "FriendlyName";
            $which_value = $ClientName;
            $array = getvalue($this->GetIDForIdent("upnp_ClientArray"));
            $Client_Array = unserialize($array);
            $key = $this->search_key($which_key, $which_value, $Client_Array);

            $Client_Array[$key]['DeviceActiveIcon'] = "image/button_ok_blue_80x80.png";

            
            $ClientIP                   = $Client_Array[$key]['DeviceIP'];
            $ClientPort                 = $Client_Array[$key]['DevicePort'];
            $friendlyName               = $Client_Array[$key]['FriendlyName'];
            $ClientControlServiceType   = $Client_Array[$key]['DeviceControlServiceType'];
            $ClientControlURL           = $Client_Array[$key]['DeviceControlURL'];
            $ClientRenderingServiceType = $Client_Array[$key]['DeviceRenderingServiceType'];
            $ClientRenderingControlURL  = $Client_Array[$key]['DeviceRenderingControlURL'];
            $ClientIconURL              = $Client_Array[$key]['IconURL'];
            SetValue($this->GetIDForIdent("upnp_ClienIP"), $ClientIP);
            SetValue($this->GetIDForIdent("upnp_ClientPort"), $ClientPort);
            SetValue($this->GetIDForIdent("upnp_ClientName"), $friendlyName);
            setvalue($this->GetIDForIdent("upnp_ClientKey"), $key);
            //SetValue(UPNP_Device_ControlServiceType, $DeviceControlServiceType);
            SetValue($this->GetIDForIdent("upnp_ClientControlURL"), $ClientControlURL);
            //SetValue(UPNP_Device_RenderingServiceType, $DeviceRenderingServiceType);
            SetValue($this->GetIDForIdent("upnp_ClientRenderingControlURL"), $ClientRenderingControlURL);
            SetValue($this->GetIDForIdent("upnp_ClientIcon"), $ClientIconURL);
            return $key;
	}
	
	/*//////////////////////////////////////////////////////////////////////////////
	Funktion setServer($serverName)
	...............................................................................
	Umschalten auf Client
	...............................................................................
	Parameter:  $serverName = "Friendly Name des Servers"  = "Plex" // "AVM"
	--------------------------------------------------------------------------------
	Variable:
	--------------------------------------------------------------------------------
	return $key - Nummer des Client Arrays
	--------------------------------------------------------------------------------
	Status: 
	//////////////////////////////////////////////////////////////////////////////*/
	public function setServer($serverName){
		//IPSLog("Starte Funktion : ", 'setServer');
		$which_key = "FriendlyName";
		$which_value = $serverName;
		$array = getvalue($this->GetIDForIdent("upnp_ServerArray"));
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
		SetValue($this->GetIDForIdent("upnp_ServerIP"), $ServerIP);
		SetValue($this->GetIDForIdent("upnp_ServerPort"), $ServerPort);
		SetValue($this->GetIDForIdent("upnp_ServerName"), $friendlyName);
		setvalue($this->GetIDForIdent("upnp_ServerKey"), $key);
		//SetValue(UPNP_Server_ServiceType, $ServerServiceType);
		SetValue($this->GetIDForIdent("upnp_ServerContentDirectory"), $ServerContentDirectory);
		SetValue($this->GetIDForIdent("upnp_ServerIcon"), $ServerIconURL);
		
		return $key;
	}	
	
	/*//////////////////////////////////////////////////////////////////////////////
	Function play()
	...............................................................................
	vorgewählte Playlist abspielen
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	Variable: 	getvalue(	ID_CLIENT_CONTROLURL
							ID_CLIENT_IP
							ID_CLIENT_PORT
							ID_PLAYLIST_XML
							ID_TRACK
							
				setvalue(	ID_MAXTRACK
	--------------------------------------------------------------------------------
	return:  none
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function play(){	
		//IPSLog("start play", "play");
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		$Playlist 	= getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
		
		$xml = new SimpleXMLElement($Playlist);
		$tracks = $xml->count();
		setvalue($this->GetIDForIdent("upnp_NoTracks"),$tracks);
 		$TrackNo = getvalue($this->GetIDForIdent("upnp_Track"))-1;
		$track = ("Track".strval($TrackNo));
			
		$res = $xml->$track->resource; // gibt resource des Titels aus

		$metadata = $xml->$track->metadata; // gibt resource des Titels aus
		//UPNP_GetPositionInfo_Playing abschalten zum Ausführen des Transitioning
		//IPS_SetScriptTimer($this->GetIDForIdent("upnp_PlayInfo"), 0);
		$this->SetTimerInterval('upnp_PlayInfo', 0);
                if ($TrackNo == 1){	
			$this->Stop_AV($ClientIP, $ClientPort, $ControlURL);
		}
		//Transport starten
		$this->SetAVTransportURI($ClientIP, $ClientPort, $ControlURL, (string) $res, (string) $metadata);
		//Stream ausführen	
		$this->Play_AV($ClientIP, $ClientPort, $ControlURL);
		// Postion Timer starten
		//IPS_SetEventActive($this->GetIDForIdent("upnp_PlayInfo"), true);  // Aktivert Ereignis
                $this->SetTimerInterval('upnp_PlayInfo', 1000);
	}

	/*//////////////////////////////////////////////////////////////////////////////
	Function PlayNextTrack()
	...............................................................................
	nächsten Track aus der vorgewählten Playlist abspielen
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	Variable: 	getvalue(	ID_CLIENT_CONTROLURL
							ID_CLIENT_IP
							ID_CLIENT_PORT
							ID_PLAYLIST_XML
							ID_TRACK
				setvalue(	
	--------------------------------------------------------------------------------
	return:  none
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function PlayNextTrack(){	
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		
		$track 		= getvalue($this->GetIDForIdent("upnp_Track"))+1;
		setvalue($this->GetIDForIdent("upnp_Track"),$track);
		$trackNo 	= ("Track".strval($track));
		$Playlist 	= getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		
		$res = $xml->$trackNo->resource; // gibt resource des Titels aus
		$metadata = $xml->$trackNo->metadata; // gibt resource des Titels aus

		$this->SetAVTransportURI($ClientIP, $ClientPort, $ControlURL, (string) $res, (string) $metadata);
		$this->Play_AV($ClientIP, $ClientPort, $ControlURL);
	}


/*//////////////////////////////////////////////////////////////////////////////
--------------------------------------------------------------------------------
Funktion Stop()
--------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////*/
	public function stop()
	{	
		//include_once ("46564 /*[DLNA\Sub Functions\_UPNP_Functions]*/.ips.php"); //UPNP_Functions
		
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
			
		$Playlist = getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		$SelectedFile = GetValue($this->GetIDForIdent("upnp_Track")); 
		$track = ("Track".($SelectedFile));
				
		$DIDL_Lite_Class = $xml->$track->class;
		$this->Stop_AV($ClientIP, $ClientPort, $ControlURL);
		 
		

      	setvalue(self::ID_TRACK, 0);
		
	  	
	  /*Timer abschalten--------------------------------------------------------*/
      $class = $DIDL_Lite_Class;

		if($class == "object.item.audioItem.musicTrack")
		{
			$this->SetTimerInterval('upnp_PlayInfo', 0);
		}

		if($class == "object.item.videoItem")
		{
			//IPS_SetScriptTimer(UPNP_GetPositionInfo_Playing, 0); //GetPositionInfo abschalten
		}

		if($class == "object.item.imageItem.photo")
		{
				//IPS_SetScriptTimer(UPNP_SlideShow, 0); //GetPositionInfo abschalten
		}
	}
	
	
/*//////////////////////////////////////////////////////////////////////////////
--------------------------------------------------------------------------------
Funktion Pause()
--------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////*/
	public function pause()
	{	
 
		
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		
		//setvalue(self::ID_CONTROL_STATUS, 'PAUSE');
		
		$this->Pause_AV($ClientIP, $ClientPort, $ControlURL);
	}


	
/*//////////////////////////////////////////////////////////////////////////////
--------------------------------------------------------------------------------
Funktion Next()
--------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////*/
	public function next()
	{	

		
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		
		$Playlist = getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		//$count = count($xml->children()); 
		//IPSLog("Anzahl XML Elemente : ", $count);
		
		$SelectedFile = GetValue($this->GetIDForIdent("upnp_Track")); 
		
		$track = ("Track".($SelectedFile+1));

		//Aktueller Track = Selected File-----------------------------------------
		SetValue($this->GetIDForIdent("upnp_Track"), ($SelectedFile+1));
		//setvalue(self::ID_CONTROL_STATUS, 'NEXT');
		
		//IPS_SetEventActive(10862 /*[DLNA\Bedienelemente\Control\ControlPanel\Bei Variablenaktualisierung der Variable "DLNA\Bedienelemente\Control"]*/, false);
		//setvalue(self::ID_BUTTON_CONTROL, 1);
		//IPS_SetEventActive(10862 /*[DLNA\Bedienelemente\Control\ControlPanel\Bei Variablenaktualisierung der Variable "DLNA\Bedienelemente\Control"]*/, true);
		
		$this->play('next');	

	}	
	
	
	
/*//////////////////////////////////////////////////////////////////////////////
--------------------------------------------------------------------------------
Funktion Previous()
--------------------------------------------------------------------------------

//////////////////////////////////////////////////////////////////////////////*/
	public function previous()
	{	
		include_once ("46564 /*[DLNA\Sub Functions\_UPNP_Functions]*/.ips.php"); //UPNP_Functions
		
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		
		$Playlist = getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
		$xml = new SimpleXMLElement($Playlist);
		$SelectedFile = GetValue($this->GetIDForIdent("upnp_Track")); 
		$track = ("Track".($SelectedFile-1));

		//Aktueller Track = Selected File-----------------------------------------
		SetValue($this->GetIDForIdent("upnp_Track"), ($SelectedFile-1));
		//setvalue(self::ID_CONTROL_STATUS, 'PREVIOUS');
		
		//IPS_SetEventActive(10862 /*[DLNA\Bedienelemente\Control\ControlPanel\Bei Variablenaktualisierung der Variable "DLNA\Bedienelemente\Control"]*/, false);
		setvalue(self::ID_BUTTON_CONTROL, 1);
		//IPS_SetEventActive(10862 /*[DLNA\Bedienelemente\Control\ControlPanel\Bei Variablenaktualisierung der Variable "DLNA\Bedienelemente\Control"]*/, true);
		
		$this->play('previous');

	}	


	/*//////////////////////////////////////////////////////////////////////////////
	Function loadPlaylist($AlbumNo)
	...............................................................................
	Playlist aus Datei laden (XML) und in Variable Playlist_XML schreiben
	...............................................................................
	Parameter:  $AlbumNo = String '0001'
	--------------------------------------------------------------------------------
	Variable: 	getvalue(	ID_SERVER_NAME
				setvalue(	ID_PLAYLIST_NAME
							ID_PLAYLIST_XML
							ID_TRACK
	--------------------------------------------------------------------------------
	return:  Playlist as XML 
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function loadPlaylist($AlbumNo){	
			//IPSLog("Lade Playlist ", $AlbumNo );
			$Server = getvalue($this->GetIDForIdent("upnp_ServerName"));
			$PlaylistName = $Server.$AlbumNo;
			setvalue($this->GetIDForIdent("upnp_PlaylistName"), $PlaylistName);
			$PlaylistFile = $PlaylistName.'.xml';
	
			$Playlist = file_get_contents($this->Kernel()."media/Multimedia/Playlist/Musik/".$PlaylistFile);
			// Playlist abspeichern
			setvalue($this->GetIDForIdent("upnp_Playlist_XML"), $Playlist);
			// neue Playlist wurde geladen - TrackNo auf 0 zurücksetzen
			setvalue($this->GetIDForIdent("upnp_Track"), 1);
			
			$vars 				= explode(".", $PlaylistFile);
			$PlaylistName 			= $vars[0];
			$PlaylistExtension		= $vars[1];

			$xml = new SimpleXMLElement($Playlist);
			
			return $xml;
	}


	/*//////////////////////////////////////////////////////////////////////////////
	Function GetPosInfo()
	...............................................................................
	Aufruf durch Timer jede Sekunde
	überprüft 'CurrentTransportState' und PositionInfo
	...............................................................................
	Parameter:  none
	--------------------------------------------------------------------------------
	Variable: getvalue(		ID_CLIENT_CONTROLURL
							ID_CLIENT_IP
							ID_CLIENT_RENDER_CONTROL_URL
							ID_PLAYLIST_XML
							ID_TRACK
				setvalue(	ID_TRANSPORT_STATUS
							ID_PROGRESS
							ID_TRACK
							ID_MAXTRACK
	--------------------------------------------------------------------------------
	return:  none
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	public function GetPosInfo(){ 
		//IPAdresse und Port des gewählten Device---------------------------------------
		$ControlURL = getvalue($this->GetIDForIdent("upnp_ClientControlURL"));
		$ClientIP 	= getvalue($this->GetIDForIdent("upnp_ClienIP"));
		$ClientPort = getvalue($this->GetIDForIdent("upnp_ClientPort"));
		$RenderingControlURL = getvalue($this->GetIDForIdent("upnp_ClientRenderingControlURL"));
		
		$fsock = fsockopen($ClientIP, $ClientPort, $errno, $errstr, $timeout = '1');
		if ( !$fsock ){
			//nicht erreichbar --> Timer abschalten--------------------------------
			echo ("$DeviceIP nicht erreichbar !!!");
		}
		else{
			/*///////////////////////////////////////////////////////////////////////////
			Auswertung nach CurrentTransportState "PLAYING" oder "STOPPED"
			bei "PLAYING" -> GetPositionInfo -> Progress wird angezeigt
			bei "STOPPED" -> nächster Titel wird aufgerufen
			/*///////////////////////////////////////////////////////////////////////////
			$Playlist = getvalue($this->GetIDForIdent("upnp_Playlist_XML"));
			$xml = new SimpleXMLElement($Playlist);
			$SelectedFile = GetValue($this->GetIDForIdent("upnp_Track"))-1; 
			$track = ("Track".($SelectedFile));
				
			$DIDL_Lite_Class = $xml->$track->class;
			
			/* Transport Status abfragen */
			$Playing = $this->GetTransportInfo($ClientIP, $ClientPort, $ControlURL);
			//IPSLog('PlayStatus ', $Playing);
 			setvalue($this->GetIDForIdent("upnp_Transport_Status"), $Playing['CurrentTransportState']);
			
			//Transport Status auswerten
			switch ($Playing['CurrentTransportState']){
				case 'NO_MEDIA_PRESENT':
						$this->SetTimerInterval('upnp_PlayInfo', 0);  // DeAktivert Ereignis
						setvalue($this->GetIDForIdent("upnp_Progress"),0);
						setvalue($this->GetIDForIdent("upnp_Track"),0);
				break;
				case 'STOPPED':
					$lastTrack = getvalue($this->GetIDForIdent("upnp_Track"));
					$maxTrack = getvalue($this->GetIDForIdent("upnp_NoTracks"));
					if ($lastTrack > 0  AND $lastTrack < $maxTrack){
						$this->PlayNextTrack();		
					}
					else {
						 
						$this->SetTimerInterval('upnp_PlayInfo', 0);  // DeAktivert Ereignis
						setvalue($this->GetIDForIdent("upnp_Progress"),0);
						setvalue($this->GetIDForIdent("upnp_Track"),0);
					}
				break;
				case 'PLAYING':
					if($DIDL_Lite_Class == "object.item.audioItem.musicTrack"){
						$fortschritt = $this->progress($ClientIP, $ClientPort, $ControlURL);
					}
					if($DIDL_Lite_Class == "object.item.videoItem"){
						//include_once ("35896 /*[Multimedia\Core\UPNP_Progress]*/.ips.php"); //UPNP_Progress
					}
					if($DIDL_Lite_Class == "object.item.imageItem.photo"){
						//include_once ("57444 /*[Multimedia\Core\UPNP_SlideShow]*/.ips.php"); //UPNP_SlideShow
					}			
				break;
			}
		}
	}
	
	
	

	/*//////////////////////////////////////////////////////////////////////////////
	Funktion: progress ($ClientIP, $ClientPort, $ControlURL)
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
	Parameter:  $ClientIP = Client IP auf dem wiedergeben wird
				$ClientPort
				$ControlURL
	--------------------------------------------------------------------------------
	Variable: setvalue(	ID_PROGRESS
						ID_DIDL_ARTIST
						ID_DIDL_TITEL
						ID_DIDL_ALBUM
						ID_DIDL_TRACK
						ID_DIDL_DESCRIPT
						ID_DIDL_DATE
						ID_DIDL_ALBUMARTURI
						ID_DIDL_GENRE
 	--------------------------------------------------------------------------------
	return:  Progress - Integer Wert 0 - 100
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function progress($ClientIP, $ClientPort, $ControlURL){	
		$GetPositionInfo = $this->GetPositionInfo($ClientIP, $ClientPort, $ControlURL);
		//IPSLog('Aktuelle Track Nummer ', $GetPositionInfo);
		$Duration = (string) $GetPositionInfo['TrackDuration']; //Duration
		$RelTime = (string) $GetPositionInfo['RelTime']; //RelTime
		$TrackMeta = (string) $GetPositionInfo['TrackMetaData'];
 		$b = html_entity_decode($TrackMeta);
	 	$didlXml = simplexml_load_string($b); 
		$creator = $didlXml->item[0]->xpath('dc:creator')[0];
		$title = $didlXml->item[0]->xpath('dc:title')[0];
		$album = $didlXml->item[0]->xpath('upnp:album')[0];
		$TrackNo = $didlXml->item[0]->xpath('upnp:originalTrackNumber')[0];
		$description = $didlXml->item[0]->xpath('dc:description')[0];
		$AlbumArtURI = $didlXml->item[0]->xpath('upnp:albumArtURI')[0];
		$genre = $didlXml->item[0]->xpath('upnp:genre')[0];
		$date = $didlXml->item[0]->xpath('dc:date')[0];
		setvalue($this->GetIDForIdent("upnp_Artist"), (string) $creator);
		setvalue($this->GetIDForIdent("upnp_Title"), (string) $title);
		setvalue($this->GetIDForIdent("upnp_Album"), (string) $album);		
		setvalue($this->GetIDForIdent("upnp_TrackNo"), (string) $TrackNo);
		setvalue($this->GetIDForIdent("upnp_Description"), (string) $description);
		setvalue($this->GetIDForIdent("upnp_Date"), (string) $date);
		setvalue($this->GetIDForIdent("upnp_AlbumArtUri"), (string) $AlbumArtURI);
		setvalue($this->GetIDForIdent("upnp_Genre"), (string) $genre);

 
			function get_time_difference($Duration, $RelTime){
				$duration = explode(":", $Duration);
				//print_r ($duration);
				$reltime = explode(":", $RelTime);
				//print_r ($reltime);
				$time_difference = round((((($reltime[0] * 3600) + ($reltime[1] * 60) + ($reltime[2]))* 100) / (($duration[0] * 3600) + ($duration[1] * 60) + ($duration[2]))), 0, PHP_ROUND_HALF_UP);
				return ($time_difference);
			}
			
		if($Duration == "0:00:00"){
			$Duration = (string) $GetPositionInfo[AbsTime]; //AbsTime
		}
		$Progress = get_time_difference($Duration, $RelTime);
		SetValueInteger($this->GetIDForIdent("upnp_Progress"), $Progress);
		return $Progress;
	}


	/*//////////////////////////////////////////////////////////////////////////////
	Funktion: browseContainerServer($ObjectID)
	...............................................................................
	Liest alle Objecte eines Containers/Folders mit ID aus 
	...............................................................................
	Parameter:  $ObjectID
	--------------------------------------------------------------------------------
	Variable:
	--------------------------------------------------------------------------------
	return:  array: [$liste][$i]
			//01: container/item
			//02: id
			//03: refID
			//04: parentid
			//05: restricted
			//06: artist
			//07: album
			//08: title
			//09: resource
			//10: duration
			//11: size
			//12: bitrate
			//13: albumArtURI
			//14: genre
			//15: date
			//16: originalTrackNumber
			//17: class
			//18: extension
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function browseContainerServer($ObjectID){	
		//IPSLog("Starte Funktion: browseServer mit : ",$ObjectID);
		$ServerContentDirectory = GetValue(self::ID_SERVER_CONTENTDIR);
		$ServerIP= GetValue(self::ID_SERVER_IP);
		$ServerPort= GetValue(self::ID_SERVER_PORT);
		
		//Suchvariablen-----------------------------------------------------------------
		$BrowseFlag = "BrowseDirectChildren"; //"BrowseMetadata"; //"BrowseDirectChildren";
		$Filter = "*"; //GetValue();
		$StartingIndex = "0"; //GetValue();
		$RequestedCount = "0"; //GetValue();
		$SortCriteria = ""; //GetValue();
		
		$Kernel = $this->Kernel();

		//Function ContentDirectory_Browse aufrufen-------------------------------------
		$BrowseResult = $this->ContentDirectory_Browse ( $ServerIP, $ServerPort, $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria);
		//print_r ($BrowseResult);
		//IPSLog('BrowseServer Result  ', $BrowseResult);
		sleep(2);

		$Result_xml = $BrowseResult['Result'] ;
		$NumberReturned = $BrowseResult['NumberReturned'];
		$TotalMatches = $BrowseResult['TotalMatches'];
		$UpdateID = $BrowseResult['UpdateID'];
				
		if ($NumberReturned == $TotalMatches){
			if ($NumberReturned == "0"){
				$liste[0]['title']="leer";
				$liste[0]['id']="0";
				$liste[0]['artist'] = "";
				$liste[0]['resource'] = "";
				$liste[0]['parentid'] = "";
				$liste[0]['albumArtURI'] = "";
				}
			else{
				// Result mit gefundenden media files bearbeiten 
				$liste = $this->BrowseList($Result_xml);
				//print_r ($liste); //Testanzeige
				}
			  }
		//wenn nur Teilrückgabe, dann mehrfach auslesen	  
		if ($NumberReturned <= $TotalMatches) {
			$liste = $this->BrowseList($Result_xml);
			//IPSLog("TotalMatches ",$TotalMatches);
			for($i = 0; $NumberReturned*$i < $TotalMatches; ++$i){
				$StartingIndex = $NumberReturned*$i;
				//IPSLog("StartingIndex ",$StartingIndex);
				$BrowseArray_add =  $this->ContentDirectory_Browse ( $ServerIP, $ServerPort, $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria);
				$BrowseResult_add = $BrowseArray_add['Result'];
				$liste_add = $this->BrowseList($BrowseResult_add);
				$liste = array_merge($liste, $liste_add);
			}
		}
		return $liste;
	}




	/*//////////////////////////////////////////////////////////////////////////////
	Funktion: getContainerServer($Mediatype)
	...............................................................................
	Alle Container/Folder eines Servers ab Stammverzeichnis $Mediatype oder ab Filter auslesen 
	...............................................................................
	Parameter:  $Mediatype = 'Musik' - 'Audiobook' - Foto' - 'Video'
	--------------------------------------------------------------------------------
	Variable:
	--------------------------------------------------------------------------------
	return:  array: [$container]
					['class']  
					['id']  
					['title']  
	--------------------------------------------------------------------------------
	Status:  
	//////////////////////////////////////////////////////////////////////////////*/
	Public function getContainerServer($Mediatype){
		//IPSLog("Starte Funktion: browseServer mit : ",$Mediatype);
		$ServerContentDirectory = GetValue(self::ID_SERVER_CONTENTDIR);
		$ServerIP= GetValue(self::ID_SERVER_IP);
		$ServerPort = GetValue(self::ID_SERVER_PORT);
		$ServerName = GetValue(self::ID_SERVER_NAME);

		//Suchvariablen-----------------------------------------------------------------
		$BrowseFlag = "BrowseDirectChildren"; //"BrowseMetadata"; //"BrowseDirectChildren";
		$Filter = "*"; //GetValue();
		$StartingIndex = 0; //GetValue();
		$RequestedCount = "0"; //GetValue();
		$SortCriteria = ""; //GetValue();

		$Kernel = $this->Kernel();
		 
		$container[0]['id'] = '0';
		$n = 0;
		$i = 0;
		$SI = 0;
		// Server spezifische Filter = STammverzeichnis
		if($ServerName == "Plex"){
			$AuswahlA = "By Folder";
			$AuswahlB = "Music";
		} 
		if($ServerName == "AVM"){
			$AuswahlA = "Ordner";
			$AuswahlB = "Musik";
		} 
		for($n = 0; $n <= $i; ++$n){	
			$ObjectID = $container[$n]['id'];	
			//IPSLog('ContentDirectory_Browse mit Object ID mit '.$n." - ", $ObjectID);
			//Function ContentDirectory_Browse aufrufen-------------------------------------
			$BrowseResult = $this->ContentDirectory_Browse ($ServerIP, $ServerPort, $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria);
			$Result_xml = $BrowseResult['Result'] ;
			$NumberReturned = intval($BrowseResult['NumberReturned']);
			$TotalMatches = intval($BrowseResult['TotalMatches']);
			//IPSLog("NumberReturned", $NumberReturned);	
			//IPSLog("Total Matches", $TotalMatches);	
			if ($NumberReturned == $TotalMatches){
				$liste = $this->BrowseList($Result_xml);
				foreach ($liste as $value) {
					if($value['typ'] == 'container'){
						if(($value['title'] == $AuswahlB) or ($value['title'] == "My".$Mediatype) or ($value['title'] == $AuswahlA)){
							$i = 0;
							$n = 0;
							unset($container);
						}	
							$i = $i + 1;
							$container[$i]['class'] = $value['typ'];
							$container[$i]['id'] = $value['id'];
							$container[$i]['title'] = $value['title'];	
							//IPSLog('gefundener Container Titel mit neuer array ID -'. $value['id']." - ",$container[$i]['title']);		
					}

				}
			}
			//wenn nur Teilrückgabe, dann mehrfach auslesen
			elseif ($NumberReturned == 0){
				//IPSLog("Wert ist Null", $NumberReturned );
			}
			else if ($NumberReturned < $TotalMatches){
				//$SI = 0;	
				$StartingIndex = 0;
				//IPSLog("Teilrückgabe - Anzahl zurueckgegebener Datensätze", $NumberReturned);
				//IPSLog("Teilrückgabe - Anzahl TotalMatches Datensätze", $TotalMatches);
				if ($NumberReturned > 0){
					for($SI = 0; $NumberReturned*$SI < $TotalMatches; ++$SI){
						$StartingIndex = $NumberReturned*$SI;
						IPSLog('StartIndex', $StartingIndex );

						$BrowseResult = $this->ContentDirectory_Browse ($ServerIP, $ServerPort, $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria);
						$Result_xml = $BrowseResult['Result'] ;
						$liste = $this->BrowseList($Result_xml);

						foreach ($liste as $value) {
							if($value['typ'] == 'container'){
								$i = $i + 1;
								$container[$i]['class'] = $value['typ'];
								$container[$i]['id'] = $value['id'];
								$container[$i]['title'] = $value['title'];	
								//IPSLog('gefundener Teil-Container Titel - '.$i." - ",$container[$i]['title']);			
							}
						}
					}
				}
			}
		}	

		//Serialize the array.
		$serialized = serialize($container);
		//Save the serialized array to a text file.
		file_put_contents($Kernel."media/Multimedia/Playlist/Musik/".$ServerName."_Musik_Container.con", $serialized);
		
		//Retrieve the serialized string.
		//$fileContents = file_get_contents('serialized.txt');
		//Unserialize the string back into an array.
		//$arrayUnserialized = unserialize($fileContents);
		return $container;
	}


	/*//////////////////////////////////////////////////////////////////////////////
	Function BrowseList($Result) --> $Results in Arrays ausgeben
	...............................................................................
	Alle Container/Folder eines Servers ab Stammverzeichnis $Mediatype oder ab Filter auslesen 
	...............................................................................
	Parameter:  $Mediatype = 'Musik' - 'Audiobook' - Foto' - 'Video'
	--------------------------------------------------------------------------------
	Variable:
	--------------------------------------------------------------------------------
	return:  array: [$liste]
			//01: container/item
			//02: id
			//03: refID
			//04: parentid
			//05: restricted
			//06: artist
			//07: album
			//08: title
			//09: resource
			//10: duration
			//11: size
			//12: bitrate
			//13: albumArtURI
			//14: genre
			//15: date
			//16: originalTrackNumber
			//17: class
			//18: extension
	--------------------------------------------------------------------------------
	Status: 
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function BrowseList($Result_xml){
		$xml = simplexml_load_string($Result_xml); //
		//IPSLog("Starte Funktion  - Browse Liste /r/n","..");
		//IPSLog("XML Array = ", $Result);
		$skip = false;
		$liste = array();
		
		for($i=0,$size=count($xml);$i<$size;$i++)
		
		/*///////////////////////////////////////////////////////////////////////////
		///////////////////////Ereignisbaum container oder item//////////////////////
		///////////////////////////////////////////////////////////////////////////*/
		{
			if(isset($xml->container[$i])) //Container vorhanden also Verzeichnis
		      {
				$node = $xml->container[$i];
				$attribut = $xml->container[$i]->attributes();
				$liste[$i]['typ'] = "container";
				//print_r ($liste[$i]['typ']);
			  }
			else if(isset($xml->item[$i])) //Item vorhanden also item (Musik, Bild, Video)
				{
					$node = $xml->item[$i];
					$attribut = $xml->item[$i]->attributes();
					$liste[$i]['typ'] = "item";
			
					//MetaData für jeden Titel zusammenstellen--------------------------------
					$metadata_header 			= '&lt;DIDL-Lite xmlns="urn:schemas-upnp-org:metadata-1-0/DIDL-Lite/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:upnp="urn:schemas-upnp-org:metadata-1-0/upnp/" xmlns:dlna="urn:schemas-dlna-org:metadata-1-0/"&gt;';
					$raw_metadata_string 	= $xml->item[$i]->asxml();
					$metadata_string 			= str_replace(array("<", ">"), array("&lt;", "&gt;"), $raw_metadata_string);
					$metadata_close  			= '&lt;/DIDL-Lite&gt;';
					$metadata					= ("$metadata_header"."$metadata_string"."$metadata_close");
			
					$liste[$i]['metadata']	= $metadata;
					//IPSLog("Item der Liste zugefügt = ", $liste[$i]['metadata']);
		
				}
			else
				{
					$skip = true;
					//IPSLog("nicht lesbar !!!" , $i); //Fehler aufgetreten
					//IPSLog('Container ', $xml->container[$i]);
					//IPSLog('Item ', $xml->item[$i]);
					//IPSLog('XML ', $xml);
		   			//return;
				}
				//////////////////////////////Ende Ereignisbaum//////////////////////////////
			if ($skip == false){
				/*//////////////////////////////////////////////////////////////////////////////
				//////////////////////////////////////////////////////////////////////////////*/
				if(isset($attribut['id']) && !empty($attribut['id'])) {
						$id = $attribut['id'];
						$liste[$i]['id']=(string)$id;
					}else{
						$liste[$i]['id']="leer";
						}
				if(isset($attribut['refID']) && !empty($attribut['refID'])) {
						$refID = $attribut['refID'];
						$liste[$i]['refid']=(string)$refID;
					}else{
						$liste[$i]['refid']="leer";
						}
				if(isset($attribut['parentID']) && !empty($attribut['parentID'])) {
				      $parentID = $attribut['parentID'];
						$liste[$i]['parentid']=(string)$parentID;
					}else{
						$liste[$i]['parentid']="leer";
						}
				if(isset($attribut['restricted']) && !empty($attribut['restricted'])) {
						$restricted = $attribut['restricted'];
						$liste[$i]['restricted']=(string)$restricted;
					}else{
						$liste[$i]['restricted']="leer";
						}
				if($node->xpath("dc:creator")) {
						$interpret = $node->xpath("dc:creator");
						$liste[$i]['artist']=utf8_decode((string)$interpret[0]);
					}else{
						$liste[$i]['artist']="leer";
						}
				if($node->xpath("upnp:album")) {
						$album = $node->xpath("upnp:album");
						$liste[$i]['album']=utf8_decode((string)$album[0]);
					}else{
						$liste[$i]['album']="leer";
						}
				if($node->xpath("dc:title")) {
						$titel = $node->xpath("dc:title");
						$liste[$i]['title']=utf8_decode((string)$titel[0]);
					}else{
						$liste[$i]['title']="leer";
						}
				if($node->xpath("upnp:albumArtURI")) {
						$albumart = $node->xpath("upnp:albumArtURI");
						$liste[$i]['albumArtURI']=(string)$albumart[0];
					}else{
						$liste[$i]['albumArtURI'] ="leer";
						}
				if($node->xpath("upnp:genre")) {
						$genre = $node->xpath("upnp:genre");
						$liste[$i]['genre']=utf8_decode((string)$genre[0]);
					}else{
						$liste[$i]['genre']="leer";
						}
				if($node->xpath("dc:date")) {
						$date = $node->xpath("dc:date");
						$liste[$i]['date']=(string)$date[0];
					}else{
						$liste[$i]['date']="leer";
						}
				if($node->xpath("upnp:originalTrackNumber")) {
						$originalTrackNumber = $node->xpath("upnp:originalTrackNumber");
						$liste[$i]['originalTrackNumber']=(string) $originalTrackNumber[0];
					}else{
						$liste[$i]['originalTrackNumber']="leer";
						}
				if($node->xpath("upnp:class")) {
						$class = $node->xpath("upnp:class");
						$liste[$i]['class']=(string)$class[0];
					}else{
						$liste[$i]['class']="leer";
						}
				//der einzige Node ohne Namespace !
				if(isset($node->res)){
					$res = $node->res;
					$liste[$i]['resource'] = (string)$res[0];
					$resattribut = $res[0]->attributes();;
					}
				else {
					$liste[$i]['resource'] = "leer";
				}
				//Resource-Attribute auslesen---------------------------------------------------
				if(isset($resattribut['duration'])) {
					$liste[$i]['duration']=(string)$resattribut['duration'];
				}else{
					$liste[$i]['duration']="leer";
					}
				if(isset($resattribut['size'])) {
					$liste[$i]['size']=(string)$resattribut['size'];
				}else{
					$liste[$i]['size']="leer";
					}
				if(isset($resattribut['bitrate'])) {
					$liste[$i]['bitrate']=(string)$resattribut['bitrate'];
				}else{
					$liste[$i]['bitrate']="leer";
					}
			}
			$skip=false;
		}	
		//IPSLog('Ergebnis als Liste Array ', $liste);
		return ($liste); //Rückgabe
	}





        /*//////////////////////////////////////////////////////////////////////////////
        Funktion createPlaylist($id, $PlaylistNo)
        ...............................................................................
        Erzeugt eine Playliste aus dem Container mit der ID und 
        bennent sie nach Servername + PlaylistNo
        "AVM0001.xml"
        ...............................................................................
        Parameter:  $id = Container ID
                                PlaylistNo = "0001"
        --------------------------------------------------------------------------------
        Variable:
        --------------------------------------------------------------------------------
        return:  schreibt FILE
                        $Kernel."media/Multimedia/Playlist/Musik/".$PlaylistName.".xml"]  
        --------------------------------------------------------------------------------
        Status: 
        //////////////////////////////////////////////////////////////////////////////*/
        Protected function createPlaylist($id, $PlaylistNo){
                //IPSLog("Starte Funktion CREATEPLAYLIST mit Parameter ", $id.' - '.$PlaylistNo);
                $PlaylistArray = array();


                //es wird der angewählte Server durchsucht
                $ServerName = getvalue(self::ID_SERVER_NAME);
                //IPSLog('ServerName', $ServerName);
                //------------------------------------------------
                // alle media files in Ordner mit ID  = $id suchen
                //------------------------------------------------
                $result = $this->browseContainerServer($id);

                //Browse als XML-Datei zwischenspeichern

                //Numerische Keys durch Track[Nr.] ersetzen-------------------------------------
                $prefix = "Track";
                $BrowselistArray = $this->rekey_array( $result , $prefix );
                //print_r ($BrowselistArray);

                $xml = new SimpleXMLElement('<Playlist/>');
                $xml = Array2XML::createXML('Playlist' , $BrowselistArray);
                $Playlist = $xml->saveXML();

                $Kernel = $this->Kernel();
                $PlaylistName = $ServerName.$PlaylistNo;
                //XML-Datei in D:/IP-Symcon/webfront/user/Multimedia/Browse/Browse schreiben
                $handle = fopen($Kernel."media/Multimedia/Playlist/Musik/".$PlaylistName.".xml", "w");
                fwrite($handle, $Playlist);
                fclose($handle);

        }



        /*//////////////////////////////////////////////////////////////////////////////
        Funktion createAllPlaylist($mediatype)
        ...............................................................................
        Erzeugt alle Playlisten vom Typ Mediatype
        bennent sie nach Servername + PlaylistNo + .xml
        ...............................................................................
        Parameter:  $mediatype = "Musik"
        --------------------------------------------------------------------------------
        Variable:
        --------------------------------------------------------------------------------
        return:  schreibt FILES (Playlisten)
                        $Kernel."media/Multimedia/Playlist/Musik/".$ServerName."PlaylistNo".xml
        --------------------------------------------------------------------------------
        Status: 
        //////////////////////////////////////////////////////////////////////////////*/
        Public function createAllPlaylist($mediatype){
                $ServerName = getvalue(self::ID_SERVER_NAME);
                if ($mediatype == 'Fotos'){
                        $DB_Fotos_Compressed = getvalue(45521 /*[DLNA\Medienbibliothek\DB_Fotos]*/);
                        $DB_Fotos = unserialize($DB_Fotos_Compressed);
                        foreach($DB_Fotos as $Foto){
                                $id = $Foto['ID_Plex'];
                                $PlaylistNo = $Foto['No'];
                                $this->createPlaylist($id, $PlaylistNo);
                        }
                }
                if ($mediatype == 'Musik'){
                        //Retrieve the serialized string.
                        $Kernel = $this->Kernel();
                        $fileContents = file_get_contents($Kernel."media/Multimedia/Playlist/Musik/".$ServerName."_Musik_Container.con");
                        //Unserialize the string back into an array.
                        $MusikContainer = unserialize($fileContents);
                        foreach ($MusikContainer as $key => $value) {
                                $id = $value['id'];		
                                $PlaylistNo = substr($value['title'],0,4);
                                $this->createPlaylist($id, $PlaylistNo);
                        }
                }
        }










        /*//////////////////////////////////////////////////////////////////////////////
        UPNP_Function_Tools.ips.php V1.2                          2015 by André Liebmann
        21.07.2015
        --------------------------------------------------------------------------------
        Funktionen - Sammlung / Tools
        /*//////////////////////////////////////////////////////////////////////////////

        /*//////////////////////////////////////////////////////////////////////////////
        function Meldung($string)
        Anzeige im Meldungsfenster (Textbox) ObjectID 11701
        /*//////////////////////////////////////////////////////////////////////////////

        Public function Meldung($string)
        {
        //SetValue(self::ID_MELDUNGEN, GetValue(self::ID_MELDUNGEN)."$string\r\n\r\n");
        //IPSLog('Meldung ', $string);
        }

        /*//////////////////////////////////////////////////////////////////////////////
        function rekey_array($input, $prefix)
        umbenennen der im Array enthaltenen numerischen Keys mit einem Präfix
        /*//////////////////////////////////////////////////////////////////////////////

        function rekey_array($input, $prefix)
                {
                $out = array();
                foreach($input as $i => $v)
                        {
                        if(is_numeric($i))
                                {
                                $out[$prefix . $i] = $v;
                                continue;
                                }
                        $out[$i] = $v;
                        }
                        return $out;
                }

        /*//////////////////////////////////////////////////////////////////////////////
        function ping($IP, $Port, $timeout)
        /*//////////////////////////////////////////////////////////////////////////////

        Public function ping($IP, $Port, $timeout)
                {
                $fsock = @fsockopen($IP, $Port, $errno, $errstr, $timeout);

                //socket_set_timeout($fsock, $timeout);

                if ( ! $fsock )
                        {
                        $this->Meldung($IP.": nicht erreichbar\r\n\r\n");
                        return ("false");
                        }
                else
                        {
                        $this->Meldung($IP.": erreichbar\r\n\r\n");
                        return ("true");
                        }
                }

        /*//////////////////////////////////////////////////////////////////////////////
        function search_exclude_value($array, $key, $value)
        ein mehrdimensionales Array wird durchsucht nach allen Subarrays welche nicht
        den Wert ($value) enthalten
        $array -> das mehrdimensionale Array
        $key   -> in welchen Key
        $value -> auszuschliessender Wert
        Rückgabe eines Array, welches nur die Subarrays enthält, welche nicht den Wert
        (z.B. '', also leer)  enthalten
        /*//////////////////////////////////////////////////////////////////////////////

        Public function search_exclude_value($array, $key, $value){
            $results = array();
            if (is_array($array)){
                if (isset($array[$key]) && $array[$key] !== $value){
                    $results[] = $array;
                    foreach ($array as $subarray){
                        $results = array_merge($results, search_exclude_value($subarray, $key, $value));
                    }
                }
            }
            else{
                // Array empty - search clients and Server
                $this->SendDebug("Array empty", "Starte Suchlauf für Clients und/oder Server", 0);
            }
            return $results;
        }

        
        /*//////////////////////////////////////////////////////////////////////////////
        function search_key($which_key, $which_value, $array)
        ...............................................................................
        den $key des Elternelementes in einem mehrdimensionalen Array finden
        ...............................................................................
        Parameter:  $which_key =    = zu durchsuchedes ArrayFeld = ['FriendlyName']
                    $which_value    = Suchwert = z.Bsp "CEOL"
                    $array          = zu durchsuchendes Array
        --------------------------------------------------------------------------------
        Variable:
        --------------------------------------------------------------------------------
        return:  key = gefundener Datensatz index
        --------------------------------------------------------------------------------
        Status:  checked 11.6.2018
        //////////////////////////////////////////////////////////////////////////////*/
        Public function search_key($which_key, $which_value, $array){
            foreach ($array as $key => $value){
                if($value[$which_key] === $which_value){
                    return $key;
                }
                else{
                    //echo("$which_value in Key: $key not found\r\n");
                }
            }
        }

        /* Pfad von IPS
**********************************************/
        Protected function Kernel(){ 
            $Kernel = str_replace("\\", "/", IPS_GetKernelDir());
            return $Kernel;
        }
	
}
