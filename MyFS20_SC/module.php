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
        // Aufruf dieser Form Variable mit -> $this->GetIDForIdent("IDENTNAME")
        $this->RegisterPropertyInteger("FS20RSU_ID", 0);
        $this->RegisterPropertyFloat("Time_OU", 0.5);
        $this->RegisterPropertyFloat("Time_UO", 0.5);
        $this->RegisterPropertyFloat("Time_OM", 0.5);
        $this->RegisterPropertyFloat("Time_UM", 0.5);
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger ( string $Ident, string $Name, string $Profil, integer $Position )
        $this->RegisterVariableInteger("FSSC_Position", "Position", "Rollo.Position");
 
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        $this->EnableAction("FSSC_Position");
        IPS_SetVariableCustomProfile($this->GetIDForIdent("FSSC_Position"), "Rollo.Position");
        
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
            default:
                throw new Exception("Invalid Ident");
        }
 
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