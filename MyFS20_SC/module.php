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
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit "getvalue($this->GetIDForIdent("IDENTNAME"))"
        $this->RegisterVariableInteger("FSSC_Position", "Position", "Rollo.Position");
        
        //Boolean Variable anlegen
        //integer RegisterVariableBoolean ( string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit "getvalue($this->GetIDForIdent("IDENTNAME"))"
        $this->RegisterVariableBoolean("UpDown", "Rollo Up/Down");
 
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        $this->EnableAction("FSSC_Position");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("FSSC_Position"), "Rollo.Position");
     
        $this->EnableAction("UpDown");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("UpDown"), "Rollo.UpDown");
        
        
        //Wochenplan - Ereignis erzeugen
        //if (IPS_EventExists(34881))
        $eid = IPS_CreateEvent(2);                  //Wochenplan Ereignis
        //Anlegen von Gruppen
        IPS_SetEventScheduleGroup($eid, 0, 31); //Mo - Fr (1 + 2 + 4 + 8 + 16)
        IPS_SetEventScheduleGroup($eid, 1, 96); //Sa + So (32 + 64)
        //Anlegen von Schaltpunkten für Gruppe mit ID = 0 (=Mo-Fr)
        IPS_SetEventScheduleGroupPoint($eid, 0, 0, 8, 0, 0, 0); //Um 8:00 Aktion mit ID 0 (Up) aufrufen
        IPS_SetEventScheduleGroupPoint($eid, 0, 1, 22, 30, 0, 1); //Um 22:30 Aktion mit ID 1 (Down) aufrufen
        //Anlegen von Aktionen 
        IPS_SetEventScheduleAction($EreignisID, 0, "Up", 0xFF0000, "FSSC_SetRolloUp();");
        IPS_SetEventScheduleAction($EreignisID, 1, "Down", 0x0000FF, "FSSC_SetRolloDown();");
            
 

        IPS_SetParent($eid, $this->GetIDForIdent("UpDown"));         //Eregnis zuordnen
        IPS_SetEventActive($eid, true);             //Ereignis aktivieren
        
    }
    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
       
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
            default:
                throw new Exception("Invalid Ident");
        }
 
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