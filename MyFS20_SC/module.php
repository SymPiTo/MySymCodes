<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of module
 *
 * @author PiTo
 */
class MyFS20_SC extends IPSModule
{

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
    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
        //Wird ausgeführt, wenn auf der Konfigurationsseite "Übernehmen" gedrückt wird und nach dem unittelbaren Erstellen der Instanz.
        //--------------------------------------------------------------------------------------------------------------------------------
            
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
            IPS_SetEventActive($eid, false);             //Ereignis  aktivieren
            
        }
        else {
            IPS_SetEventActive($SunRiseEventID, false);             //Ereignis  deaktivieren
            IPS_SetEventActive($SunSetEventID, false);             //Ereignis  deaktivieren
            IPS_SetEventActive($eid, true);             //Ereignis  aktivieren
        } 
       
    }
    
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
    
    public function SetRolloUp() {
       $Tup = $this->ReadPropertyFloat('Time_UO'); 
       FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, $Tup); 
       SetValue($this->GetIDForIdent("FSSC_Position"), 0);
    }   

     public function SetRolloDown() {
       $Tdown = $this->ReadPropertyFloat('Time_OU'); 
       FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, $Tdown); 
       SetValue($this->GetIDForIdent("FSSC_Position"), 100);
    }   
    
    public function SetRollo($pos) {
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
            }
            else{
                $time = $dpos * ($Tdown/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), false, $time); 
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
            }
            else{
                $time = $dpos * ($Tup/50);
                FS20_SwitchDuration($this->ReadPropertyInteger("FS20RSU_ID"), true, $time); 
            } 
            
        }
            
        
        SetValue($this->GetIDForIdent("FSSC_Position"), $pos);
    }
    
    
    
    
    
    
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
    
    
	private function RegisterScheduleAction($EventID, $ActionID, $Name, $Color, $Script)
	{
		IPS_SetEventScheduleAction($EventID, $ActionID, $Name, $Color, $Script);
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





		
}