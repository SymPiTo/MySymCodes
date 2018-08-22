<?php
/**
 * Title: FS20 RSU Shutter Control
 * 
 *
 * author PiTo
 * 
 * GITHUB = <https://github.com/SymPiTo/MySymCodes/tree/master/MyFS20_SC>
 * 
 * Version:1.0.2018.08.21
 */
//Class: MyFS20_SC
class MyFS20_SC extends IPSModule
{
    /* Section: Internal Modul Funtions
     Die folgenden Funktionen sind Standard Funktionen zur Modul Erstellung.
    */
    //*****************************************************************************
    /* Function: Create  
    Create() wird einmalig beim Erstellen einer neuen Instanz und 
    neu laden der Modulesausgeführt. Vorhandene Variable werden nicht veändert, auch nicht 
    eingetragene Werte (Properties).
    Überschreibt die interne IPS_Create($id)  Funktion
   
     CONFIG-VARIABLE:
      FS20RSU_ID   -   ID des FS20RSU Modules (selektierbar).
      Time_OU      -   Zeit von Oben bis unten in Sekunden
      Time_UO      -   Zeit von Unten bis oben in Sekunden
      Time_OM      -   Zeit von Oben bis Mitte in Sekunden
      Time_UM      -   Zeit von Unten bis Mitte in Sekunden
      SunRise      -   Schalter um SunRise Event zu aktivieren
     
    STANDARD-AKTIONEN:
      FSSC_Position    -   Position (integer)
      UpDown           -   up/Down  (bool)
      Mode             -   Automatik/Manual (bool)
    */
    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
	//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.}
        
        // Variable aus dem Instanz Formular registrieren (zugänglich zu machen)
        // Aufruf dieser Form Variable mit $Tup = $this->ReadPropertyFloat('IDENTNAME'); 
        $this->RegisterPropertyInteger("FS20RSU_ID", 0);
        $this->RegisterPropertyFloat("Time_OU", 0.5);
        $this->RegisterPropertyFloat("Time_UO", 0.5);
        $this->RegisterPropertyFloat("Time_OM", 0.5);
        $this->RegisterPropertyFloat("Time_UM", 0.5);
        $this->RegisterPropertyBoolean("SunRise", false);
        
            
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit "getvalue($this->GetIDForIdent("IDENTNAME"))"
        $this->RegisterVariableInteger("FSSC_Position", "Position", "Rollo.Position");
        $this->RegisterVariableInteger("FSSC_Timer", "Timer", "");
            
      
        //Boolean Variable anlegen
        //integer RegisterVariableBoolean ( string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit "getvalue($this->GetIDForIdent("IDENTNAME"))"
        $this->RegisterVariableBoolean("UpDown", "Rollo Up/Down");
        $this->RegisterVariableBoolean("Mode", "Mode");
        
