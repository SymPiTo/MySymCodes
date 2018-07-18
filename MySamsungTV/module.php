<?php
//zugehoerige TRAIT-Klassen    
require_once(__DIR__ . "/SamsungTV_Interface.php");


class MySamsungTV extends IPSModule
{
    
    //externe Klasse einbinden - ueberlagern mit TRAIT
    use SamsungUPNP;
        
    //*****************************************************************************
    /* Function: Standardfunktinen für ein Modul
    ...............................................................................
    *  Create()
     * ApplyChanges()
     */
    /* **************************************************************************** */
    public function Create()
    {
	//Never delete this line!
        parent::Create();
           
        // Variable aus dem Instanz Formular registrieren (um zugänglich zu machen)
        $this->RegisterPropertyBoolean("active", false);
        $this->RegisterPropertyString("ip", "192.168.178.35");
        $this->RegisterPropertyInteger("updateInterval", 10000);	
        $this->RegisterPropertyInteger("devicetype", 1);
        
        //Variable anlegen.
        $this->RegisterVariableString("TVchList", "ChannelList");
        
        
        
        
        
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
    }
    
        // ApplyChanges() wird einmalig aufgerufen beim Erstellen einer neuen Instanz und
        // bei Änderungen der Formular Parameter (form.json) (nach Übernahme Bestätigung)
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
    public function ApplyChanges() {
	//Never delete this line!
        parent::ApplyChanges();
       
    }
    
