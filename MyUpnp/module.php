<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of module
 *
 * @author Torsten
 */
class MyUpnp extends IPSModule {
    
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
        // Category anlegen
        // Anlegen einer neuen Kategorie 
        $KategorieID = @IPS_GetCategoryIDByName("DIDL", $this->InstanceID);
        if ($KategorieID === false){
            $CatID = IPS_CreateCategory();       // Kategorie anlegen
            IPS_SetName($CatID, "DIDL"); // Kategorie benennen
            IPS_SetParent($CatID, $this->InstanceID); // Kategorie einsortieren unter dem Objekt mit der ID "12345"    
        }
        $KategorieID = @IPS_GetCategoryIDByName("PositionInfo", $this->InstanceID);
        if ($KategorieID === false){        
            $CatID = IPS_CreateCategory();       // Kategorie anlegen
            IPS_SetName($CatID, "PositionInfo"); // Kategorie benennen
            IPS_SetParent($CatID, $this->InstanceID); 
        }
        // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            //$this->RegisterPropertyBoolean("active", false);
            //$this->RegisterPropertyString("IPAddress", "");
            //$this->RegisterPropertyInteger("UpdateInterval", 30);
           
        //Status Variable anlegen
        //if($this->GetIDForIdent("upnp_Artist" == false){
        $this->RegisterVariableString("upnp_Artist", "Artist [dc:creator]");
        $this->RegisterVariableString("upnp_Album", "Album [upnp:album]");
        $this->RegisterVariableString("upnp_Title", "Titel [dc:title]");
        $this->RegisterVariableString("upnp_Description", "Description [dc:description]");
        $this->RegisterVariableString("upnp_AlbumArtUri", "AlbumArtURI [upnp:albumArtURI]");
        $this->RegisterVariableString("upnp_Genre", "Genre [upnp:genre]");
        $this->RegisterVariableString("upnp_Date", "Date [dc:date]");
        $this->RegisterVariableString("upnp_TrackNo", "TrackNumber [upnp:originalTrackNumber]");
        $ID_CatDIDL =  IPS_GetCategoryIDByName("DIDL", $this->InstanceID);
        //Verschieben der Variable unter Ordner DIDL
        IPS_SetParent($this->GetIDForIdent("upnp_Artist"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_Album"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_Title"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_Description"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_AlbumArtUri"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_Genre"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_Date"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("upnp_TrackNo"), $ID_CatDIDL);
        
        $this->RegisterVariableInteger("upnp_Progress", "Progress", "UPNP_Progress");
        $this->RegisterVariableInteger("upnp_Track", "Track", "");
        $this->RegisterVariableString("upnp_Transport_Status", "Transport_Status");
        $this->RegisterVariableString("upnp_TrackDuration", "TrackDuration [upnp:album]");
        $this->RegisterVariableString("upnp_TrackMetaData", "TrackMetaData");
        $this->RegisterVariableString("upnp_TrackURI", "TrackURI");
        $this->RegisterVariableString("upnp_RelTime", "RelTime");
        $this->RegisterVariableString("upnp_AbsTime", "GAbsTime");
        $this->RegisterVariableString("upnp_RelCount", "RelCount");
        $this->RegisterVariableString("upnp_AbsCount", "AbsCount");
        $ID_PosInfo =  IPS_GetCategoryIDByName("PositionInfo", $this->InstanceID);
        //Verschieben der Variable unter Ordner PositionInfo
        IPS_SetParent($this->GetIDForIdent("upnp_Progress"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_Track"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_Transport_Status"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_TrackDuration"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_TrackMetaData"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_TrackURI"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_RelTime"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_AbsTime"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_RelCount"), $ID_PosInfo);
        IPS_SetParent($this->GetIDForIdent("upnp_AbsCount"), $ID_PosInfo);
        
        $this->RegisterVariableString("upnp_ClientArray", "Client:Array");
        $this->RegisterVariableString("upnp_ClientControlURL", "Client:ControlURL");
        $this->RegisterVariableString("upnp_ClientIcon", "Client:Icon");
        $this->RegisterVariableString("upnp_ClienIP", "Client:IP");  
        $this->RegisterVariableInteger("upnp_ClientKey", "Client:Key", "");
        $this->RegisterVariableString("upnp_ClientName", "Client:Name");
        $this->RegisterVariableString("upnp_ClientPort", "Client:Port");
        $this->RegisterVariableString("upnp_ClientRenderingControlURL", "Client:RenderingControlURL");
        
        $this->RegisterVariableString("upnp_ServerArray", "Server:Array");
        $this->RegisterVariableString("upnp_ServerArray", "Server:ContentDirectory");
        $this->RegisterVariableString("upnp_ServerArray", "Server:Icon");
        $this->RegisterVariableString("upnp_ServerArray", "Server:IP");
        $this->RegisterVariableInteger("upnp_ServerKey", "Server:Key", "");
        $this->RegisterVariableString("upnp_ServerName", "Server:Name");
        $this->RegisterVariableString("upnp_ServerPort", "Server:Port");
        
        $this->RegisterVariableInteger("upnp_NoTracks", "No of tracks", "");
        $this->RegisterVariableString("upnp_PlaylistName", "PlaylistName");
        $this->RegisterVariableString("upnp_Playlist_XML", "Playlist_XML");       
        
        
            //$this->RegisterVariableBoolean("CeolPower", "Power");        
         
            
        // Timer erstellen
        $this->RegisterTimer("upnp_PlayInfo", 1000, '$this->GetPosInfo');
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
        
       
}
