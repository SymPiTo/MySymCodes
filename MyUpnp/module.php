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
            //IPS_SetParent($CatID, 12345); // Kategorie einsortieren unter dem Objekt mit der ID "12345"    
        }
        
        $CatID = IPS_CreateCategory();       // Kategorie anlegen
        IPS_SetName($CatID, "PositionInfo"); // Kategorie benennen
        // 
        // 
        // // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
            //$this->RegisterPropertyBoolean("active", false);
            //$this->RegisterPropertyString("IPAddress", "");
            //$this->RegisterPropertyInteger("UpdateInterval", 30);
           
        //Status Variable anlegen
            //$this->RegisterVariableInteger("CeolSource", "Source", "");
            //$this->RegisterVariableBoolean("CeolPower", "Power");
;
            //$this->RegisterVariableString("CeolSZ1", "Line1");

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
