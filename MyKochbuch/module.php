<?php
//zugehoerige TRAIT-Klassen    TEST xxxy
require_once(__DIR__ . "/../libs/NetworkTraits.php");


/** 
 * Title: Repte aus Chefkoch.de Kochbch
  *
 * author PiTo
 * 
 * GITHUB = <https://github.com/SymPiTo/MySymCodes/tree/master/MyKochbuch>
 * 
 * Version:1.0.2019.03.22
 */
//Class: MyKochbuch
class MyKochbuch extends IPSModule
{
    
    //externe Klasse einbinden - ueberlagern mit TRAIT.
    use MyDebugHelper;
    
    
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
         // Aufruf dieser Form Variable mit  §this->ReadPropertyFloat('IDENTNAME')
        //$this->RegisterPropertyInteger('IDENTNAME', 0);
        //$this->RegisterPropertyFloat('IDENTNAME', 0.5);
        //$this->RegisterPropertyBoolean('IDENTNAME', false);
        
       
        
        //Integer Variable anlegen
        //integer RegisterVariableInteger (string $Ident, string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit $this->GetIDForIdent('IDENTNAME')
        //$this->RegisterVariableInteger('FSSC_Position', 'Position', 'Rollo.Position);
        $this->RegisterVariableInteger('ID_No', 'Number', '');
        $this->SetValue("ID_No", 1);
        
        //Boolean Variable anlegen
        //integer RegisterVariableBoolean (string 'Ident', string $Name, string $Profil, integer $Position )
        // Aufruf dieser Variable mit $this->GetIDForIdent('IDENTNAME')
        //$this->RegisterVariableBoolean('FSSC_Mode', 'Mode');
        
        //String Variable anlegen
        //RegisterVariableString ( $Ident,  $Name, $Profil, $Position )
        // Aufruf dieser Variable mit $this->GetIDForIdent('IDENTNAME')
        $this->RegisterVariableString("ID_Rezept", "Rezept");
        $this->RegisterVariableString("ID_Bild", "Image","~HTMLBox");
        $this->RegisterVariableString("ID_Zutaten", "Zutaten");
        $this->RegisterVariableString("ID_Kochbuch", "Kochbuch");
        
         
        // Aktiviert die Standardaktion der Statusvariable zur Bedienbarkeit im Webfront
        //$this->EnableAction('IDENTNAME');
        //IPS_SetVariableCustomProfile(§this->GetIDForIdent(!Mode!), !Rollo.Mode!);
        
        //anlegen eines Timers
        //$this->RegisterTimer(!TimerName!, 0, !FSSC_reset(\§_IPS[!TARGET!>]);!);
            


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
            
        //init
        $this->readKochbuch(0);
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
 
    }

  /* ______________________________________________________________________________________________________________________
     Section: Public Funtions
     Die folgenden Funktionen stehen automatisch zur Verfügung, wenn das Modul über die "Module Control" eingefügt wurden.
     Die Funktionen werden, mit dem selbst eingerichteten Prefix, in PHP und JSON-RPC wie folgt zur Verfügung gestellt:
    
     FSSC_XYFunktion($Instance_id, ... );
     ________________________________________________________________________________________________________________________ */
    //-----------------------------------------------------------------------------
    /* Function: readKochbuch
    ...............................................................................
    Beschreibung
    ...............................................................................
    Parameters: 
        none
    ...............................................................................
    Returns:    
        none
    ------------------------------------------------------------------------------  */
    public function readKochbuch($No){
        //Einlesen der Kochbuch json Datei
        $ModulPath = "MyKochbuch";
        $JsonFileName = "Rezepte.json";
        $CookBook = $this->readJsonFile($ModulPath, $JsonFileName);
            
        foreach ($CookBook['Rezepte'] as $key=>$rezept) {
            
            $Kochbuch[$key]['name'] = $rezept['items'][0]['mainEntity'][1]['name'];
            $Kochbuch[$key]['recipeIngredient'] = $rezept['items'][0]['mainEntity'][1]['recipeIngredient'];
            $Kochbuch[$key]['image'] = $rezept['items'][0]['mainEntity'][1]['image'];
            $Kochbuch[$key]['recipeInstructions'] = $rezept['items'][0]['mainEntity'][1]['recipeInstructions'];
            $KochbuchIndex[$key] = $rezept['items'][0]['mainEntity'][1]['name'];
        }
        
        
            
            $ZutatenHTML = '
                <table width="100%">';
            foreach ($Kochbuch[$No]['recipeIngredient'] as $key => $value) {
                $ZutatenHTML .= '
                    <tr>
                        <td>
                            <div style="text-align: left;">' 
                                .$Kochbuch[$No]['recipeIngredient'][$key]. 
                            '</div>
                        </td>
                    </tr>
                ';
             }
            $ZutatenHTML .= '</table>';


            if(strlen($Kochbuch[$No]['image']) > 0) {
                $imageHTML =  '
                    <img src="'.@$Kochbuch[$No]['image'].'" style="width: auto; height: 200px">
                ';             
            }       
        
        
        $suchMuster = "\n\n";
        $str     =  $Kochbuch[$No]['recipeInstructions'];
            
        $replace = '<br>';
            
        $NewRezept = str_replace($suchMuster, $replace, $str);

        setvalue($this->GetIDForIdent('ID_Kochbuch'),serialize($KochbuchIndex));
        setvalue($this->GetIDForIdent('ID_Rezept'), $NewRezept);
        setvalue($this->GetIDForIdent('ID_Bild'), $imageHTML);
        //setvalue($this->GetIDForIdent('ID_Zutaten'),  implode("/r/n,", $Kochbuch[$No]['recipeIngredient']));
        setvalue($this->GetIDForIdent('ID_Zutaten'), $ZutatenHTML);
        return $Kochbuch;
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
    private function RegisterEvent($Name, $Ident, $Typ, $Parent, $Position)
    {
            $eid = @$this->GetIDForIdent($Ident);
            if($eid === false) {
                    $eid = 0;
            } elseif(IPS_GetEvent($eid)["EventType"] <> $Typ) {
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

    private function readJsonFile($ModulPath, $JsonFileName) {
        // Read JSON file
        $dataPath = IPS_GetKernelDir() . '/modules/MySymCodes/'.$ModulPath.'/';
        $json = file_get_contents($dataPath.$JsonFileName);
        //Decode JSON
        // true = json als array ausgeben
        $json_data = json_decode($json,true);
         
        return $json_data;
    }    

		
}