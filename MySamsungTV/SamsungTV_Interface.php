<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author PiTo
 */
trait SamsungUPNP {
    
    //AV TRANSPORT
    
    //*****************************************************************************
    /* Function: GetCurrentTransportActions_AV()
    ...............................................................................
    UPNP Transport Actin
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  
        none.
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetCurrentTransportActions_AV(){

    return  $this->processSoapCall("/upnp/control/AVTransport1",

                                       "urn:schemas-upnp-org:service:AVTransport:1",

                                       "GetCurrentTransportActions",

                                       array(

                                              new SoapParam("0"     ,"InstanceID")


                                            ));

  }


     //*****************************************************************************
    /* Function: GetDeviceCapabilities_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  
     *   PlayMedia (string) = NETWORK,NONE
     *   RecMedia           = NOT_IMPLEMENTED   
     *   RecQualityModes    = NOT_IMPLEMETED
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
  public function GetDeviceCapabilities_AV () {

    $this->processSoapCall("/upnp/control/AVTransport1",

                           "urn:schemas-upnp-org:service:AVTransport:1",

                           "GetDeviceCapabilities",

                           array(

                                  new SoapParam("0","InstanceID")

                                ));

  }
    
     //*****************************************************************************
    /* Function: GetMediaInfo_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  
     *   NrTracks (Integer)             =  0
     *   MediaDuration (string)         =  0:00:00   
     *   CurrentURI (string)            = 
     *   CurrentURIMetaData (string)    =   
     
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetMediaInfo_AV(){

    $mediaInfo = $this->processSoapCall("/upnp/control/AVTransport1",

                                        "urn:schemas-upnp-org:service:AVTransport:1",

                                        "GetMediaInfo",

                                        array(

                                               new SoapParam("0","InstanceID")

                                             ));



    $xmlParser = xml_parser_create("UTF-8");

    xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");

    xml_parse_into_struct($xmlParser, $mediaInfo["CurrentURIMetaData"], $vals, $index);

    xml_parser_free($xmlParser);

    if (isset($index["DC:TITLE"]) and isset($vals[$index["DC:TITLE"][0]]["value"])){

      $mediaInfo["title"] = $vals[$index["DC:TITLE"][0]]["value"];

    }else{

      $mediaInfo["title"] = "";

    }


    return $mediaInfo;
  }

    //*****************************************************************************
    /* Function: GetPositionInfo_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  
     *   Track (Integer)             =  0
     *   TrackDuration (string)      =  0:00:00   
     *   TrackMetaData (string)      = 
     *   TrackURI (string)           =   
     
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetPositionInfo_AV() {

        $positionInfo = $this->processSoapCall("/upnp/control/AVTransport1",

                                               "urn:schemas-upnp-org:service:AVTransport:1",

                                               "GetPositionInfo",

                                               array(

                                                      new SoapParam("0","InstanceID")

                                                     ));

        $xmlParser = xml_parser_create("UTF-8");
        xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parse_into_struct($xmlParser, $positionInfo["TrackMetaData"], $vals, $index);
        xml_parser_free($xmlParser);


        if (isset($index["DC:CREATOR"]) and isset($vals[$index["DC:CREATOR"][0]]["value"])){
          $positionInfo["artist"] = $vals[$index["DC:CREATOR"][0]]["value"];
        }else{
          $positionInfo["artist"] = "";
        }
        if (isset($index["DC:TITLE"]) and isset($vals[$index["DC:TITLE"][0]]["value"])){
          $positionInfo["title"] = $vals[$index["DC:TITLE"][0]]["value"];
        }else{
          $positionInfo["title"] = "";
        }
        if (isset($index["UPNP:ALBUM"]) and isset($vals[$index["UPNP:ALBUM"][0]]["value"])){
          $positionInfo["album"] = $vals[$index["UPNP:ALBUM"][0]]["value"];
        }else{
          $positionInfo["album"] = "";
        }
        if (isset($index["UPNP:ALBUMARTURI"]) and isset($vals[$index["UPNP:ALBUMARTURI"][0]]["value"])){
          if (preg_match('/^https?:\/\/[\w,.,\d,-,:]*\/\S*/',$vals[$index["UPNP:ALBUMARTURI"][0]]["value"]) == 1){
            $positionInfo["albumArtURI"] = $vals[$index["UPNP:ALBUMARTURI"][0]]["value"];
          }else{
            $positionInfo["albumArtURI"] = "http://" . $this->address . ":8080" . $vals[$index["UPNP:ALBUMARTURI"][0]]["value"];
          }
        }else{
          $positionInfo["albumArtURI"] = "";
        }
        if (isset($index["R:ALBUMARTIST"]) and isset($vals[$index["R:ALBUMARTIST"][0]]["value"])){
          $positionInfo["albumArtist"] = $vals[$index["R:ALBUMARTIST"][0]]["value"];
        }else{
          $positionInfo["albumArtist"] = "";
        }
        if (isset($index["R:STREAMCONTENT"]) and isset($vals[$index["R:STREAMCONTENT"][0]]["value"])){
          $positionInfo["streamContent"] = $vals[$index["R:STREAMCONTENT"][0]]["value"];
        }else{
          $positionInfo["streamContent"] = "";
        }

        return $positionInfo;
    }

    //*****************************************************************************
    /* Function: GetTransportSetting_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  
     *   PlayMode (string)            =   
     *   RecQualityMode (string)      =    
     
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetTransportSettings() {

        $returnContent = $this->processSoapCall("/upnp/control/AVTransport1",

                                                "urn:schemas-upnp-org:service:AVTransport:1",

                                                "GetTransportSettings",

                                                array(

                                                       new SoapParam("0","InstanceID")

                                                     ));

        switch ($returnContent["PlayMode"]){
          case "NORMAL":
            return 0;
          case "REPEAT_ALL":
            return 1;
          case "REPEAT_ONE":
            return 2;
          case "SHUFFLE_NOREPEAT":
            return 3;
          case "SHUFFLE":
            return 4;
          case "SHUFFLE_REPEAT_ONE":
            return 5;
          default:
            throw new Exception("Unknown Play Mode: ".$returnContent["CurrentTransportState"]);
        }

    }


    //*****************************************************************************
    /* Function: Next_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Next_AV() {

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Next",

                               array(

                                      new SoapParam("0","InstanceID")

                                    ));

    }
    
    //*****************************************************************************
    /* Function: Pause_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Pause_AV() {

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Pause",

                                array(

                                       new SoapParam("0","InstanceID")

                                     ));

    }
    
    
    //*****************************************************************************
    /* Function: Play_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Play(){

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Play",

                               array(

                                      new SoapParam("0","InstanceID"),

                                      new SoapParam("1","Speed"     )

                                    ));

    }
    
    
    //*****************************************************************************
    /* Function: Previous_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
         
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Previous_AV() {

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Previous",

                                array(

                                       new SoapParam("0","InstanceID")

                                     ));

    }
    
    //*****************************************************************************
    /* Function: Seek_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *   $Unit (string)     =   TRACK_NR
     *                          REL_TIME 
     *                          ABS_TIME
     *                          ABS_COUNT
     *                          REL_COUNT
     *                          X_DLNA_REL_BYTE
     *  $Target (string)    =
     *
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Seek_AV(string $unit, string $target){

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Seek",

                               array(

                                      new SoapParam("0"    ,"InstanceID"),

                                      new SoapParam($unit  ,"Unit"      ),

                                      new SoapParam($target,"Target"    )

                                    ));

    }
    
    
    //*****************************************************************************
    /* Function: SetAVTransportURI_AV($CurrentURI, $CurrentURIMetaData)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *   $CurrentURI (string)           =   
     *   $CurrentURIMetaData (string)    =
     *
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetAVTransportURI_AV(string $CurrentURI, string $CurrentURIMetaData=NULL){

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "SetAVTransportURI",

                               array(

                                      new SoapParam("0"                             ,"InstanceID"        ),

                                      new SoapParam(htmlspecialchars($CurrentURI)   ,"CurrentURI"        ),

                                      new SoapParam($CurrentURIMetaData             ,"CurrentURIMetaData")

                                    ));



    }
    
    
    //*****************************************************************************
    /* Function: SetPlayMode_AV($PlayMode)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            $PlayMode = NORMAL
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetPlayMode_AV(string $PlayMode){

        switch ($PlayMode){
            case 0:
              $PlayMode = "NORMAL";
              break;
            default:
            throw new Exception("Unknown Play Mode: ".$PlayMode);

        }

                $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "SetPlayMode",

                               array(

                                      new SoapParam("0"     ,"InstanceID" ),

                                      new SoapParam($PlayMode ,"NewPlayMode"    )


                                    ));

    }
    
    
    //*****************************************************************************
    /* Function: Stop_AV()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function Stop_AV() {

        $this->processSoapCall("/upnp/control/AVTransport1",

                               "urn:schemas-upnp-org:service:AVTransport:1",

                               "Stop",

                               array(

                                      new SoapParam("0","InstanceID")

                                    ));
    }
    
    
    // ConnectionManager
    
    //*****************************************************************************
    /* Function: GetCurrentConnectionIDs()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetCurrentConnectionIDs(){

        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetCurrentConnectionIDs" ,

                                                array(

            

                                                     ));

    }   
    
    
    //*****************************************************************************
    /* Function: GetCurrentConnectionInfo()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetCurrentConnectionInfo(){

        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetCurrentConnectionIDs" ,

                                                array(

                                                    new SoapParam("0"    ,"InstanceID"   ),

                                                     ));

    }      
    
    
     //*****************************************************************************
    /* Function: GetProtocolInfo()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetProtocolInfo(){

        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetProtocolInfo" ,

                                                array(

            

                                                     ));

    } 
    
    //POST /RCR/control/TestRCRService HTTP/1.1 
    
    //*****************************************************************************
    /* Function: SendKeyCode_AV(integer $KeyCode, string KeyDescription)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *  $KeyCode
     *  KeyDescription
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function SendKeyCode_RCR(integer $KeyCode, string $KeyDescription){
            
        $this->processSoapCall("/RCR/control/TestRCRService",

                                           "urn:samsung.com:service:TestRCRService:1",

                                           "SendKeyCode" ,

                                                array(

                                                    new SoapParam($KeyCode    ,"KeyCode"   ),
                                                    
                                                    new SoapParam($KeyDescription   ,"KeyDescription"   )

                                                     ));

    }     
    
    
// Rendering Control 
    
    
    //*****************************************************************************
    /* Function: SetSharpness_AV($Sharpness)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *  $Sharpness = integer 0 ... 4
      
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
  
    public function SetSharpness_AV(integer $Sharpness){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetSharpness",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($Sharpness,"DesiredSharpness")

                                    ));

    }
    
    
    
    
    //*****************************************************************************
    /* Function: SetContrast_AV($Contrast)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *  $Contrast = integer 0 ... 4
      
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetContrast_AV(integer $Contrast){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetContrast",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($Contrast,"DesiredContrast")

                                    ));

    }
 
    //*****************************************************************************
    /* Function: SetColorTemperature_AV($ColorTemperature)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *  $ColorTemperature = integer 0 ... 4
      
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetColorTemperature_AV(integer $ColorTemperature){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetColorTemperature",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($ColorTemperature,"DesiredColorTemperature")

                                    ));

    }
    
    //*****************************************************************************
    /* Function: SetBrightness_RC($Brightness)
    ...............................................................................
     Stellt die Helligkeit auf Wert  $Brightness
    ...............................................................................
    Parameters: 
     *  $Brightness (integer) =  0 ... 4
    --------------------------------------------------------------------------------
    Returns:  none
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/   
    public function SetBrightness_RC(integer $Brightness){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetBrightness",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($Brightness,"DesiredBrightness")

                                    ));

    }
    
    //*****************************************************************************
    /* Function: SetVolume_RC($volume)
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
     *  $volume = integer 0 ... 4
      
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function SetVolume_RC(integer $volume, $channel = 'Master'){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetVolume",

                               array(

                                      new SoapParam("0"     ,"InstanceID"   ),

                                      new SoapParam($channel,"Channel"      ),

                                      new SoapParam($volume ,"DesiredVolume")

                                    ));

    }
    
    //*****************************************************************************
    /* Function: SetVolume_MTVA($volume)
    ...............................................................................
    Stellt die Lautstärke auf Wert  $volume
    ...............................................................................
    Parameters: 
     *  $volume = integer 0 ... 16
    --------------------------------------------------------------------------------
    Returns:  none
   --------------------------------------------------------------------------------
    Status: 18.07.2018 - OK 
    //////////////////////////////////////////////////////////////////////////////*/    
    public function SetVolume_MTVA(integer $volume){

        $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "SetVolume",

                               array(

                                      new SoapParam($volume ,"Volume")

                                    ));

    } 
    
    
    //*****************************************************************************
    /* Function: SetMainTVChannel_MTVA($Channel, $AntennaMode = 2, $ChannelListType = '0x01', $SatelliteID = 0)
    ...............................................................................
	TV Channel umschalten
    ...............................................................................
    Parameters: 
     *   $Channel   = string 
     *              = <Channel><ChType>CDTV</ChType><MajorCh>1</MajorCh><MinorCh>65534</MinorCh><PTC>28</PTC><ProgNum>11100</ProgNum></Channel>
     *   $AntennaMode = 0...4  - Antenna Mode = 2
     *   $ChannelListType = '0x01'  -  (von GetChannelListUrl auslesen)
     *   $SatelliteID = 0  - SateliteID nicht verwendet bleibt auf = 0
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  17.7.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/   
  public function SetMainTVChannel_MTVA(string $Channel, $AntennaMode = 2, $ChannelListType = '0x01',  $SatelliteID = 0) {

        $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "SetMainTVChannel",

                               array(

                                      new SoapParam($AntennaMode    ,"AntennaMode"   ),

                                      new SoapParam($ChannelListType , "ChannelListType"),

                                      new SoapParam($SatelliteID    ,"SatelliteID"   ),

                                      new SoapParam($Channel , "Channel")
                                    ));

  }
    
    //*****************************************************************************
    /* Function: SetMute_RC($mute)
    ...............................................................................
	TV stumm schalten
    ...............................................................................
    Parameters: 
     *   $mute   = boolean true/false 

    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/      
    public function SetMute_RC($mute) {
        if($mute){
          $mute = "1";
        }else{
          $mute = "0";
        }
        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetMute",

                               array(

                                      new SoapParam("0"     ,"InstanceID" ),

                                      new SoapParam("Master","Channel"    ),

                                      new SoapParam($mute   ,"DesiredMute")

                                    ));
  }

    
    //*****************************************************************************
    /* Function: GetTransportInfo_AV()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
     

    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentTransportState>      NO_MEDIA_PRESENT
     * <CurrentTransportStatus>     OK
     * <CurrentSpeed>               1
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetTransportInfo_AV() {
        $returnContent = $this->processSoapCall("/upnp/control/AVTransport1",

                                            "urn:schemas-upnp-org:service:AVTransport:1",

                                            "GetTransportInfo",

                                            array(

                                                   new SoapParam("0","InstanceID")

                                                 ));

        switch ($returnContent["CurrentTransportState"]){
            case "PLAYING":
                return 1;
                break;
            case "PAUSED_PLAYBACK":
                return 2;
                break;
            case "STOPPED":
                return 3;
                break;
            case "NO_MEDIA_PRESENT":
                return 4;
                break;
            case "TRANSITIONING":
                return 5;
                break;
            default:
                throw new Exception("Unknown Transport State: ".$returnContent["CurrentTransportState"]); 
        }
    }
    
    
    //*****************************************************************************
    /* Function: GetMute_RC()
    ...............................................................................
	Gibt zurück ob Mute 0 oder 1 ist 
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentMute>    0
    --------------------------------------------------------------------------------
    Status:  18.07.2018
    //////////////////////////////////////////////////////////////////////////////*/     
    public function GetMute_RC(){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetMute",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID"),

                                                  new SoapParam("Master","Channel"   )

                                                ));

    }
    
    
    
    //*****************************************************************************
    /* Function: GetVolume_RC($channel = 'Master')
    ...............................................................................
	 Gibt den Lautstärke Wert von Master Kanal zurück
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentVolume>  2
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetVolume_RC($channel = 'Master'){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetVolume",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID"),

                                                  new SoapParam($channel,"Channel"   )

                                                ));

    }
    
    
    //*****************************************************************************
    /* Function: GetColorTemperature_RC()
    ...............................................................................
	 Holt den aktuellen ColorTemperatur Wert
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentColorTemperature>    3
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetColorTemperature_RC(){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetColorTemperature",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID") 

                                                ));

    } 
    
    
     //*****************************************************************************
    /* Function: GetColorTemperature_RC()
    ...............................................................................
	 Gibt den aktuellen Sharpness Wert zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentSharpness>   60
    --------------------------------------------------------------------------------
    Status:  18.07.2018
    //////////////////////////////////////////////////////////////////////////////*/   
     public function GetSharpness_RC(){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetSharpness",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID") 

                                                ));

    }
    
    
     //*****************************************************************************
    /* Function: GetContrast_RC()
    ...............................................................................
	 Holt den aktuellen Kontrast Wert
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentContrast>    80
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/ 
    public function GetContrast_RC(){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetContrast",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID") 

                                                ));

    }
    
    
    
     //*****************************************************************************
    /* Function: GetBrightness_RC()
    ...............................................................................
	Holt den aktuellen Brightness Wert 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentBrightness>  50
    --------------------------------------------------------------------------------
    Status:  18.07.2018
    //////////////////////////////////////////////////////////////////////////////*/   
    public function GetBrightness_RC(){

        return (int)$this->processSoapCall("/upnp/control/RenderingControl1",

                                           "urn:schemas-upnp-org:service:RenderingControl:1",

                                           "GetBrightness",

                                           array(

                                                  new SoapParam("0"     ,"InstanceID") 

                                                ));

    } 
    
    
     //*****************************************************************************
    /* Function: GetCurrentConnection_CM()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <ConnectionIDs>  0

    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/   
    public function GetCurrentConnection_CM() {

        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetCurrentConnection" ,

                                                array(

                                                       new SoapParam("0","InstanceID")

                                                     ));

    }
    

    
    

    
    
 
     
    //*****************************************************************************
    /* Function: GetAllProgramInformationURL_MTVA($Channel, $AntennaMode)
    ...............................................................................
    Übergibt die Adresse für das Fernsehprogramms des Kanals x
    ...............................................................................
    Parameters: 
     * $Channel     = "<ChType>CDTV</ChType><MajorCh>301</MajorCh><MinorCh>65534</MinorCh><PTC>26</PTC><ProgNum>28106</ProgNum>"
     * $AntennaMode (integer) = 2
    
    --------------------------------------------------------------------------------
    Returns:  (array)
     * ['Result']                    =>    OK // NOTOK_InvalidCh
     * ['AllProgramInformationURL']   => http://192.168.178.35:9090/BinaryBlob/1/AllProgInfo.dat
    --------------------------------------------------------------------------------
    Status:  17.7.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/ 
    public function GetAllProgramInformationURL_MTVA(string $Channel, integer $AntennaMode){
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetAllProgramInformationURL",

                               array(

                                      new SoapParam($AntennaMode, "AntennaMode"),

                                      new SoapParam($Channel, "Channel"    )

                                    ));
    } 
    
    //*****************************************************************************
    /* Function: GetAllProgramInformationURL_MTVA($Channel, $AntennaMode)
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
     * $Channel
     * $AntennaMode
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>         OK
     *  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    
    
    
    
    
    
    
    //*****************************************************************************
    /* Function: AddSchedule()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    
    //*****************************************************************************
    /* Function: DeleteChannelList()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    //*****************************************************************************
    /* Function: ChangeSchedule()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
   
    
    //*****************************************************************************
    /* Function: DeleteChannelListPIN()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    //*****************************************************************************
    /* Function: DeleteRecordItem()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
  
    
    //*****************************************************************************
    /* Function: DeleteSchedule()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    //*****************************************************************************
    /* Function: EditChannelNumber()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    

       //*****************************************************************************
    /* Function: EditSourceName()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/  
    
    
    
    //*****************************************************************************
    /* Function: EnforceAKE()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    
    //*****************************************************************************
    /* Function: GetBannerInformation()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/   

    
    
    
    //*****************************************************************************
    /* Function: GetChannelLockInformation()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 

    
    //*****************************************************************************
    /* Function: GetDetailChannelInformation()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 


    //*****************************************************************************
    /* Function: GetDetailProgramInformation()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 
    
    
    //*****************************************************************************
    /* Function: GetRegionalVariantList_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/ 


    //*****************************************************************************
    /* Function: GetDetailProgramInformation()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
    
    
 
    //*****************************************************************************
    /* Function: ModifyChannelName_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
    
       
 
    //*****************************************************************************
    /* Function: ModifyFavoriteChannel_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
    
     
    
    //*****************************************************************************
    /* Function: PlayRecoordItem_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
    
    //*****************************************************************************
    /* Function: RecordSateliteChannel_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
    
    //*****************************************************************************
    /* Function: RunApp_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
    
    //*****************************************************************************
    /* Function: RunBrowser_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
    //*****************************************************************************
    /* Function: RunWidget_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
         
    //*****************************************************************************
    /* Function: SendRoomEQData_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
    
          
    //*****************************************************************************
    /* Function: SetAntennaMode_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
       
           
    //*****************************************************************************
    /* Function: SetAVOff_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
   
    //*****************************************************************************
    /* Function: SetChannelListSort_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
     
    //*****************************************************************************
    /* Function: SetChannelLock_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
      
    //*****************************************************************************
    /* Function: SetCloneView_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
       
    
    //*****************************************************************************
    /* Function: SetMainTVChannelPIN_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
      
    
    //*****************************************************************************
    /* Function: SetMainTVSource_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
     
    //*****************************************************************************
    /* Function: SetMute_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
        
    
    //*****************************************************************************
    /* Function: SetRecordDuration_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
         
    //*****************************************************************************
    /* Function: SetRegionalVariant_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
   

    //*****************************************************************************
    /* Function: SetRoomEQTest_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
   

    //*****************************************************************************
    /* Function: SetRoomEQTest_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
   
    //*****************************************************************************
    /* Function: StartCloneView_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
  
   //*****************************************************************************
    /* Function: StartExtSourceView_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
        
    //*****************************************************************************
    /* Function: StartInstantRecording_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   
    //*****************************************************************************
    /* Function: StartIperfClient_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   

    //*****************************************************************************
    /* Function: StartIperfServer_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   


    //*****************************************************************************
    /* Function: StartSecondTVView_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   


    //*****************************************************************************
    /* Function: StopIperf_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   


    //*****************************************************************************
    /* Function: StopRecord_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   


    //*****************************************************************************
    /* Function: StopView_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   
    //*****************************************************************************
    /* Function: SyncRemoteControlPannel_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   


    //*****************************************************************************
    /* Function: AddMessage_RCR()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   

    //*****************************************************************************
    /* Function: RemoveMessage_RCR()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   

    
    //*****************************************************************************
    /* Function: ListPresets_RC()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   

    //*****************************************************************************
    /* Function: SelectPreset_RC()
    ...............................................................................
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
   
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    



    
    
    //*****************************************************************************
    /* Function: GetChannelListURL_MTVA()
    ...............................................................................
     * gibt ein URL mit der Liste aller Sender zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [ChannelListVersion] => 190
     * [SupportChannelList] => 0x01Number0x03Number0x04Number
     * [ChannelListURL] => http://192.168.178.35:9090/BinaryBlob/3/ChannelList.dat
     * [ChannelListType] => 0x01
     * [Sort] => Number
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/ 
    public function GetChannelListURL_MTVA(){
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetChannelListURL",

                               array(

                                    ));
    } 
    
    
    //*****************************************************************************
    /* Function: GetCurrentExternalSource_MTVA()
    ...............................................................................
     * gibt die aktuelle Abspiel Quelle aus
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [CurrentExternalSource] => TV
     * [ID] => 0
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetCurrentExternalSource_MTVA(){
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentExternalSource",

                               array(

                                    ));
    } 
    
    
    
    
    
    //*****************************************************************************
    /* Function: CheckPin_MTVA($PIN)
    ...............................................................................
     * Überprüft ob der übergebene PIN Code richtig ist.
    ...............................................................................
    Parameters: 
     * $PIN (string)
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>     NOTOK_InvalidPIN   // OK
     *
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/   
    public function CheckPINn_MTVA(string $PIN){
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "CheckPIN",

                               array(

                                      new SoapParam($PIN, "PIN")

                                    ));
    } 
    
    
    
    //*****************************************************************************
    /* Function: GetCurrentMainTVChannel_MTVA()
    ...............................................................................
     * gibt den aktuellen Fernseh Kanal zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
            $output['Result']   = OK
            $output['ChType']   = CDTV
            $output['MAJORCH']  = 305
            $output['MINORCH']  = 65534
            $output['PTC']      = 1
            $output['PROGNUM']  = 12103
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetCurrentMainTVChannel_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentMainTVChannel",

                               array(

                                    ));
        // $result = array  [Result] = (string)  
        //                  ['CurrentChannel'] = (xml)
        $xml = $result['CurrentChannel'];
        $xmlParser = xml_parser_create("UTF-8");
        xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parse_into_struct($xmlParser, $xml, $vals, $index);
        xml_parser_free($xmlParser);
            $output['Result'] = $result['Result'];
            $output['ChType'] = $vals[1]['value'];
            $output['MAJORCH'] = $vals[2]['value'];
            $output['MINORCH'] = $vals[3]['value'];
            $output['PTC'] = $vals[4]['value'];
            $output['PROGNUM'] = $vals[5]['value'];
            
        return $output;    
    }  
    
    
    
    //*****************************************************************************
    /* Function: GetCurrentProgramInformationURL_MTVA()
    ...............................................................................
     * gibt die URL der Sender Programmliste
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] (string)            => OK
     * [CurrentProgInfoURL] (xml)   => http://192.168.178.35:9090/BinaryBlob/0/CurrentProgInfo.dat
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetCurrentProgramInformationURL_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentProgramInformationURL",

                               array(

                                    ));

            
        return $result;    
    }  
    
   
    //*****************************************************************************
    /* Function: GetCurrentTime_MTVA ()
    ...............................................................................
     * gibt das aktuelle Datum und Uhrzeit
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] (string)     => OK
     * [CurrentTime] (xml)   =>  2018-07-17T15:56:14
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetCurrentTime(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentTime",

                               array(

                                    ));

            
        return $result;    
    } 
    
    
    
    //*****************************************************************************
    /* Function: GetDTVInformation_MTVA ()
    ...............................................................................
     * gibt  DTV Informationen zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
            $output['Result']                           = OK
            $output['SUPPORTANTMODE']                   = 1,2,3
            $output['SUPPORTCHSORT']                    = Yes
            $output['SUPPORTCLONEVIEW']                 = Yes
            $output['SUPPORTSECONDTVVIEW']              = Yes
            $output['SUPPORTEXTSOURCEVIEW']             = Yes
            $output['SUPPORTDTV']                       = Yes
            $output['TUNERCOUNT']                       = 1
            $output['SUPPORTTVVERSION']                 = 2011
            $output['SUPPORTCHANNELLOCK']               = No
            $output['SUPPORTCHANNELINFO']               = Yes
            $output['SUPPORTCHANNELDELETE']             = Yes
            $output['SUPPORTEDITNUMMODE']['NUMMODE']    = IGITAL_SWAP
            $output['SUPPORTEDITNUMMODE']['MINVALUE']   =  0
            $output['SUPPORTEDITNUMMODE']['MAXVALUE']   = 9999
            $output['EDITNUMMODE']['NUMMODE']           = ANALOG_INSERT
            $output['EDITNUMMODE']['MINVALUE']          = 0
            $output['EDITNUMMODE']['MAXVALUE']          = 99
            $output['SUPPORTREGIONALVARIANT']           = No
            $output['SUPPORTSTREAM']['CONTAINER']       = MPEG2 
            $output['SUPPORTSTREAM']['VIDEOFORMAT']     = MPEG4SP
            $output['SUPPORTSTREAM']['AUDIOFORMAT']     = MP3
            $output['SUPPORTSTREAM']['XRESOLUTION']     = 672
            $output['SUPPORTSTREAM']['YRESOLUTION']     = 544 
            $output['SUPPORTSTREAM']['AUDIOSAMPLINGRATE']   = 48000 
            $output['SUPPORTSTREAM']['AUDIOCHANNELS']   = 2
            $output['SUPPORTPVR']                       =  Yes
            $output['TARGETLOCATION']                   =  TARGET_LOCATION_PANEURO
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetDTVInformation_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetDTVInformation",

                               array(

                                    ));
        
        // $result = array  [Result] = (string)  
        //                  ['DTVInformation'] = (xml)
        $xml = $result['DTVInformation'];
        $xmlParser = xml_parser_create("UTF-8");
        xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parse_into_struct($xmlParser, $xml, $vals, $index);
        xml_parser_free($xmlParser);
		 
		$output['Result'] = $result['Result'];
		$output['SUPPORTANTMODE'] = $vals[1]['value'];
		$output['SUPPORTCHSORT'] = $vals[2]['value'];
		$output['SUPPORTCLONEVIEW'] = $vals[3]['value'];
		$output['SUPPORTSECONDTVVIEW'] = $vals[4]['value'];
		$output['SUPPORTEXTSOURCEVIEW'] = $vals[5]['value'];
		$output['SUPPORTDTV'] = $vals[6]['value'];
		$output['TUNERCOUNT'] = $vals[7]['value'];
		
		$output['SUPPORTTVVERSION'] =  $vals[8]['value'];
		$output['SUPPORTCHANNELLOCK'] = $vals[9]['value'];
		$output['SUPPORTCHANNELINFO'] = $vals[10]['value'];
		$output['SUPPORTCHANNELDELETE'] = $vals[11]['value'];
		$output['SUPPORTEDITNUMMODE']['NUMMODE'] = $vals[14]['value'];
		$output['SUPPORTEDITNUMMODE']['MINVALUE'] = $vals[15]['value'];
		$output['SUPPORTEDITNUMMODE']['MAXVALUE'] = $vals[16]['value'];	
		
		$output['EDITNUMMODE']['NUMMODE'] = $vals[19]['value'];
		$output['EDITNUMMODE']['MINVALUE']  = $vals[20]['value'];
		$output['EDITNUMMODE']['MAXVALUE']  = $vals[21]['value'];
		$output['SUPPORTREGIONALVARIANT'] = $vals[24]['value'];
		$output['SUPPORTSTREAM']['CONTAINER']  = $vals[26]['value'];
		$output['SUPPORTSTREAM']['VIDEOFORMAT']  = $vals[27]['value'];
		$output['SUPPORTSTREAM']['AUDIOFORMAT']  = $vals[28]['value'];
		$output['SUPPORTSTREAM']['XRESOLUTION']  = $vals[29]['value'];
		$output['SUPPORTSTREAM']['YRESOLUTION']  = $vals[30]['value'];
		$output['SUPPORTSTREAM']['AUDIOSAMPLINGRATE']  = $vals[31]['value'];
		$output['SUPPORTSTREAM']['AUDIOCHANNELS']  = $vals[32]['value'];
		
		$output['SUPPORTPVR']  = $vals[34]['value'];
		$output['TARGETLOCATION']  = $vals[35]['value'];
        return $output;    
    }  
    
    
    //*****************************************************************************
    /* Function: GetMuteStatus_MTVA ()
    ...............................................................................
     * prüft ob Mute aktiv ist
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [MuteStatus] => Disable
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetMuteStatus_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetMuteStatus",

                               array(

                                    ));

         return $result;    
    }     
    
    //*****************************************************************************
    /* Function: GetNetworkInformation_MTVA ()
    ...............................................................................
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [[NetworkInformation] ] => BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAxOTIuMTY4LjE3OC4xAAAAAAAAAAA=
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetNetworkInformation_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetNetworkInformation",

                               array(

                                    ));

         return $result;    
    }     
    
    
    //*****************************************************************************
    /* Function: GetRecordChannel_MTVA ()
    ...............................................................................
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => NOTOK
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetRecordChannel_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetRecordChannel",

                               array(

                                    ));

         return $result;    
    }      
    
    
    
     //*****************************************************************************
    /* Function: GetScheduleListURL_MTVA ()
    ...............................................................................
     *  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [GetScheduleListURL] =>  http://192.168.178.35:9090/BinaryBlob/2/ScheduleList.dat
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetScheduleListURL_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetScheduleListURL",

                               array(

                                    ));

         return $result;    
    }   
    
    
     //*****************************************************************************
    /* Function: GetSourceList_MTVA()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>OK
     * <SourceList
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function GetSourceList_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetSourceList",

                               array(

                                    ));
    /*    
        //Ausgabe: array [Result] und [SourceList].
        $xml = $result['SourceList'];
                $xmlParser = xml_parser_create("UTF-8");
                xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");
                xml_parse_into_struct($xmlParser, $xml, $vals, $index);
                xml_parser_free($xmlParser);
        $output['Result'] = $result['Result'] ;
        $output['CURRENTSOURCETYPE'] = $vals[1]['value'];    
        $output['ID'] = $vals[2]['value'];  
        $anzahl = count($vals);
        for ($index1 = 0; $index1 < count($vals); $index1++) {
            

        $output['SOURCE1'][$index1]['SOURCETYPE'] = $vals[4*$index1]['value'];    
        $output['SOURCE1'][$index1]['ID'] = $vals[5*$index1]['value'];  
        $output['SOURCE1'][$index1]['EDITABLE'] = $vals[6*$index1]['value'];    
        $output['SOURCE1'][$index1]['DEVICENAME'] = $vals[7*$index1]['value'];    
        $output['SOURCE1'][$index1]['CONNECTED'] = $vals[8*$index1]['value'];    
        $output['SOURCE1'][$index1]['SUPPORTVIEW'] = $vals[9*$index1]['value'];
        }
    */    
        
         return $result;    
    }       
    
    
    
    
     //*****************************************************************************
    /* Function: GetVolume_MTVA ()
    ...............................................................................
     *  Gibt den Volume Wert zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [Volume] => 8
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetVolume_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetVolume",

                               array(

                                    ));

         return $result;    
    }    
    
    
     //*****************************************************************************
    /* Function: GetWatchingInformation_MTVA()
    ...............................................................................
	Gibt die aktuelle Sendung zurück 
    ...............................................................................
    Parameters: none
    
    --------------------------------------------------------------------------------
    Returns:  (array)
     * ['Result´]   = OK
     * ['TVMode´]   = Tuner
     * ['WatchingInformation'] = Hilf mir! Jung, pleite, verzweifelt... on RTL2 (01:0PM~02:00PM)
     * 
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/   
    public function GetWatchingInformation_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetWatchingInformation",

                               array(

                                    ));

         return $result;   
    }    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
 
    //*****************************************************************************
    /* Function: processSoapCall($path,$uri,$action,$parameter)
    ...............................................................................
    UPNP SOAP ACTION
    ...............................................................................
    Parameters: 
     * $path
     * $uri
     * $action
     * $parameter
     *
    --------------------------------------------------------------------------------
    Returns:  
        none.
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/
    protected function processSoapCall($path,$uri,$action,$parameter) {

    try{

      $client     = new SoapClient(null, array("location"   => "http://"."192.168.178.35:52235".$path,

                                               "uri"        => $uri,

                                               "trace"      => true ));


      return $client->__soapCall($action,$parameter);

    }catch(Exception $e){

      $faultstring = $e->faultstring;

      $faultcode   = $e->faultcode;

      if(isset($e->detail->UPnPError->errorCode)){

        $errorCode   = $e->detail->UPnPError->errorCode;

        throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode." ".$errorCode." (".$this->resolveErrorCode($path,$errorCode).")");

      }else{

        throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode);

      }

    }

  }



    //*****************************************************************************
    /* Function: resolveErrorCode($path,$errorCode)
    ...............................................................................
    UPNP SOAP Fehlerbehandlung
    ...............................................................................
    Parameters: 
     * $path
     * $errorCode
     *
    --------------------------------------------------------------------------------
    Returns:  
        none.
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/

  protected function resolveErrorCode($path,$errorCode) {

   $errorList = array( "/AVTransport/ctrl"      => array(

                                                                           "701" => "ERROR_AV_UPNP_AVT_INVALID_TRANSITION",

                                                                           "702" => "ERROR_AV_UPNP_AVT_NO_CONTENTS",

                                                                           "703" => "ERROR_AV_UPNP_AVT_READ_ERROR",

                                                                           "704" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_PLAY_FORMAT",

                                                                           "705" => "ERROR_AV_UPNP_AVT_TRANSPORT_LOCKED",

                                                                           "706" => "ERROR_AV_UPNP_AVT_WRITE_ERROR",

                                                                           "707" => "ERROR_AV_UPNP_AVT_PROTECTED_MEDIA",

                                                                           "708" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_REC_FORMAT",

                                                                           "709" => "ERROR_AV_UPNP_AVT_FULL_MEDIA",

                                                                           "710" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_SEEK_MODE",

                                                                           "711" => "ERROR_AV_UPNP_AVT_ILLEGAL_SEEK_TARGET",

                                                                           "712" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_PLAY_MODE",

                                                                           "713" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_REC_QUALITY",

                                                                           "714" => "ERROR_AV_UPNP_AVT_ILLEGAL_MIME",

                                                                           "715" => "ERROR_AV_UPNP_AVT_CONTENT_BUSY",

                                                                           "716" => "ERROR_AV_UPNP_AVT_RESOURCE_NOT_FOUND",

                                                                           "717" => "ERROR_AV_UPNP_AVT_UNSUPPORTED_PLAY_SPEED",

                                                                           "718" => "ERROR_AV_UPNP_AVT_INVALID_INSTANCE_ID"

                                                                         ),

                       "/RenderingControl/ctrl" => array(

                                                                           "701" => "ERROR_AV_UPNP_RC_INVALID_PRESET_NAME",

                                                                           "702" => "ERROR_AV_UPNP_RC_INVALID_INSTANCE_ID"

                                                                         ),

                       "/ContentDirectory/ctrl"   => array(

                                                                           "701" => "ERROR_AV_UPNP_CD_NO_SUCH_OBJECT",

                                                                           "702" => "ERROR_AV_UPNP_CD_INVALID_CURRENTTAGVALUE",

                                                                           "703" => "ERROR_AV_UPNP_CD_INVALID_NEWTAGVALUE",

                                                                           "704" => "ERROR_AV_UPNP_CD_REQUIRED_TAG_DELETE",

                                                                           "705" => "ERROR_AV_UPNP_CD_READONLY_TAG_UPDATE",

                                                                           "706" => "ERROR_AV_UPNP_CD_PARAMETER_NUM_MISMATCH",

                                                                           "708" => "ERROR_AV_UPNP_CD_BAD_SEARCH_CRITERIA",

                                                                           "709" => "ERROR_AV_UPNP_CD_BAD_SORT_CRITERIA",

                                                                           "710" => "ERROR_AV_UPNP_CD_NO_SUCH_CONTAINER",

                                                                           "711" => "ERROR_AV_UPNP_CD_RESTRICTED_OBJECT",

                                                                           "712" => "ERROR_AV_UPNP_CD_BAD_METADATA",

                                                                           "713" => "ERROR_AV_UPNP_CD_RESTRICTED_PARENT_OBJECT",

                                                                           "714" => "ERROR_AV_UPNP_CD_NO_SUCH_SOURCE_RESOURCE",

                                                                           "715" => "ERROR_AV_UPNP_CD_SOURCE_RESOURCE_ACCESS_DENIED",

                                                                           "716" => "ERROR_AV_UPNP_CD_TRANSFER_BUSY",

                                                                           "717" => "ERROR_AV_UPNP_CD_NO_SUCH_FILE_TRANSFER",

                                                                           "718" => "ERROR_AV_UPNP_CD_NO_SUCH_DESTINATION_RESOURCE",

                                                                           "719" => "ERROR_AV_UPNP_CD_DESTINATION_RESOURCE_ACCESS_DENIED",

                                                                           "720" => "ERROR_AV_UPNP_CD_REQUEST_FAILED"

                                                                         ) ); 



    if (isset($errorList[$path][$errorCode])){

      return $errorList[$path][$errorCode] ;

    }else{

      return "UNKNOWN";

    }

  }
   
    
    
    
}