    //*****************************************************************************
    /* Function: Hilfs funktinen für ein Modul
    ...............................................................................
    *  SendToSplitter(string $payload)
     * GetIPSVersion()
     * RegisterProfile()
     * RegisterProfileAssociation()
     *  SetValue($Ident, $Value)
     */
    /* **************************************************************************** */
    protected function SendToSplitter(string $payload)
		{						
			//an Splitter schicken
			$result = $this->SendDataToParent(json_encode(Array("DataID" => "{F1BA0997-BDDD-2732-1C5C-4C61BFD36F21}", "Buffer" => $payload))); // Interface GUI
			return $result;
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

	//Profile
	protected function RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize, $Digits, $Vartype)
	{

		if (!IPS_VariableProfileExists($Name)) {
			IPS_CreateVariableProfile($Name, $Vartype); // 0 boolean, 1 int, 2 float, 3 string,
		} else {
			$profile = IPS_GetVariableProfile($Name);
			if ($profile['ProfileType'] != $Vartype)
				$this->SendDebug("BMW:", "Variable profile type does not match for profile " . $Name, 0);
		}

		IPS_SetVariableProfileIcon($Name, $Icon);
		IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
		IPS_SetVariableProfileDigits($Name, $Digits); //  Nachkommastellen
		IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize); // string $ProfilName, float $Minimalwert, float $Maximalwert, float $Schrittweite
	}

	protected function RegisterProfileAssociation($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype, $Associations)
	{
		if (sizeof($Associations) === 0) {
			$MinValue = 0;
			$MaxValue = 0;
		}

		$this->RegisterProfile($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $Stepsize, $Digits, $Vartype);

		//boolean IPS_SetVariableProfileAssociation ( string $ProfilName, float $Wert, string $Name, string $Icon, integer $Farbe )
		foreach ($Associations as $Association) {
			IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
		}

	}
        
  	//Add this Polyfill for IP-Symcon 4.4 and older
	protected function SetValue($Ident, $Value)
	{

		if (IPS_GetKernelVersion() >= 5) {
			parent::SetValue($Ident, $Value);
		} else {
			SetValue($this->GetIDForIdent($Ident), $Value);
		}
	}

    //*****************************************************************************
    /* Function: Build Configuration Form
    ...............................................................................
    *  Überschreibt form.jsom
     */
    /* **************************************************************************** */
	public function GetConfigurationForm()
	{
		// return current form
		return json_encode([
			'elements' => $this->FormHead(),
			'actions' => $this->FormActions(),
			'status' => $this->FormStatus()
		]);
	}

	/**
	 * return form configurations on configuration step
	 * @return array
	 */
	protected function FormHead()
	{
		$form = [
 			[
				'name' => 'active',
				'type' => 'CheckBox',
				'caption' => 'active',
			],
			[
				'name' => 'devicetype',
				'type' => 'Select',
				'caption' => 'device type',
				'options' => [
					[
						'label' => 'Please choose',
						'value' => -1
					],
					[
						'label' => 'Samsung UE40D8000',
						'value' => 0
					],
					[
						'label' => 'Type 2',
						'value' => 1
					]
				]
			],
			[
				'type' => 'Label',
				'label' => 'IP adress'
			],
			[
				'name' => 'ip',
				'type' => 'ValidationTextBox',
				'caption' => 'IP adress'
			],
			[
				'type' => 'Label',
				'label' => 'update Interval'
			],
			[
				'name' => 'updateInterval',
				'type' => 'ValidationTextBox',
				'caption' => 'update Interval [ms]'
			]
		];
		return $form;
	}

	/**
	 * return form actions
	 * @return array
	 */
	protected function FormActions()
	{
		$form = [
			[
				'type' => 'Label',
				'label' => 'Update'
			],
			[
				'type' => 'Button',
				'label' => 'labelname',
				'onClick' => 'Prefix_Functionname($id);'
			]
		];

		return $form;
	}

	/**
	 * return from status
	 * @return array
	 */
	protected function FormStatus()
	{
		$form = [
			[
				'code' => 101,
				'icon' => 'inactive',
				'caption' => 'Creating instance.'
			],
			[
				'code' => 102,
				'icon' => 'active',
				'caption' => 'Device created.'
			],
			[
				'code' => 104,
				'icon' => 'inactive',
				'caption' => 'interface closed.'
			],
			[
				'code' => 201,
				'icon' => 'error',
				'caption' => 'special errorcode'
			]
		];

		return $form;
	}


    //*****************************************************************************
    /* Function: Eigene Public Funktionen
    /* **************************************************************************** */		
    public function buildChannelList() {
        //$Kernel = str_replace("\\", "/", IPS_GetKernelDir());
        $Channellist = file_get_contents($this->Kernel."media/".'channellist.txt');
        $channel = explode("\n", $Channellist);

        $n =  0;
        foreach($channel as $ch) {
                $kanal = explode("\t", $ch);
                if ($kanal[0] == 'List'){
                        $head = $kanal;
                }else{

                        $chlist[$n][$head[1]] = $kanal[1];
                        $chlist[$n][$head[2]] = $kanal[2];
                        // auf Kanal schalten und MainChannel XML auslesen
                        //$key = 'KEY_' +  $kanal[2];
                        $key = 'KEY_CHUP'; 
                        //$result =   STV_sendKey($ID, $key);
                        //$result =   STV_sendKey($ID, 'KEY_ENTER');

                        $mc = $this->GetCurrentMainTVChannel_MTVA();
                        $chlist[$n]['ChType'] = $mc['ChType'];
                        $chlist[$n]['MAJORCH'] = $mc['MAJORCH'];
                        $chlist[$n]['MINORCH'] = $mc['MINORCH'];
                        $chlist[$n]['PTC'] = $mc['PTC'];
                        $chlist[$n]['PROGNUM'] = $mc['PROGNUM'];
                        $this->sendKey($key);
                        //IPS_SLEEP(100);

                }
                $n= $n + 1;
        } 
        $chListSer = serialize($chlist);
        setvalue(TVchList, $chListSer);
    }    
        
        
    //*****************************************************************************
    /* Function: Eigene Interne Funktionen
    /* **************************************************************************** */	        
      
        
        
	//*****************************************************************************
	/* Function: Kernel()
        ...............................................................................
        Stammverzeichnis von IP Symcon
        ...............................................................................
        Parameter:  

        --------------------------------------------------------------------------------
        return:  

        --------------------------------------------------------------------------------
        Status  checked 11.6.2018
        //////////////////////////////////////////////////////////////////////////////*/
        Protected function Kernel(){ 
            $Kernel = str_replace("\\", "/", IPS_GetKernelDir());
            return $Kernel;
        }         
}