        //String Variable anlegen
        //RegisterVariableString (  $Ident,  $Name, $Profil, $Position )
         // Aufruf dieser Variable mit "getvalue($this->GetIDForIdent("IDENTNAME"))"
        
        
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        $this->EnableAction("FSSC_Position");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("FSSC_Position"), "Rollo.Position");
     
        $this->EnableAction("UpDown");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("UpDown"), "Rollo.UpDown");
        
        $this->EnableAction("Mode");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("Mode"), "Rollo.Mode");
    }
    //*****************************************************************************
    /* Function: ApplyChanges    
      ApplyChanges() Wird ausgeführt, wenn auf der Konfigurationsseite "Übernehmen" gedrückt wird 
      und nach dem unittelbaren Erstellen der Instanz.
     
    SYSTEM-VARIABLE:
    InstanceID - $this->InstanceID.

    EVENTS:
     SwitchTimeEvent".$this->InstanceID   -   Wochenplan (Mo-Fr und Sa-So)
    SunRiseEvent".$this->InstanceID       -   cyclice Time Event jeden Tag at SunRise
     SunSetEvent".$this->InstanceID       -   cyclice Time Event jeden Tag at SunSet
    */
    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();

    	// Anlegen des Wochenplans mit ($Name, $Ident, $Typ, $Parent, $Position)
	$this->RegisterEvent("Wochenplan", "SwitchTimeEvent".$this->InstanceID, 2, $this->InstanceID, 20);    
     
	// Anlegen der Daten für den Wochenplan
        IPS_SetEventScheduleGroup($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 0, 31); //Mo - Fr (1 + 2 + 4 + 8 + 16)
        IPS_SetEventScheduleGroup($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 1, 96); //Sa + So (32 + 64)     
        
        //Aktionen erstellen mit  ($EventID, $ActionID, $Name, $Color, $Script)
	$this->RegisterScheduleAction($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 0, "Up", 0x40FF00, "FSSC_SetRolloUp(\$_IPS['TARGET']);");
	$this->RegisterScheduleAction($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 1, "Down", 0xFF0040, "FSSC_SetRolloDown(\$_IPS['TARGET']);");
         
        //Ändern von Schaltpunkten für Gruppe mit ID = 0 (Mo-Fr) ID = 1 (Sa-So)
        $eid = $this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID);
        IPS_SetEventScheduleGroupPoint($eid, 0, 0, 7, 0, 0, 0); //Um 7:00 Aktion mit ID 0 (Up) aufrufen
        IPS_SetEventScheduleGroupPoint($eid, 0, 1, 22, 00, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
        IPS_SetEventScheduleGroupPoint($eid, 1, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
        IPS_SetEventScheduleGroupPoint($eid, 1, 1, 22, 00, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
        IPS_SetEventActive($eid, true);             //Ereignis  aktivieren

        //anlegen eines Laufzeit Events
        $this->RegisterEvent("Laufzeit", "LaufzeitEvent".$this->InstanceID, 0, $this->InstanceID, 22);
        $LaufzeitEventID = $this->GetIDForIdent("LaufzeitEvent".$this->InstanceID);
        IPS_SetEventCyclic($LaufzeitEventID, 2 /* Täglich */, 1 /* Jeden Tag */, 0, 0, 1, 30 /* Alle 30 Sekunden */);    
        IPS_SetEventScript($LaufzeitEventID, "");
        
    	// Anlegen des cyclic events SunRise mit ($Name, $Ident, $Typ, $Parent, $Position)
	$this->RegisterEvent("SunRise", "SunRiseEvent".$this->InstanceID, 1, $this->InstanceID, 21); 
        $SunRiseEventID = $this->GetIDForIdent("SunRiseEvent".$this->InstanceID);
        // täglich, um x Uhr
        $sunrise = getvalue(56145);
        $sunrise_H = date("H", $sunrise); 
        $sunrise_M = date("i", $sunrise); 
        IPS_SetEventCyclicTimeFrom($SunRiseEventID, $sunrise_H, $sunrise_M, 0);
        IPS_SetEventScript($SunRiseEventID, "FSSC_SetRolloUp(\$_IPS['TARGET']);");
    	// Anlegen des cyclic events SunSet mit ($Name, $Ident, $Typ, $Parent, $Position)
	$this->RegisterEvent("SunSet", "SunSetEvent".$this->InstanceID, 1, $this->InstanceID, 21); 
        $SunSetEventID = $this->GetIDForIdent("SunSetEvent".$this->InstanceID);
        // täglich, um x Uhr
        $sunset = getvalue(25305);
        $sunset_H = date("H", $sunset); 
        $sunset_M = date("i", $sunset); 
        IPS_SetEventCyclicTimeFrom($SunSetEventID, $sunset_H, $sunset_M, 0);
        IPS_SetEventScript($SunSetEventID, "FSSC_SetRolloDown(\$_IPS['TARGET']);");

            
        if($this->ReadPropertyBoolean("SunRise")){
            IPS_SetEventActive($SunRiseEventID, true);             //Ereignis  aktivieren
            IPS_SetEventActive($SunSetEventID, true);             //Ereignis  aktivieren
            IPS_SetEventActive($eid, false);             //Ereignis  deaktivieren
            IPS_SetHidden($eid, true); //Objekt verstecken
            IPS_SetDisabled($eid, true);// Das Objekt wird inaktiv gesetzt.
             IPS_SetHidden($SunRiseEventID, false); //Objekt verstecken
            IPS_SetDisabled($SunRiseEventID, true);// Das Objekt wird inaktiv gesetzt.
            IPS_SetHidden($SunSetEventID, false); //Objekt verstecken
            IPS_SetDisabled($SunSetEventID, true);// Das Objekt wird inaktiv gesetzt.
        }
        else {
            IPS_SetEventActive($SunRiseEventID, false);             //Ereignis  deaktivieren
            IPS_SetEventActive($SunSetEventID, false);             //Ereignis  deaktivieren
            IPS_SetEventActive($eid, true);             //Ereignis  aktivieren
            IPS_SetHidden($eid, false); //Objekt nicht verstecken
            IPS_SetDisabled($eid, false);// Das Objekt wird aktiv gesetzt.
            IPS_SetHidden($SunRiseEventID, true); //Objekt verstecken
            IPS_SetDisabled($SunRiseEventID, true);// Das Objekt wird inaktiv gesetzt.
            IPS_SetHidden($SunSetEventID, true); //Objekt verstecken
            IPS_SetDisabled($SunSetEventID, true);// Das Objekt wird inaktiv gesetzt.
        } 
       
    }
    //*****************************************************************************
    /* Function: RequestAction  
      RequestAction() Wird ausgeführt, wenn auf der Webfront eine Variable
      geschaltet oder verändert wird. Es werden die System Variable des betätigten
      Elementes übergeben.
     
   
    SYSTEM-VARIABLE:
      $this->GetIDForIdent($Ident)     -   ID der von WebFront geschalteten Variable
      $Value                           -   Wert der von Webfront geänderten Variable

   STANDARD-AKTIONEN:
      FSSC_Position    -   Slider für Position
      UpDown           -   Switch für up / Down
      Mode             -   Switch für Automatik/Manual
    */
    public function RequestAction($Ident, $Value) {
         switch($Ident) {
            case "FSSC_Position":
                //Hier würde normalerweise eine Aktion z.B. das Schalten ausgeführt werden
                //Ausgaben über 'echo' werden an die Visualisierung zurückgeleitet
                $this->setRollo($Value);

                //Neuen Wert in die Statusvariable schreiben
                //SetValue($this->GetIDForIdent($Ident), $Value);
                break;
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
    /* Section: Public Funtions
     Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
    
     FSSC_XYFunktion($Instance_id, ... );
    */
    
    //*****************************************************************************
    /* Function: StepRolloDown
    ...............................................................................
    fährt den Rolladen Schrittweise Zu = Down
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    public function StepRolloDown(){
        FS20_DimDown($this->ReadPropertyInteger("FS20RSU_ID"));
        $aktpos = getvalue($this->GetIDForIdent("FSSC_Position")) + 5; 
        if($aktpos > 100){$aktpos = 100;}
        setvalue($this->GetIDForIdent("FSSC_Position"), $aktpos ); //Stellung um 5% verändern        
    }   
    //*****************************************************************************
    /* Function: StepRolloUp
    ...............................................................................
    fährt den Rolladen Schrittweise Auf = Up
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    public function StepRolloUp(){
        FS20_DimUp($this->ReadPropertyInteger("FS20RSU_ID"));
        $aktpos = getvalue($this->GetIDForIdent("FSSC_Position")) - 5; 
        if($aktpos < 0){$aktpos = 0;}
        setvalue($this->GetIDForIdent("FSSC_Position"), $aktpos ); //Stellung um 5% verändern  
    }
    //*****************************************************************************
    /* Function: SetMode
    ...............................................................................
    Setzt Automatik bzw. Manual Modus
     * Automatik aktiviert die Events
     * Manual deaktiviert die Events
      ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetMode(bool $mode) {
        $eid = $this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID);
        if ($mode) {
           IPS_SetEventActive($eid, true); 
        } 
        else {
           IPS_SetEventActive($eid, false); 
        }
       SetValue($this->GetIDForIdent("Mode"), $mode);
    } 
    //*****************************************************************************
    /* Function: SetRolloUp
    ...............................................................................
    fährt den Rolladen auf 0% = Auf = Up
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetRolloUp() {
       $Tup = $this->ReadPropertyFloat('Time_UO'); 
       FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, $Tup); 
       Setvalue($this->GetIDForIdent("UpDown"),false);
       $VarArray = IPS_GetVariable($this->GetIDForIdent("UpDown"));
       $zeit = $VarArray["VariableUpdated"];
       Setvalue($this->GetIDForIdent("FSSC_Timer"), $zeit);
       SetValue($this->GetIDForIdent("FSSC_Position"), 0);
    }   
    //*****************************************************************************
    /* Function: SetRolloDown
    ...............................................................................
    fährt den Rolladen auf 100% = Zu = Down
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
     public function SetRolloDown() {
       $Tdown = $this->ReadPropertyFloat('Time_OU'); 
       FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, $Tdown); 
       Setvalue($this->GetIDForIdent("UpDown"),true); 
       $VarArray = IPS_GetVariable($this->GetIDForIdent("UpDown"));
       $zeit = $VarArray["VariableUpdated"];
       Setvalue($this->GetIDForIdent("FSSC_Timer"), $zeit);
       SetValue($this->GetIDForIdent("FSSC_Position"), 100);
    }   
    //*****************************************************************************
    /* Function: StepRolloStop
    ...............................................................................
    Stopt die fahrt
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
     public function SetRolloStop() {
       $startTime = getvalue($this->GetIDForIdent("FSSC_Timer"));  
       $direct = getvalue($this->GetIDForIdent("UpDown"));  
       if($direct){
            FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, 0); 
       }
       else{
           FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, 0); 
       }     
       $VarArray = IPS_GetVariable($this->GetIDForIdent("UpDown"));
       $zeit = time();
       $dTime = $zeit - $startTime;
       Setvalue($this->GetIDForIdent("FSSC_Timer"), $dTime);
       if($direct){
            Setvalue($this->GetIDForIdent("FSSC_Position"), getvalue($this->GetIDForIdent("FSSC_Position")) + $dTime * (100/$this->ReadPropertyFloat('Time_OU')));
       }
       else{
          Setvalue($this->GetIDForIdent("FSSC_Position"), getvalue($this->GetIDForIdent("FSSC_Position")) - $dTime * (100/$this->ReadPropertyFloat('Time_UO')));  
       } 
    }  
    //*****************************************************************************
    /* Function: SetRollo
    ...............................................................................
    fährt den Rolladen auf 100% = Zu = Down
    ...............................................................................
    Parameters: 
     $pos -   Position des Rolladens in 0-100%
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetRollo(integer $pos) {
        $lastPos = getvalue($this->GetIDForIdent("FSSC_Position"));
        if($pos>$lastPos){
            //runterfahren
            //Abstand ermitteln
            $dpos = $pos-$lastPos;
            //Zeit ermitteln für dpos
            
            $Tdown = $this->ReadPropertyFloat('Time_OU');
            $Tmid = $this->ReadPropertyFloat('Time_OM');
            if($dpos<51){
                $time = $dpos * ($Tmid/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, $time); 
                Setvalue($this->GetIDForIdent("UpDown"),true); 
            }
            else{
                $time = $dpos * ($Tdown/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, $time); 
                Setvalue($this->GetIDForIdent("UpDown"),true); 
            }
        }
        else{
            //hochfahren
            //Abstand ermitteln
            $dpos = $lastPos-$pos;
            //Zeit ermitteln für dpos
            
            $Tup = $this->ReadPropertyFloat('Time_UO');
            $Tmid = $this->ReadPropertyFloat('Time_UM');
            if($dpos<51){
                $time = $dpos * ($Tmid/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, $time); 
                Setvalue($this->GetIDForIdent("UpDown"),false); 
            }
            else{
                $time = $dpos * ($Tup/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, $time); 
                Setvalue($this->GetIDForIdent("UpDown"),false);
            } 
            
        }
        SetValue($this->GetIDForIdent("FSSC_Position"), $pos);
    }

   /* ---------------------------------------------------------------------
    * Section: Private Funtions
    * Die folgenden Funktionen sind nur zur internen Verwendung verfügbar
    *   Hilfsfunktionen
    * ---------------------------------------------------------------------
    */  
    
 
    //*****************************************************************************
    /* Function: SetTimer
    ...............................................................................
    Schreibt Aktions Zeit in Timer
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    private function SetTimer(){
       
    }
    
    //*****************************************************************************
    /* Function: RegisterEvent
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
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    private function RegisterEvent($Name, $Ident, $Typ, $Parent, $Position)
    {
            $eid = @$this->GetIDForIdent($Ident);
            if($eid === false) {
                    $eid = 0;
            } elseif(IPS_GetEvent($eid)['EventType'] <> $Typ) {
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
    
    //*****************************************************************************
    /* Function: RegisterScheduleAction
    ...............................................................................
     *  Legt eine Aktion für den Event fest
     * Beispiel:
     * ("SwitchTimeEvent".$this->InstanceID), 1, "Down", 0xFF0040, "FSSC_SetRolloDown(\$_IPS['TARGET']);");
    ...............................................................................
    Parameters: 
      $EventID
      $ActionID
      $Name
      $Color
      $Script
    --------------------------------------------------------------------------------
    Returns:    
        none
    //////////////////////////////////////////////////////////////////////////////*/
    private function RegisterScheduleAction($EventID, $ActionID, $Name, $Color, $Script)
    {
            IPS_SetEventScheduleAction($EventID, $ActionID, $Name, $Color, $Script);
    }
    
		
    //*****************************************************************************
    /* Function: GetIPSVersion
    ...............................................................................
    gibt die instalierte IPS Version zurück
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:   
        $ipsversion
    //////////////////////////////////////////////////////////////////////////////*/
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

		
}