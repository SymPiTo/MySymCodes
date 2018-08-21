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
        $this->RegisterPropertyBoolean("SunRise", true);

        
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
        
        //Wochenplan - Ereignis erzeugen

            //$eid = IPS_CreateEvent(2);                  //Wochenplan Ereignis
            //IPS_SetName($eid, "SwitchTimeEvent");
            //IPS_SetParent($eid, $this->GetIDForIdent("UpDown"));         //Eregnis zuordnen
            //IPS_SetEventActive($eid, false);             //Ereignis  deaktivieren
            //Anlegen von Gruppen
            //IPS_SetEventScheduleGroup($eid, 0, 31); //Mo - Fr (1 + 2 + 4 + 8 + 16)
            //IPS_SetEventScheduleGroup($eid, 1, 96); //Sa + So (32 + 64)      

            //Anlegen von Aktionen 
            //IPS_SetEventScheduleAction ($EreignisID, $AktionsID, $Name, $Farbe, $Skriptinhalt )
            //IPS_SetEventScheduleAction($eid, 0, "Up", 0xFF0000, 'FSSC_SetRolloUp($_IPS[\'TARGET\']);');
            //IPS_SetEventScheduleAction($eid, 1, "Down", 0x0000FF, 'FSSC_SetRolloDown($_IPS[\'TARGET\']);');

            //Anlegen von Schaltpunkten für Gruppe mit ID = 0 (=Mo-Fr)
            //IPS_SetEventScheduleGroupPoint ($EreignisID, $GruppenID, $SchaltpunktID, Stunde,Minute,Sekunde, $AktionsID )
           // IPS_SetEventScheduleGroupPoint($eid, 0, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
            //IPS_SetEventScheduleGroupPoint($eid, 0, 1, 22, 30, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
            //IPS_SetEventScheduleGroupPoint($eid, 1, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
            //IPS_SetEventScheduleGroupPoint($eid, 1, 1, 22, 30, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
    

 

            
    }
    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
        //Wird ausgeführt, wenn auf der Konfigurationsseite "Übernehmen" gedrückt wird und nach dem unittelbaren Erstellen der Instanz.
        
    	// Anlegen des Wochenplans mit ($Name, $Ident, $Typ, $Parent, $Position)
	$this->RegisterEvent("Wochenplan", "SwitchTimeEvent".$this->InstanceID, 2, $this->InstanceID, 20);    
 
    
	// Anlegen der Daten für den Wochenplan
        IPS_SetEventScheduleGroup($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 0, 31); //Mo - Fr (1 + 2 + 4 + 8 + 16)
        IPS_SetEventScheduleGroup($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 1, 96); //Sa + So (32 + 64)     
        

        
        //Aktionen erstellen mit  ($EventID, $ActionID, $Name, $Color, $Script)
	$this->RegisterScheduleAction($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 0, "Up", 0x40FF00, "FSSC_SetRolloUp(\$_IPS['TARGET']);");
	$this->RegisterScheduleAction($this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID), 1, "Down", 0xFF0040, "FSSC_SetRolloUp(\$_IPS['TARGET']);");
         




            $eid = $this->GetIDForIdent("SwitchTimeEvent".$this->InstanceID);
            if($this->ReadPropertyBoolean("SunRise")){
                $sunrise = getvalue(56145);
                $sunrise_H = date("H", $sunrise); 
                $sunrise_M = date("i", $sunrise); 
                $sunset = getvalue(25305);
                $sunset_H = date("H", $sunset); 
                $sunset_M = date("i", $sunset); 
                //Ändern von Schaltpunkten für Gruppe mit ID = 0 (=Mo-Fr) ID = 1 (=Sa-So)

                IPS_SetEventScheduleGroupPoint($eid, 0, 0, $sunrise_H, $sunrise_M, 0, 0); //Um Sonnenaufgang Aktion mit ID 0 (Up) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 0, 1, $sunset_H, $sunset_M, 0, 1); //Um Sonnenuntergang Aktion mit ID 1 (Down) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 1, 0, $sunrise_H, $sunrise_M, 0, 0); //Um Sonnenaufgang Aktion mit ID 0 (Up) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 1, 1, $sunset_H, $sunset_M, 0, 1); //Um Sonnenuntergang Aktion mit ID 1 (Down) aufrufen
            
            }
            else {
                //Ändern von Schaltpunkten für Gruppe mit ID = 0 (=Mo-Fr) ID = 1 (=Sa-So)
                IPS_SetEventScheduleGroupPoint($eid, 0, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 0, 1, 22, 30, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 1, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
                IPS_SetEventScheduleGroupPoint($eid, 1, 1, 22, 30, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
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
        $eid = IPS_GetEventIDByName("SwitchTimeEvent", $this->GetIDForIdent("UpDown"));
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
		    	IPS_SetEventActive($EventID, true);  
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