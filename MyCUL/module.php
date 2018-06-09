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

include_once(__DIR__ . "/moduleBase.php");
include_once(__DIR__ . "/FHZ_Class.php");

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
    public function __construct()
    {
        // 

    }

    //--------------------------------------------------------
    /**
     * overload internal IPS_Create($id) function
     */
    public function Create(){
        parent::Create();
        //Hint: $this->debug will not work in this stage! must use IPS_LogMessage
        //props


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


}//class
