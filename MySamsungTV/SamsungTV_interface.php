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
    public function SetAVTransportURI(string $CurrentURI, string $CurrentURIMetaData=""){

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
