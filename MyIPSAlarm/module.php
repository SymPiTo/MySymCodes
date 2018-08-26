<?
/** ============================================================================
 * Title: Alarm for MyIPS
 * author PiTo
 * 
 * GITHUB = <https://github.com/SymPiTo/MySymCodes/tree/master/MyIPSAlarm>
 * 
 * Version:1.0.2018.08.25
 =============================================================================== */
//Class: MyAlarm
class MyAlarm extends IPSModule
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
 
         // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
         // Aufruf dieser Form Variable mit  §this->ReadPropertyFloat(-IDENTNAME-)
        //$this->RegisterPropertyInteger(!IDENTNAME!, 0);
        //$this->RegisterPropertyFloat(!IDENTNAME!, 0.5);
        //$this->RegisterPropertyBoolean(!IDENTNAME!, false);
        
        //Listen Einträge als JSON regisrieren
        // zum umwandeln in ein Array 
        // $sensors = json_decode($this->ReadPropertyString("Battery"));
            $this->RegisterPropertyString("Battery", "[]");
            $this->RegisterPropertyString("Targets", "[]");
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string §Ident, string §Name, string §Profil, integer §Position )
        // Aufruf dieser Variable mit §this->GetIDForIdent(!IDENTNAME!)
        //$this->RegisterVariableInteger(!FSSC_Position!, !Position!, !Rollo.Position!);
      
        //Boolean Variable anlegen
        //integer RegisterVariableBoolean ( string §Ident, string §Name, string §Profil, integer §Position )
        // Aufruf dieser Variable mit §this->GetIDForIdent(!IDENTNAME!)
        //$this->RegisterVariableBoolean(!FSSC_Mode!, !Mode!);
        
        //String Variable anlegen
        //RegisterVariableString (  §Ident,  §Name, §Profil, §Position )
         // Aufruf dieser Variable mit §this->GetIDForIdent(!IDENTNAME!)
         //$this->RegisterVariableString("SZ_MoFr", "SchaltZeiten Mo-Fr");
 
          
            
            
            
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        
        //§this->EnableAction(-IDENTNAME-);
        //IPS_SetVariableCustomProfile(§this->GetIDForIdent(!Mode!), !Rollo.Mode!);
        
        //anlegen eines Timers
        //$this->RegisterTimer(!TimerName!, 0, !FSSC_reset(\§_IPS[!TARGET!>]);!);
            
        $alleEreignisse = IPS_GetEventList();
        foreach ($alleEreignisse as $EreignisID) {
            IPS_DeleteEvent($EreignisID);
        }
        
    }
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
        
        //Unterkategorie anlegen
        $AlarmCatID = $this->RegisterCategory("AlarmEvents");
        // für jedes Liste ID ein Event anlegen
        $batteries = json_decode($this->ReadPropertyString("Battery"));
        foreach($batteries as $sensor) {
            $ParentID = $AlarmCatID;
            $Typ = 0;
            $Ident = "AE".$sensor->ID;
            $Name = "AEvent".$sensor->ID;
            $this->RegisterVarEvent($Name, $Ident, $Typ, $ParentID, 0, 1, $sensor->ID);
        }       
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
        /*
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
        */
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
    public function test(){
       $Batteries = $this->ReadPropertyString("Battery");
       
        
    }  


   /* _______________________________________________________________________
    * Section: Private Funtions
    * Die folgenden Funktionen sind nur zur internen Verwendung verfügbar
    *   Hilfsfunktionen
    * _______________________________________________________________________
    */  


		
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
    private function RegisterVarEvent($Name, $Ident, $Typ, $ParentID, $Position, $trigger, $var)
    {
            $eid =  @IPS_GetEventIDByName($Name, $ParentID);
            if($eid === false) {
                //we need to create one
                $EventID = IPS_CreateEvent($Typ);
                IPS_SetParent($EventID, $ParentID);
                @IPS_SetIdent($EventID, $Ident);
                IPS_SetName($EventID, $Name);
                IPS_SetPosition($EventID, $Position);
                IPS_SetEventTrigger($EventID, $trigger, $var);   //OnUpdate für Variable 12345
                IPS_SetEventActive($EventID, false);
            } 
            else{
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

    /* ----------------------------------------------------------------------------------------------------- 
    Function: RegisterCategory
    ...............................................................................
     *  Legt ein Unterverzeichnis an
     * Beispiel:
     *  
    ...............................................................................
    Parameters: 
 
    .......................................................................................................
    Returns:    
        none
    -------------------------------------------------------------------------------------------------------- */
    private function RegisterCategory($catName ) {
        $KategorieID = @IPS_GetCategoryIDByName($catName, $this->InstanceID);
        if ($KategorieID === false){
            // Anlegen einer neuen Kategorie mit dem Namen $catName
            $CatID = IPS_CreateCategory();       // Kategorie anlegen
            IPS_SetName($CatID, $catName); // Kategorie benennen

            IPS_SetParent($CatID, $this->InstanceID); // Kategorie einsortieren unterhalb der der Instanz
        }
        return $KategorieID;
    }

		
}