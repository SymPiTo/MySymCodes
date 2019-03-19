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
    UPNP Transport Action
    ...............................................................................
    Parameters: 
    --------------------------------------------------------------------------------
    Returns:      none.
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
    public function GetTransportSettings_AV() {
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
    Parameters: none
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
    Parameters: none
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
    Parameters: none
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
     stopped Stream 
    ...............................................................................
    Parameters: none
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
    
    /* ****************************************************************************
    
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  25.09.2018 - OK
    **************************************************************************** */
    public function GetCurrentConnectionIDs(){

        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetCurrentConnectionIDs" ,

                                                array(

            

                                                     ));

    }   
    
    
    //*****************************************************************************
    /* Function: GetCurrentConnectionInfo_CM()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: 
            
    --------------------------------------------------------------------------------
    Returns:  

    --------------------------------------------------------------------------------
    Status:  25.09.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetCurrentConnectionInfo_CM(){
        return (int)$this->processSoapCall("/upnp/control/ConnectionManager1",

                                           "urn:schemas-upnp-org:service:ConnectionManager:1",

                                           "GetCurrentConnectionInfo" ,

                                                array(

                                                    new SoapParam("0"    ,"ConnectionID"   ),

                                                     ));

    }      
    
    
     //*****************************************************************************
    /* Function: GetProtocolInfo_CM()
    ...............................................................................
    UPNP  
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  
     * Source
     * Sink
    --------------------------------------------------------------------------------
    Status:  25.09.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetProtocolInfo_CM(){
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
    /* Function: SetSharpness_RC($Sharpness)
    ...............................................................................
     Stellt die Schärfe auf Wert $Sharpness ein.
    ...............................................................................
    Parameters: 
     *  $Sharpness = integer 0 ... 4
    --------------------------------------------------------------------------------
    Returns:  none
   --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetSharpness_RC(integer $Sharpness){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetSharpness",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($Sharpness,"DesiredSharpness")

                                    ));

    }
    
    
    
    
    //*****************************************************************************
    /* Function: SetContrast_RC($Contrast)
    ...............................................................................
     Stellt den Kontrast Wert auf Wert  $Contrast ein.
    ...............................................................................
    Parameters: 
     *  $Contrast(integer)  = 0 ... 4
    --------------------------------------------------------------------------------
    Returns:  none
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetContrast_RC(int $Contrast){

        $this->processSoapCall("/upnp/control/RenderingControl1",

                               "urn:schemas-upnp-org:service:RenderingControl:1",

                               "SetContrast",

                               array(

                                      new SoapParam("0"    ,"InstanceID"   ),

                                      new SoapParam($Contrast,"DesiredContrast")

                                    ));

    }
 
    //*****************************************************************************
    /* Function: SetColorTemperature_RC($ColorTemperature)
    ...............................................................................
     Stellt den Farbwert auf Wert  $ColorTemperature  ein
    ...............................................................................
    Parameters: 
     *  $ColorTemperature (integer)  = 0 ... 4
    --------------------------------------------------------------------------------
    Returns:  none
    --------------------------------------------------------------------------------
    Status: 18.07.2018 - OK 
    //////////////////////////////////////////////////////////////////////////////*/
    public function SetColorTemperature_RC(int $ColorTemperature){

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
    public function SetBrightness_RC(int $Brightness){

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
     Stellt die Lautstärke auf Wert   $volume ein.
    ...............................................................................
    Parameters: 
     *  $volume (integer) =  0 ... 16
    --------------------------------------------------------------------------------
    Returns: none
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK  RASPI - OK
    //////////////////////////////////////////////////////////////////////////////*/    
    public function SetVolume_RC(int $volume, string $channel = 'Master'){
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
    Status:  
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
    Status:  17.7.2018 - OK  RASPI - geht nicht
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
    Returns:  none
    --------------------------------------------------------------------------------
    Status:  18.07.2018 - OK  RASPI - OK
    //////////////////////////////////////////////////////////////////////////////*/      
    public function SetMute_RC(bool $mute) {
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
    Status:  Raspi - ok
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
    Status:  18.07.2018 - OK - RASPI OK
    //////////////////////////////////////////////////////////////////////////////*/
    public function GetVolume_RC(string $channel = 'Master'){

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
    Status:  18.07.2018 - OK - raspi ok
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
    Status:  25.09.2018 - OK
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
    Status:  3.2.2019 - NOK   geht nicht Raspi
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
    public function GetAllProgramInformationURL_MTVA(string $Channel, int $AntennaMode){
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
     * <Channel><ChType>CDTV</ChType><MajorCh>1</MajorCh><MinorCh>65534</MinorCh><PTC>28</PTC><ProgNum>11100</ProgNum></Channel>
     * $AntennaMode = 2
    --------------------------------------------------------------------------------
    Returns:  Array
                    [Result] => OK
                    [Lock] => Disable    
    --------------------------------------------------------------------------------
    Status:  3.2.2019 - OK
    //////////////////////////////////////////////////////////////////////////////*/ 
    public function GetChannelLockInformation_MTVA(string $channel, int $AntennaMode = 2) {
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetChannelLockInformation",

                               array(

                                        new SoapParam($channel     ,"Channel"),

                                        new SoapParam($AntennaMode ,"AntennaMode")

                                    ));
    }  
    
    
    
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
    /* Function: RunBrowser_MTVA($URL)
    ...............................................................................
    ...............................................................................
    Parameters: 
     * $URL = www.google.de 
     *
    --------------------------------------------------------------------------------
    Returns:  OK
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
    public function RunBrowser_MTVA(string $URL){
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "RunBrowser",

                               array(

                                      new SoapParam($URL, "BrowserURL"    )

                                    ));
    }
    
    
    
    
    
    
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
    public function RunWidget_MTVA() {
        
    }
         
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
    public function SendRoomEQData_MTVA() {
        
    }   
    
          
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
    public function SetAntennaMode_MTVA() {
        
    }     
       
           
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
    public function SetAVOff_MTVA() {
        
    }
   
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
    public function SetChannelListSort_MTVA() {
        
    }
     
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
    public function SetChannelLock_MTVA() {
        
    }
      
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
    public function SetCloneView_MTVA() {
        
    }   
       
    
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
     public function SetMainTVChannelPIN_MTVA() {
        
    }
      
    
    //*****************************************************************************
    /* Function: SetMainTVSource_MTVA()
    ...............................................................................
    ...............................................................................
    Parameters:  
     * Source: TV, SCART1, SCART2, PC, HDMI1/DVI, HDMI2, HDMI3, HDMI4, AV, COMPONENT, USB, DLNA
     *   
     * ID:     0,   75,     76,    67,   71,       72,    73,     74,  55,     63,     -1,  -1
    --------------------------------------------------------------------------------
    Returns:  
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
     protected function SetMainTVSource_MTVA(string $Source, $ID) {
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "SetMainTVSource",

                               array(

                                      new SoapParam($Source, "Source"),
                                   
                                      new SoapParam($ID, "ID"),

                                      new SoapParam("-1", "UiID")

                                    ));
    }
    
    
    //*****************************************************************************
    /* Function: SetMute_MTVA($mute)
    ...............................................................................
    ...............................................................................
    Parameters: 
     * $mute = "Enable"    // "Disable"
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>OK</Result>
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/     
     public function SetMute_MTVA(string $mute) {
        return $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "SetMute",

                               array(

                                      new SoapParam($mute, "Mute"    )

                                    ));
    }  
        
    
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
     public function SetRecordDuration_MTVA() {
        
    }  
         
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
     public function SetRegionalVariant_MTVA() {
        
    }      
   

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
     public function SetRoomEQTest_MTVA() {
        
    }      
   

   
   
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
    public function StartCloneView_MTVA() {
        
    }       
  
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
    public function StartExtSourceView_MTVA() {
        
    } 
    
    
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
    public function StartInstantRecording_MTVA() {
        
    } 
    
    
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
    public function StartIperfClient_MTVA() {
        
    } 

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
    public function StartIperfServer_MTVA() {
        
    } 


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
    public function StartSecondTVView_MTVA() {
        
    } 


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
    public function StopIperf_MTVA() {
        
    } 


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
    public function StopRecord_MTVA() {
        
    } 


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
    public function StopView_MTVA() {
        
    } 
    
    
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
    public function SyncRemoteControlPannel_MTVA() {
        
    } 


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
     public function AddMessage_RCR() {
        
    } 

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
     public function RemoveMessage_RCR() {
        
    } 

    
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
     public function ListPresets_RC() {
        
    }

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
     public function SelectPreset_RC() {
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    



    
    
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
    private function GetCurrentExternalSource_MTVA(){
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
     * <Channel><ChType>CDTV</ChType><MajorCh>1</MajorCh><MinorCh>65534</MinorCh><PTC>28</PTC><ProgNum>11100</ProgNum></Channel> 
     *
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
    Status:  17.07.2018 - OK   RASPI - OK 8.2.2019
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
            $output['xml'] = $xml;
            
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
     * [CurrentProgInfoURL] (xml)   => http://192.168.178.135:9090/BinaryBlob/1/CurrentProgInfo.dat
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK  
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetCurrentProgramInformationURL_MTVA(){
        try {
       $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentProgramInformationURL",

                               array(

                                    ));
        
            return $result;
        } catch (Exception $e) {
            $this->SendDebug("GetCurrentProgramInformationURL_MTVA ", $e->getMessage(), 0);  
            return false;
        }   
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
     * [MuteStatus] => Disable  // Enable
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
    Status:  17.07.2018 - OK   RASPI: OK  8.2.2019
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
    Status:  17.07.2018 - OK   RASPI: OK 8.2.2019
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
     *  gibt die URL der Liste der Schedule zurück
    ...............................................................................
    Parameters: none
    --------------------------------------------------------------------------------
    Returns:  (array)
     * [Result] => OK
     * [GetScheduleListURL] =>  http://192.168.178.35:9090/BinaryBlob/2/ScheduleList.dat
    --------------------------------------------------------------------------------
    Status:  17.07.2018 - OK   RASP: OK 8.2.2019
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
	 *  Gibt den aktuellen Source zurück und eine Liste aller Source als Array
    ...............................................................................
    Parameters: 
        none
    --------------------------------------------------------------------------------
    Returns:  
    *    [Result] => OK
    *    [CURRENTSOURCETYPE] => TV
    *    [ID] => 0
    *    [SOURCE1] => Array
    *        (
    *            [0] => Array
    *                (
    *                    [SOURCETYPE] => TV
    *                    [ID] => 0
    *                    [EDITABLE] => No
    *                    [DEVICENAME] => 3
    *                    [CONNECTED] => Yes
    *                    [SUPPORTVIEW] => Yes
    *                )
    --------------------------------------------------------------------------------
    Status:  3.2.2019 - OK   RASPI: OK 8.2.2019
    //////////////////////////////////////////////////////////////////////////////*/  
    protected function GetSourceList_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetSourceList",

                               array(

                                    ));
       
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
        $n = ($anzahl-4)/8;
            
        for ($index1 = 0; $index1 < $n; $index1++) {
            $output['SOURCE1'][$index1]['SOURCETYPE'] = $vals[4+$index1*8]['value'];    
            $output['SOURCE1'][$index1]['ID'] = $vals[5+$index1*8]['value'];  
            $output['SOURCE1'][$index1]['EDITABLE'] = $vals[6+$index1*8]['value'];    
            $output['SOURCE1'][$index1]['DEVICENAME'] = $vals[7+$index1*8]['level'];    
            $output['SOURCE1'][$index1]['CONNECTED'] = $vals[8+$index1*8]['value'];    
            $output['SOURCE1'][$index1]['SUPPORTVIEW'] = $vals[9+$index1*8]['value'];
        }
         return $output;    
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
    Status:  25.07.2018 - OK   Raspi: OK 8.2.2019
    //////////////////////////////////////////////////////////////////////////////*/    
    public function GetVolume_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetVolume",

                               array(

                                    ));

         return $result['Volume'];    
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
    Status:  OK - 3.2.1019   Raspi-OK 8.2.2019
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

      $client     = new SoapClient(null, array( "location"   => "http://"."192.168.178.135:52235".$path,

                                               "uri"        => $uri,

                                               "trace"      => true 
          ));


      return $client->__soapCall($action,$parameter);

    }catch(Exception $e){

      $faultstring = $e->faultstring;

      $faultcode   = $e->faultcode;

      if(isset($e->detail->UPnPError->errorCode)){

        $errorCode   = $e->detail->UPnPError->errorCode;

        throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode." ".$errorCode." (".$this->resolveErrorCode($path,$errorCode).")");

      }else{
        $this->SendDebug("Error during Soap Call:  ", $faultstring." ".$faultcode,0);
        throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode);
        return $faultcode;
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
   
    
  
  
	/* Remote Control Tasten simulieren
    //KEY_0
    //KEY_1
    //KEY_2
    //KEY_3
    //KEY_4
    //KEY_5
    //KEY_6
    //KEY_7
    //KEY_8
    //KEY_9
    //KEY_UP
    //KEY_DOWN
    //KEY_LEFT
    //KEY_RIGHT
    //KEY_MENU
    //KEY_PRECH
    //KEY_GUIDE
    //KEY_INFO
    //KEY_RETURN
    //KEY_CH_LIST
    //KEY_EXIT
    //KEY_ENTER
    //KEY_SOURCE
    //KEY_AD
    //KEY_PLAY
    //KEY_PAUSE
    //KEY_MUTE
    //KEY_PICTURE_SIZE
    //KEY_VOLUP
    //KEY_VOLDOWN
    //KEY_TOOLS
    //KEY_POWEROFF
    //KEY_CHUP
    //KEY_CHDOWN
    //KEY_CONTENTS
    //KEY_W_LINK //Media P
    //KEY_RSS //Internet
    //KEY_MTS //Dual
    //KEY_CAPTION //Subt
    //KEY_REWIND
    //KEY_FF
    //KEY_REC
    //KEY_STOP
	**************************************/
    public function sendKey(string $key)
    {
            $port = 55000;
            $src = "192.168.178.28"; # ip des IPS Servers
            //$mac = "B8:27:EB:80:C2:C7"; # mac des Kodi Servers
            $mac = "B8:27:EB:9D:78:B5"; # mac des IPS Servers
            $remote = "My IPS Raspi";
            $dst =  $this->ReadPropertyString('ip');
            $app = "iphone..iapp.samsung";
            $tv = "iphone.UE40D8000.iapp.samsung"; # iphone.UE40D8000.iapp.samsung


            /* Create a TCP/IP socket. */
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($socket === false) {
                echo "socket_create() failed: reason: " . 
                     socket_strerror(socket_last_error()) . "\n";
            }

            echo "Attempting to connect to '$dst' on port '$port'...";
            $result = socket_connect($socket, $dst, $port);
            if ($result === false) {
                echo "socket_connect() failed.\nReason: ($result) " . 
                      socket_strerror(socket_last_error($socket)) . "\n";
            }

            //------------------------------------------------------------
            //This part send once - asking for release remote control
            // will stay till TV aunpluged
            //-------------------------------------------------------------
            $msg = chr(0x64).chr(0x00).chr(strlen(base64_encode($src))).chr(0x00).base64_encode($src).chr(strlen(base64_encode($mac))).chr(0x00).base64_encode($mac).chr(strlen(base64_encode($remote))).chr(0x00).base64_encode($remote);
            $relremote = chr(0x00).chr(strlen($app)).chr(0x00).$app.chr(strlen($msg)).chr(0x00).$msg;

            socket_write($socket, $relremote, strlen($relremote)); 

            //------------------------------------------------------------------
            // Key übertragen
            //------------------------------------------------------------------

                    $msg = chr(0x00).chr(0x00).chr(0x00).chr(strlen(base64_encode($key))).chr(0x00).base64_encode($key);
                    $data = chr(0x00).chr(strlen($tv)).chr(0x00).$tv.chr(strlen($msg)).chr(0x00).$msg;


                    //$out = '';

                    echo "Sending HTTP HEAD request...";
                    socket_write($socket, $data, strlen($data));
                    echo "OK.\n";

            //echo "Reading response:\n\n";
            //$out = socket_read($socket, 2048);
            //echo $out."\n";

            socket_close($socket);
            sleep(1);
    }
  
    
    
}
