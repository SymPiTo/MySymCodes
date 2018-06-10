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
        
        $CatID = IPS_CreateCategory();       // Kategorie anlegen
        IPS_SetName($CatID, "PositionInfo"); // Kategorie benennen
        IPS_SetParent($CatID, $this->InstanceID); 
      
        // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            //$this->RegisterPropertyBoolean("active", false);
            //$this->RegisterPropertyString("IPAddress", "");
            //$this->RegisterPropertyInteger("UpdateInterval", 30);
           
        //Status Variable anlegen
        //$this->RegisterVariableInteger("Track", "TrackNumber [upnp:originalTrackNumber]", "");
            //$this->RegisterVariableBoolean("CeolPower", "Power");
        $this->RegisterVariableString("Artist", "Artist [dc:creator]");
        $this->RegisterVariableString("Album", "Album [upnp:album]");
        $this->RegisterVariableString("Title", "Titel [dc:title]");
        $this->RegisterVariableString("Description", "Description [dc:description]");
        $this->RegisterVariableString("AlbumArtUri", "AlbumArtURI [upnp:albumArtURI]");
        $this->RegisterVariableString("Genre", "Genre [upnp:genre]");
        $this->RegisterVariableString("Date", "Date [dc:date]");
        $this->RegisterVariableString("Track", "TrackNumber [upnp:originalTrackNumber]");
        $ID_CatDIDL =  IPS_GetCategoryIDByName("DIDL", $this->InstanceID);
        //Verschieben der Variable unter Ordner DIDL
        IPS_SetParent($this->GetIDForIdent("Album"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Title"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Description"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("AlbumArtUri"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Genre"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Artist"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Date"), $ID_CatDIDL);
        IPS_SetParent($this->GetIDForIdent("Track"), $ID_CatDIDL);
        
        
        
            //$this->RegisterVariableInteger("CeolFavChannel", "FavChannel", "");
            
        // Timer erstellen
            //$this->RegisterTimer("Update", $this->ReadPropertyInteger("UpdateInterval"), 'CEOL_update($_IPS[\'TARGET\']);');
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
