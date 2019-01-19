<?php
// require_once(__DIR__ . "/../libs/SymconLib.php");
require_once(__DIR__ . "/../libs/NetworkTraits.php");
require_once(__DIR__ . "/../libs/MyTraits.php");

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
   //externe Klasse einbinden - ueberlagern mit TRAIT.
    use MyDebugHelper,
        MyLogger;
    
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
            $this->RegisterPropertyString("SecAlarms", "[]");
            
            
          
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string §Ident, string §Name, string §Profil, integer §Position )
        // Aufruf dieser Variable mit $his->GetIDForIdent("IDENTNAME)
        $this->RegisterVariableInteger("A_AlarmCode", "AlarmCode", "Alarm.Code");
        //$this->RegisterVariableInteger("A_Activate", "Alarm Activate");
        
        
        //Boolean Variable anlegen
        // Aufruf dieser Variable mit §this->GetIDForIdent("IDENTNAME")
        $this->RegisterVariableBoolean("A_SecActivate", "Alarmanlage aktivieren");
        $this->RegisterVariableBoolean("A_SecActive", "Alarmanlage Aktiv");
        //Alexa Sprachbefehl Trigger
        $this->RegisterVariableBoolean("Alexa_SecActivate", "Alexa Alarmanlage activieren");
        //TTS Trigger
        $this->RegisterPropertyBoolean("AlexaTTS", false);
        
        //String Variable anlegen
        //RegisterVariableString (  §Ident,  §Name, §Profil, §Position )
         // Aufruf dieser Variable mit §this->GetIDForIdent(!IDENTNAME!)
        $this->RegisterVariableString("A_BatAlarm", "Battery Alarm");
        $this->RegisterVariableString("A_SecCode", "Security Code");
        $this->RegisterVariableString("A_SecWarning", "Security Meldung");  

            
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        //$this->EnableAction("IDENTNAME");
        $this->EnableAction("A_SecActivate");
        $this->EnableAction("A_SecCode");
        $this->EnableAction("Alexa_SecActivate");
        
        //anlegen eines Timers
        //$this->RegisterTimer(!TimerName!, 0, !FSSC_reset(\§_IPS[!TARGET!>]);!); 
        /*    
        $alleEreignisse = IPS_GetEventList();
        foreach ($alleEreignisse as $EreignisID) {
            IPS_DeleteEvent($EreignisID);
        }
        */
    }
    
    

        
   /* ------------------------------------------------------------ 
     Function: ApplyChanges 
      ApplyChanges() Wird ausgeführt, wenn auf der Konfigurationsseite "Übernehmen" gedrückt wird 
      und nach dem unittelbaren Erstellen der Instanz.
     
    SYSTEM-VARIABLE:
        InstanceID = $this->InstanceID.

    EVENTS:
        SwitchTimeEvent".$this->InstanceID   -   Wochenplan (Mo-Fr und Sa-So)
        SunRiseEvent".$this->InstanceID       -   cyclice Time Event jeden Tag at SunRise
    ------------------------------------------------------------- */
    public function ApplyChanges()
    {
        $assoc[0] = "deaktiviert";
        $assoc[1] = "aktiviert";  
	$this->RegisterProfile("Alarm.Activate", "","", "", "", "", "", "", 0, "A_Activate", $assoc);
        //Never delete this line!
        parent::ApplyChanges();
        
        //Unterkategorie Batterie Alarme anlegen
        $AlarmCatID = $this->RegisterCategory("BatAlarmEvents");
        // für jedes Liste ID ein Event anlegen
        $batteries = json_decode($this->ReadPropertyString("Battery"));
        foreach($batteries as $sensor) {
            $ParentID = $AlarmCatID;
            $Typ = 0;
            $Ident = "AE".$sensor->ID;
            $Name = "AEvent".$sensor->ID;
            $cmd = "A_BatAlarm(".$this->InstanceID.");" ;
            $this->RegisterVarEvent($Name, $Ident, $Typ, $ParentID, 0, 1, $sensor->ID, $cmd  );
        }       
        
         //Unterkategorie Sec  Alarme anlegen
        $SecAlarmCatID = $this->RegisterCategory("SecAlarmEvents");
        // für jedes Liste ID ein Event anlegen
        $SecAlarms = json_decode($this->ReadPropertyString("SecAlarms"));
        foreach($SecAlarms as $sensor) {
            $ParentID = $SecAlarmCatID;
            $Typ = 0;
            $Ident = "SecAE".$sensor->ID;
            $Name = "SecAEvent".$sensor->ID;
            $cmd = "A_SecurityAlarm(".$this->InstanceID.");";
            $this->RegisterVarEvent($Name, $Ident, $Typ, $ParentID, 0, 1, $sensor->ID, $cmd );
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
        A_SecActivate    -   Alarm Anlage aktivieren
 
     ------------------------------------------------------------- */
    public function RequestAction($Ident, $Value) {
            
         switch($Ident) {
             case "A_SecActivate":
                if (value == true){ 
                    $this->activateSecAlarm();  
                }
                else {
                   setvalue($this->GetIDForIdent("A_SecWarning"),"Sicherheits Code eingeben."); 
                   setvalue($this->GetIDForIdent("A_SecActivate"),true); 
                }
                break;
             case "Alexa_SecActivate":
                $this->activateSecAlarm();  
                break;
              case "A_SecCode":
                setvalue($this->GetIDForIdent("A_SecCode"),"$Value");
                $this->checkCode();  
                break;
            default:
                throw new Exception("Invalid Ident");
        }
            
    }

   /* ------------------------------------------------------------ 
    Function: Destroy  
      Destroy() Wird ausgeführt, wenn die Instance gelöscht wird.
     ------------------------------------------------------------- */
    public function Destroy()
    {
        if (IPS_GetKernelRunlevel() <> KR_READY) {
            return parent::Destroy();
        }
        if (!IPS_InstanceExists($this->InstanceID)) {
             
        //Profile löschen
        $this->UnregisterProfile("Alarm.Activate");

             
        }
        parent::Destroy();
    }
    
    /* ======================================================================================================================
     Section: Public Funtions
     Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
    
     FSSC_XYFunktion($Instance_id, ... );
     ======================================================================================================================= */
    
        //-----------------------------------------------------------------------------
        /* Function: ResetAlarm
        ...............................................................................
        Beschreibung
        ...............................................................................
        Parameters: 
            none
        ...............................................................................
        Returns:    
            none
        ------------------------------------------------------------------------------  */
        public function ResetAlarm(){
            setvalue($this->GetIDForIdent("A_AlarmCode"), 0);
        }  


        //-----------------------------------------------------------------------------
        /* Function: receiveCode
        ...............................................................................
        Beschreibung
        ...............................................................................
        Parameters: 
             key = Zahlen Code
        ...............................................................................
        Returns:    
            none
        ------------------------------------------------------------------------------  */
        public function receiveCode(string $key){
            $code = getvalue($this->GetIDForIdent("A_SecCode"));
            setvalue($this->GetIDForIdent("A_SecCode"), $code.$key);    
        }  

        //-----------------------------------------------------------------------------
        /* Function: resetCode
        ...............................................................................
        Beschreibung
            löscht den eingegebenen ZahlenCode.
        ...............................................................................
        Parameters: 
             key = Zahlen Code
        ...............................................................................
        Returns:    
            none
        ------------------------------------------------------------------------------  */
        public function resetCode(){
            setvalue($this->GetIDForIdent("A_SecCode"), "");    
        }  

        //-----------------------------------------------------------------------------
        /* Function: checkCode
        ...............................................................................
        Beschreibung

        ...............................................................................
        Parameters: 
            none
        ...............................................................................
        Returns:    
            none
        ------------------------------------------------------------------------------  */
        public function checkCode(){
            $code = getvalue($this->GetIDForIdent("A_SecCode"));
            if ($code === "04826"){ 
                $this->resetCode();
                if($this->ReadPropertyBoolean("AlexaTTS")){
                    //Sprachausgabe
                    $text_to_speech = "Code wurde akzeptiert";
                    SetValueBoolean($this->GetIDForIdent("A_SecActivate"),false);
                    SetValueBoolean($this->GetIDForIdent("A_SecActive"),false);
                    EchoRemote_TextToSpeech(26188, $text_to_speech);
                }
            }  
            else{
                $this->resetCode();
                    //Sprachausgabe
                if($this->ReadPropertyBoolean("AlexaTTS")){
                    $text_to_speech = "falscher code";
                    EchoRemote_TextToSpeech(26188, $text_to_speech);
                }
            }
        }  


        //-----------------------------------------------------------------------------
        /* Function: activateSecAlarm
        ...............................................................................
        Beschreibung

        ...............................................................................
        Parameters: 
            none
        ...............................................................................
        Returns:    
            none
        ------------------------------------------------------------------------------  */
        public function activateSecAlarm(){
            //Sprachausgabe
            if($this->ReadPropertyBoolean("AlexaTTS")){
                $text_to_speech = "Alarmanlage wird in 30Sekunden aktiv.";
                EchoRemote_TextToSpeech(26188, $text_to_speech);
            }
            sleep(30);
            SetValueBoolean($this->GetIDForIdent("A_SecActive"),true);
            //Sprachausgabe
            if($this->ReadPropertyBoolean("AlexaTTS")){
                $text_to_speech = "Alarmanlage ist aktiviert.";
                EchoRemote_TextToSpeech(26188, $text_to_speech);
            }
            
        } 

        /* ----------------------------------------------------------------------------
         Function: BatAlarm
        ...............................................................................
        Erzeugt einen Alarm bei zu schwacher Batterie
        ...............................................................................
        Parameters: 
            none.
        ..............................................................................
        Returns:   
             none
        ------------------------------------------------------------------------------- */
	public function BatAlarm(){
            //überprüfen welches Ereignis ausgelöst hat 
            $batteries = json_decode($this->ReadPropertyString("Battery"));
            $ParentID =   @IPS_GetObjectIDByName("BatAlarmEvents", $this->InstanceID);
            $lastEvent = 0;
            $lastTriggerVarID = false; 
            foreach($batteries as $sensor) {
                $EreignisID = @IPS_GetEventIDByName("AEvent".$sensor->ID, $ParentID);
                $EreignisInfo = IPS_GetEvent($EreignisID);
                $aktEvent = $EreignisInfo["LastRun"];
                if($aktEvent > $lastEvent){
                    $lastEvent = $aktEvent;
                    $lastTriggerVarID = $EreignisInfo["TriggerVariableID"];
                }
            }
            if($lastTriggerVarID){
            $ltv =  getvalue($lastTriggerVarID);
            $this->SendDebug( "$lastTriggerVarID: ", $ltv, 0); 
             
                // Batterie ist Low Alarm auslösen
                setvalue($this->GetIDForIdent("A_BatAlarm"), "Battery: ".$lastTriggerVarID)." Low";
                //AlarmCode auf 1 setzen
                setvalue($this->GetIDForIdent("A_AlarmCode"), 1);
            } 
            else{
               setvalue($this->GetIDForIdent("A_BatAlarm"), ""); 
               setvalue($this->GetIDForIdent("A_AlarmCode"), 0);
            }
        }  


        /* ----------------------------------------------------------------------------
         Function: SecurityAlarm
        ...............................................................................
        Erzeugt einen Alarm bei zu schwacher Batterie
        ...............................................................................
        Parameters: 
            none.
        ..............................................................................
        Returns:   
             none
        ------------------------------------------------------------------------------- */
	public function SecurityAlarm(){   
            $AlarmAnlageActive = getvalue($this->GetIDForIdent("A_SecActive"));
            if($AlarmAnlageActive){
                //überprüfen welches Ereignis ausgelöst hat 
                $SecAlarms = json_decode($this->ReadPropertyString("SecAlarms"));
                $ParentID =   @IPS_GetObjectIDByName("SecAlarmEvents", $this->InstanceID);
                $lastEvent = 0;
                $lastTriggerVarID = false; 
                foreach($SecAlarms as $sensor) {
                    $EreignisID = @IPS_GetEventIDByName("SecAEvent".$sensor->ID, $ParentID);
                    $EreignisInfo = IPS_GetEvent($EreignisID);
                    $aktEvent = $EreignisInfo["LastRun"];
                    if($aktEvent > $lastEvent){
                        $lastEvent = $aktEvent;
                        $lastTriggerVarID = $EreignisInfo["TriggerVariableID"];
                    }
                }
                if($lastTriggerVarID){
                    $ltv = getvalue($lastTriggerVarID);
                    //AlarmCode auf 2 setzen = Einbruch
                    setvalue($this->GetIDForIdent("A_AlarmCode"), 2);
                    
                    //Meldung in Log File schreiben.
                    $text = "Unbefugter Zugang zur Wohnung. ";
                    $array = "wurde erkannt.";
                    $this->ModErrorLog("MyIPSAlarm", $text, $array);
                    
                    $message = "Achtung ein unbefugter Zugang zur Wohnung wurde erkannt!";
                    Telegram_SendText(22525, $message, "671095116" );
                    //Sprachausgabe
                    if($this->ReadPropertyBoolean("AlexaTTS")){
                        $text_to_speech = "Alarm wurde ausgelöst.";
                        EchoRemote_TextToSpeech(11629, $text_to_speech);
                    }
                } 
                else{
             
                   setvalue($this->GetIDForIdent("A_AlarmCode"), 0);
                } 
            }
        }     
             
        
   /* ==========================================================================
    * Section: Private Funtions
    * Die folgenden Funktionen sind nur zur internen Verwendung verfügbar
    *   Hilfsfunktionen
    * ==========================================================================
    */  

        /* ----------------------------------------------------------------------------
         Function: RegisterProfile
        ...............................................................................
        Erstellt ein neues Profil und ordnet es einer Variablen zu.
        ...............................................................................
        Parameters: 
            $Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype, $VarIdent, $Assoc
         * $Vartype: 0 boolean, 1 int, 2 float, 3 string,
         * $Assoc: array mit statustexte
        ..............................................................................
        Returns:   
            none
        ------------------------------------------------------------------------------- */
	protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype, $VarIdent, $Assoc){

            
		if (!IPS_VariableProfileExists($Name)) {
			IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
		} else {
			$profile = IPS_GetVariableProfile($Name);
			if ($profile['ProfileType'] != $Vartype)
				$this->SendDebug("Alarm.Reset:", "Variable profile type does not match for profile " . $Name, 0);
		}

		//IPS_SetVariableProfileIcon($Name, $Icon);
		//IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		//IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
		//IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite

             
                foreach ($Assoc as $key => $value) {
                    $value = $value * 2;
                    IPS_SetVariableProfileAssociation($Name, $key, $value, $Icon, 0xFFFFFF);  
                }
                IPS_SetVariableCustomProfile($this->GetIDForIdent($VarIdent), $Name);
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
    private function RegisterVarEvent($Name, $Ident, $Typ, $ParentID, $Position, $trigger, $var, $cmd)
    {
            $eid =  @IPS_GetEventIDByName($Name, $ParentID);
            if($eid === false) {
                //we need to create one
                $EventID = IPS_CreateEvent($Typ);
                IPS_SetParent($EventID, $ParentID);
                @IPS_SetIdent($EventID, $Ident);
                IPS_SetName($EventID, $Name);
                IPS_SetPosition($EventID, $Position);
                IPS_SetEventTrigger($EventID, $trigger, $var);   //OnChange für Variable $var
                
                IPS_SetEventScript($EventID, $cmd );
                IPS_SetEventActive($EventID, true);
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
    
    protected function UnregisterProfile(string $Name){
        if (IPS_VariableProfileExists($Name)) {
           IPS_DeleteVariableProfile($Name);
        }   
    }	
}