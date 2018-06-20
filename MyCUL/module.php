 <?php
/**
 * @file
 *
 * CUL IPSymcon PHP Splitter Module  for busware.de CUL/CUN/COC receiver
 *
 * @author Thomas Dressler
 * @copyright Thomas Dressler 2011-2017
 * @version 4.2.4
 * @date 2017-09-16
 */

//include_once(__DIR__ . "/moduleBase.php");
//include_once(__DIR__ . "/FHZ_Class.php");

/** @class CUL
 *
 * CUL IPSymcon PHP Splitter Module Class
 * for busware.de CUL/CUN/COC receiver
 * tested with IPS 4.1 COC FW 1.61 (ESA,Dimmer not tested,FHT not implementd, only FHT-TFK)
 *
 * protocol decodes translated from CULFW project
 * @see http://culfw.de/commandref.html
 */
    class MyCUL  extends IPSModule {
     //------------------------------------------------------------------------------
    //module const and vars
    //------------------------------------------------------------------------------
    /**
     * Timer constant
     * maxage of LastUpdate in sec before ReInit
     */
    const MAXAGE = 300;

    /**
     * Fieldlist for Logging weather
     */
    const fieldlist_weather = "Date;Typ;Id;Name;Temp;Hum;Bat;Lost;Wind;Rain;IsRaining;RainCounter;Pressure;";


    //--------------------------------------------------------
    // main module functions
    //--------------------------------------------------------
    /**
     * Constructor
     * @param $InstanceID
     */
    public function __construct($InstanceID){
        // Diese Zeile nicht löschen
        parent::__construct($InstanceID);

    }

    //--------------------------------------------------------
    /**
     * overload internal IPS_Create($id) function
     */
    public function Create(){
        parent::Create();
        //Hint: $this->debug will not work in this stage! must use IPS_LogMessage
        //props
        //"SerialPort" => "{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}"
         $this->ForceParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}");

    }

    //--------------------------------------------------------
    /**
     * Destructor
     */
    public function Destroy()
    {
        parent::Destroy();
    }

    //--------------------------------------------------------
    /**
     * overload internal IPS_ApplyChanges($id) function
     */
    public function ApplyChanges()
    {
        // Diese Zeile nicht loeschen
        parent::ApplyChanges();


    }

    
    
       //------------------------------------------------------------------------------
    /**
     * Data Interface from Parent(IO-RX)
     * @param string $JSONString
     * @return void
     */
    public function ReceiveData($JSONString)
    {
        //status check triggered by data
                // decode Data from Device Instanz
        if (strlen($JSONString) > 0) {
            $this->debug(__FUNCTION__, 'Data arrived:' . $JSONString);
            $this->debuglog($JSONString);
            // decode Data from IO Instanz
            $data = json_decode($JSONString);
            //entry for data from parent
        }    
    }

}//class
