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
    public function SendKeyCode_AV(integer $KeyCode, string $KeyDescription){
            
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
    /* Function: SetBrightness_AV($Brightness)
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
    public function SetBrightness_AV(integer $Brightness){

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
    UPNP  
    ...............................................................................
    Parameters: 
     *  $volume = integer 0 ... 16
      
    --------------------------------------------------------------------------------
    Returns:  

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
    Status:  
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
	 
    ...............................................................................
    Parameters: 
     

    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentMute>    0

    --------------------------------------------------------------------------------
    Status:  
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
	 
    ...............................................................................
    Parameters: 
     

    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentVolume>  2

    --------------------------------------------------------------------------------
    Status:  
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
	 
    ...............................................................................
    Parameters: 
     

    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentColorTemperature>    3

    --------------------------------------------------------------------------------
    Status:  
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
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentSharpness>   60

    --------------------------------------------------------------------------------
    Status:  
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
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentContrast>    80

    --------------------------------------------------------------------------------
    Status:  
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
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <CurrentBrightness>  50

    --------------------------------------------------------------------------------
    Status:  
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
    /* Function: GetWatchingInformation_MTVA()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>OK
     * <TVMode>Tuner
     * <WatchingInformation>Hilf mir! Jung, pleite, verzweifelt... on RTL2 (01:0PM~02:00PM)
     * 
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/   
    public function GetWatchingInformation_MTVA(){
	$sPostfields ="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"
	        ."<s:Envelope s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">\r\n"
	        ."<s:Body>\r\n"
	        ."<u:GetWatchingInformation xmlns:u=\"urn:samsung.com:service:MainTVAgent2:1\" />\r\n"
	        ."</s:Body>\r\n"
	        ."</s:Envelope>\r\n";
	$soap_do = curl_init();
	
	$header = array(
	        "Content-Type: text/xml",
	        "Cache-Control: no-cache",
	        "Pragma: no-cache",
	        "SOAPAction: \"urn:samsung.com:service:MainTVAgent2:1#GetWatchingInformation\"",
	        "Content-length: ".strlen($sPostfields),
	);
	
	curl_setopt($soap_do, CURLOPT_URL,            "http://192.168.178.35:52235/MainTVServer2/control/MainTVAgent2" );
	curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($soap_do, CURLOPT_POST,           true );
	curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $sPostfields);
	curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header );
	
	$output = curl_exec($soap_do);
	$aInfo = curl_getinfo($soap_do);
	curl_close($soap_do);
	//print_r( $output );
	//print_r( $aInfo );
	$xmlParser = xml_parser_create("UTF-8");
        xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parse_into_struct($xmlParser, $output, $vals, $index);
        xml_parser_free($xmlParser);

	return $vals[5]['value'];
	exit();
    }
    
    
     //*****************************************************************************
    /* Function: GetSourceList_MTVA()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>OK
     * <SourceList>&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;&lt;SourceList&gt;&lt;CurrentSourceType&gt;TV&lt;/CurrentSourceType&gt;&lt;ID&gt;0&lt;/ID&gt;&lt;Source&gt;&lt;SourceType&gt;TV&lt;/SourceType&gt;&lt;ID&gt;0&lt;/ID&gt;&lt;Editable&gt;No&lt;/Editable&gt;&lt;DeviceName&gt;&lt;/DeviceName&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;SCART1&lt;/SourceType&gt;&lt;ID&gt;75&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;SCART2&lt;/SourceType&gt;&lt;ID&gt;76&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;PC&lt;/SourceType&gt;&lt;ID&gt;67&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;No&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;USB&lt;/SourceType&gt;&lt;ID&gt;24&lt;/ID&gt;&lt;Editable&gt;No&lt;/Editable&gt;&lt;DeviceName&gt;Transcend 16GB&lt;/DeviceName&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;No&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;DLNA&lt;/SourceType&gt;&lt;ID&gt;23&lt;/ID&gt;&lt;Editable&gt;No&lt;/Editable&gt;&lt;DeviceName&gt;AVM Mediaserver&lt;/DeviceName&gt;&lt;Connected&gt;Yes&lt;/Connected&gt;&lt;SupportView&gt;No&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;HDMI1/DVI&lt;/SourceType&gt;&lt;ID&gt;71&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;HDMI2&lt;/SourceType&gt;&lt;ID&gt;72&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;AV_RCV&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;HDMI3&lt;/SourceType&gt;&lt;ID&gt;73&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;DMA&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;HDMI4&lt;/SourceType&gt;&lt;ID&gt;74&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;BLUE_RAY&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;AV&lt;/SourceType&gt;&lt;ID&gt;55&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;Source&gt;&lt;SourceType&gt;COMPONENT&lt;/SourceType&gt;&lt;ID&gt;63&lt;/ID&gt;&lt;Editable&gt;Yes&lt;/Editable&gt;&lt;EditNameType&gt;NONE&lt;/EditNameType&gt;&lt;Connected&gt;No&lt;/Connected&gt;&lt;SupportView&gt;Yes&lt;/SupportView&gt;&lt;/Source&gt;&lt;/SourceList&gt;</SourceList></u:GetSourceListResponse></s:Body></s:Envelope> 
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function GetSourceList_MTVA(){
	$sPostfields ="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"
	        ."<s:Envelope s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">\r\n"
	        ."<s:Body>\r\n"
	        ."<u:GetSourceList xmlns:u=\"urn:samsung.com:service:MainTVAgent2:1\" />\r\n"
	        ."</s:Body>\r\n"
	        ."</s:Envelope>\r\n";
	$soap_do = curl_init();
	
	$header = array(
	        "Content-Type: text/xml",
	        "Cache-Control: no-cache",
	        "Pragma: no-cache",
	        "SOAPAction: \"urn:samsung.com:service:MainTVAgent2:1#GetSourceList\"",
	        "Content-length: ".strlen($sPostfields),
	);
	
	curl_setopt($soap_do, CURLOPT_URL,            "http://192.168.178.35:52235/MainTVServer2/control/MainTVAgent2" );
	curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($soap_do, CURLOPT_POST,           true );
	curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $sPostfields);
	curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header );
	
	$output = curl_exec($soap_do);
	$aInfo = curl_getinfo($soap_do);
	curl_close($soap_do);
	//print_r( $output );
	//print_r( $aInfo );
	//$str =htmlspecialchars_decode($output);

        //$p = xml_parser_create();
        //xml_parse_into_struct($p, $str, $vals, $index);
        //xml_parser_free($p);
        //echo "Index array\n";
        //print_r($index);
        //echo "\nVals array\n";
        //print_r($vals);
		 	return $output;
	exit();

    } 
    
    
     //*****************************************************************************
    /* Function: GetDTVInformation_MTVA()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>OK
     * <DTVInformation> <?xml version="1.0" encoding="UTF-8"?><DTVInformation><SupportAntMode>1,2,3</SupportAntMode><SupportChSort>Yes</SupportChSort><SupportCloneView>Yes</SupportCloneView><SupportSecondTVView>Yes</SupportSecondTVView><SupportExtSourceView>Yes</SupportExtSourceView><SupportDTV>Yes</SupportDTV><TunerCount>1</TunerCount><SupportTVVersion>2011</SupportTVVersion><SupportChannelLock>No</SupportChannelLock><SupportChannelInfo>Yes</SupportChannelInfo><SupportChannelDelete>Yes</SupportChannelDelete><SupportEditNumMode><EditNumMode><NumMode>DIGITAL_SWAP</NumMode><MinValue>1</MinValue><MaxValue>9999</MaxValue></EditNumMode><EditNumMode><NumMode>ANALOG_INSERT</NumMode><MinValue>0</MinValue><MaxValue>99</MaxValue></EditNumMode></SupportEditNumMode><SupportRegionalVariant>No</SupportRegionalVariant><SupportStream><Container>MPEG2</Container><VideoFormat>MPEG4SP</VideoFormat><AudioFormat>MP3</AudioFormat><XResolution>672</XResolution><YResolution>544</YResolution><AudioSamplingRate>48000</AudioSamplingRate><AudioChannels>2</AudioChannels></SupportStream><SupportPVR>Yes</SupportPVR><TargetLocation>TARGET_LOCATION_PANEURO</TargetLocation></DTVInformation>
     * 
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function GetDTVInformation_MTVA(){
	$sPostfields ="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"
	        ."<s:Envelope s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">\r\n"
	        ."<s:Body>\r\n"
	        ."<u:GetDTVInformation xmlns:u=\"urn:samsung.com:service:MainTVAgent2:1\" />\r\n"
	        ."</s:Body>\r\n"
	        ."</s:Envelope>\r\n";
	$soap_do = curl_init();
	
	$header = array(
	        "Content-Type: text/xml",
	        "Cache-Control: no-cache",
	        "Pragma: no-cache",
	        "SOAPAction: \"urn:samsung.com:service:MainTVAgent2:1#GetDTVInformation\"",
	        "Content-length: ".strlen($sPostfields),
	);
	
	curl_setopt($soap_do, CURLOPT_URL,            "http://192.168.178.35:52235/MainTVServer2/control/MainTVAgent2" );
	curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($soap_do, CURLOPT_POST,           true );
	curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $sPostfields);
	curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header );
	
	$output = curl_exec($soap_do);
	$aInfo = curl_getinfo($soap_do);
	curl_close($soap_do);
	print_r( $output );
	print_r( $aInfo );
	exit();

	return $output;
    }
    
    
    
     //*****************************************************************************
    /* Function: GetCurrentTime_MTVA()
    ...............................................................................
	 
    ...............................................................................
    Parameters: 
    
    --------------------------------------------------------------------------------
    Returns:  
     * <Result>         OK
     * <CurrentTime>    2018-07-17T13:15:06
    --------------------------------------------------------------------------------
    Status:  
    //////////////////////////////////////////////////////////////////////////////*/  
    public function GetCurrentTime_MTVA() {
	$sPostfields ="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n"
	        ."<s:Envelope s:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\" xmlns:s=\"http://schemas.xmlsoap.org/soap/envelope/\">\r\n"
	        ."<s:Body>\r\n"
	        ."<u:GetCurrentTime xmlns:u=\"urn:samsung.com:service:MainTVAgent2:1\" />\r\n"
	        ."</s:Body>\r\n"
	        ."</s:Envelope>\r\n";
	$soap_do = curl_init();
	
	$header = array(
	        "Content-Type: text/xml",
	        "Cache-Control: no-cache",
	        "Pragma: no-cache",
	        "SOAPAction: \"urn:samsung.com:service:MainTVAgent2:1#GetCurrentTime\"",
	        "Content-length: ".strlen($sPostfields),
	);
	
	curl_setopt($soap_do, CURLOPT_URL,            "http://192.168.178.35:52235/MainTVServer2/control/MainTVAgent2" );
	curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
	curl_setopt($soap_do, CURLOPT_POST,           true );
	curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $sPostfields);
	curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header );
	
	$output = curl_exec($soap_do);
	$aInfo = curl_getinfo($soap_do);
	curl_close($soap_do);
	//print_r( $output );
	//print_r( $aInfo );

        $xmlParser = xml_parser_create("UTF-8");

        xml_parser_set_option($xmlParser, XML_OPTION_TARGET_ENCODING, "UTF-8");

        xml_parse_into_struct($xmlParser, $output, $vals, $index);

        xml_parser_free($xmlParser);
            $result = $vals[4]['value'];
            return $result;	
            exit();
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
    public function GetCurrentProgramInformationURL_MTVA(){
        $result = $this->processSoapCall("/MainTVServer2/control/MainTVAgent2",

                               "urn:samsung.com:service:MainTVAgent2:1",

                               "GetCurrentProgramInformationURL",

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
