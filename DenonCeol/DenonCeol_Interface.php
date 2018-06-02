<?

trait CEOLupnp  
{


  


	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion Stop()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	Stopped upnp Stream
	//////////////////////////////////////////////////////////////////////////////*/
	  public function Stop()
	  {
	    $this->processSoapCall("/AVTransport/ctrl",

				   "urn:schemas-upnp-org:service:AVTransport:1",

				   "Stop",

				   array(

					  new SoapParam("0","InstanceID")

					));
	  }

 
 

	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Pausiert upnp Stream
						 
	Befehl		:	Pause()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
					
	Rückgabewert: 	 none
	//////////////////////////////////////////////////////////////////////////////*/
	public function Pause()
	{
		$this->processSoapCall("/AVTransport/ctrl",
		"urn:schemas-upnp-org:service:AVTransport:1",
	    						"Pause",
	                            array(
	                                   new SoapParam("0","InstanceID")
	                                 ));
	  }



	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Stopped upnp Stream
						 
	Befehl		:	Play()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
					
	Rückgabewert: 	 none
	//////////////////////////////////////////////////////////////////////////////*/
	  public function Play()
	  {
	    $this->processSoapCall("/AVTransport/ctrl",

				   "urn:schemas-upnp-org:service:AVTransport:1",

				   "Play",

				   array(

					  new SoapParam("0","InstanceID"),

					  new SoapParam("1","Speed"     )

					));
	  }




 

	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt den Lautstärke Wert zurück
						 
	Befehl		:	GetVolume($channel = 'Master')  
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 $channel = 'Master'
					
	Rückgabewert: 	 Integer Wert 0 - x 
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetVolume($channel = 'Master')
	  {
	    return (int)$this->processSoapCall("/RenderingControl/ctrl",

					       "urn:schemas-upnp-org:service:RenderingControl:1",

					       "GetVolume",

					       array(

						      new SoapParam("0"     ,"InstanceID"),

						      new SoapParam($channel,"Channel"   )

						    ));
	  }




	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt den MUTE Status zurück
						 
	Befehl		:	GetMute() 
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
					
	Rückgabewert: 	 0  - Mute nicht gesetzt 
					 1  - Mute gesetzt 
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetMute()
	  {
	    return (int)$this->processSoapCall("/RenderingControl/ctrl",

					       "urn:schemas-upnp-org:service:RenderingControl:1",

					       "GetMute",

					       array(

						      new SoapParam("0"     ,"InstanceID"),

						      new SoapParam("Master","Channel"   )

						    ));
	  }




	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt aktuelle Media Ifo aus des laufenden Streams
						 
	Befehl		:	GetMediaInfo() 
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
					
	Rückgabewert: 	ARRAY
	[
					[NrTracks]
					[MediaDuration]
	   				[CurrentURI]
	     			[CurrentURIMetaData]
		   			[NextURI]
					[NextURIMetaData]
			       	[PlayMedium] => NETWORK
    			  	[RecordMedium] => NOT_IMPLEMENTED
    				[WriteStatus] => NOT_IMPLEMENTED
    				[title] => 
		  $mediaInfo["title"] 
	]
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetMediaInfo()
	  {
	    $mediaInfo = $this->processSoapCall("/AVTransport/ctrl",

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

	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt Meta's des Files zurück (DIDL values)
						 
	Befehl		:	GetPositionInfo()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
					
	Rückgabewert:  array [
	 						[Track] 
	  						[TrackDuration]
	   						[TrackMetaData]
	     					[TrackURI]
		     				[RelTime] 
			    			[AbsTime]
				   			[RelCount
				     		[AbsCount]
							$positionInfo["artist"]
							$positionInfo["title"]
							$positionInfo["album"]
							$positionInfo["albumArtURI"]
							$positionInfo["albumArtist"]
							$positionInfo["streamContent"]
						]
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetPositionInfo()
	  {
	    $positionInfo = $this->processSoapCall("/AVTransport/ctrl",

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


	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt Übertragungs Status des Streams aus
						 
	Befehl		:	GetTransportInfo()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
	
	Rückgabewert: 	1   - PLAYING  
					2   - PAUSED_PLAYBACK 
					3   - STOPPED 
					5	- TRANSITIONING  
					deafult - Fehlermeldung
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetTransportInfo()
	  {
	    $returnContent = $this->processSoapCall("/AVTransport/ctrl",

						    "urn:schemas-upnp-org:service:AVTransport:1",

						    "GetTransportInfo",

						    array(

							   new SoapParam("0","InstanceID")

							 ));

	    switch ($returnContent["CurrentTransportState"]){
	      case "PLAYING":
		return 1;
	      case "PAUSED_PLAYBACK":
		return 2;
	      case "STOPPED":
		return 3;
	      case "TRANSITIONING":
		return 5;
	      default:
		throw new Exception("Unknown Transport State: ".$returnContent["CurrentTransportState"]); 
	    }
	  }



	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Gibt Play Mode zurück
						 
	Befehl		:	GetTransportSettings()
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:		 none
	
	Rückgabewert: 	0   - REPEAT_ALL  
					1   - REPEAT_ONE 
					2   - SHUFFLE_NOREPEAT 
					3   - REPEAT_ALL 
					4   - SHUFFLE_REPEAT_ONE 
					5	- Fehlermeldung  
	//////////////////////////////////////////////////////////////////////////////*/
	  public function GetTransportSettings()
	  {
	    $returnContent = $this->processSoapCall("/AVTransport/ctrl",

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


	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Stummschaltung
						 
	Befehl		:	SetMute($mute)
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter: $mute = 	'1'   - Stummschalten  
				'0'	  - Stummschalten aufheben
 	
	Rückgabewert: 	none
	//////////////////////////////////////////////////////////////////////////////*/
	  Protected function SetMute($mute)
	  {
	    $this->processSoapCall("/RenderingControl/ctrl",

				   "urn:schemas-upnp-org:service:RenderingControl:1",

				   "SetMute",

				   array(
					  new SoapParam("0"     ,"InstanceID" ),
					  new SoapParam("Master","Channel"    ),
					  new SoapParam($mute   ,"DesiredMute")
					));
	  }







 

	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Sub Routine für Soap Call. Übertragt upnp Protokoll im Netzwerk
						 
	Befehl		:	processSoapCall($path,$uri,$action,$parameter)
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:  $path  
				$uri		 
 				$action
				$parameter
				
	Rückgabewert: 	Fehler Code
	//////////////////////////////////////////////////////////////////////////////*/	
	  Protected function processSoapCall($path,$uri,$action,$parameter)
	  {
	    try{
		$ip = $this->ReadPropertyString('IPAddress');
	      $client     = new SoapClient(null, array("location"   => "http://".$ip.":8080".$path,

						       "uri"        => $uri,

						       "trace"      => true ));

	      return $client->__soapCall($action,$parameter);
	    }catch(Exception $e){
	      $faultstring = $e->faultstring;
	      $faultcode   = $e->faultcode;
	      if(isset($e->detail->UPnPError->errorCode)){
		$errorCode   = $e->detail->UPnPError->errorCode;
		throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode." ".$errorCode." (".$this->resoveErrorCode($path,$errorCode).")");
	      }else{

		throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode);

	      }

	    }

	  }



	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	Funktion 	:	Sub Routine für Soap Call. Error Handling
						 
	Befehl		:	processSoapCall($path,$uri,$action,$parameter)
	--------------------------------------------------------------------------------
	Soap / upnp-command
	--------------------------------------------------------------------------------
	Parameter:  $path  
				$uri		 
 				$action
				$parameter
				
	Rückgabewert: 	Fehler Code
	//////////////////////////////////////////////////////////////////////////////*/
	  private function resolveErrorCode($path,$errorCode)

	  {

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


	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	-------------------------------------------------------------------------------- 	
	Funktion 	:	sende Telnet Befehle	 	 					
	Befehl		:	send_cmd($cmd)
	-------------------------------------------------------------------------------------------
		CEOL-TELNET-command
	-------------------------------------------------------------------------------------------
 	Parameter:		 $cmd
					 'MVUP'  	-> Master Volume UP
					 'MVDOWN'	-> Master Volume DOWN
					 
	Rückgabewert: 	 $xml
	//////////////////////////////////////////////////////////////////////////////*/		
	Protected function send_cmd($cmd){
 		$host = $this->ip;
		$url = "http://$host:80/goform/formiPhoneAppDirect.xml";
		
		$xml = $this->curl_get($url, $cmd);
		return $xml;
	}


	
	
	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	* 2nd Method
	* Send a POST request using cURL 
	* @param string $url to request
		$host = "192.168.178.29";  		
		$url =  "http://192.168.178.29/goform/AppCommand.xml";
		
	* @param array $post values to send 	=
							$xml = "<?xml version="1.0" encoding="utf-8"?>";
							$xml .= "<tx>";
	 						$xml .= "<cmd id="1">SetFavoriteStation</cmd>";
	 						$xml .= "<zone>Main</zone>";
	 						$xml .= "<value>3</value>";
							$xml .= "</tx>"
		$post =  $xml;										
	
	* @param array $options for cURL 
	* @return string 
	//////////////////////////////////////////////////////////////////////////////*/	
	Protected function curl_post($url, $post = NULL) 
	{
		print_r($url)."\n";
		print_r($post)."\n";
		$defaults = array(
			CURLOPT_POST => 1,
			//CURLOPT_HEADER => array('Content-Type: text/xml'),
			CURLOPT_HEADER => 1,
			// set url
			CURLOPT_URL => $url,
			CURLOPT_FRESH_CONNECT => 1,
			//return the transfer as a string
			CURLOPT_RETURNTRANSFER => 1,
			//CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 5,
			//CURLOPT_POSTFIELDS => http_build_query($post)
			//CURLOPT_POSTFIELDS => $post
			);
		// create curl resource
		$ch = curl_init();
		curl_setopt_array($ch, ($defaults));
		
		
		if( ! $result = curl_exec($ch))
		{
			trigger_error(curl_error($ch));
		}
		// close curl resource to free up system resources
		curl_close($ch);
		return $result;
		} 
	
	/*//////////////////////////////////////////////////////////////////////////////
	2018-02´5-01 (TESTED-OK)
	--------------------------------------------------------------------------------
	* 1st Method
	* Send a GET request using cURL 
	* @param string $url to request 
	* @param array $get values to send 
	* @param array $options for cURL 
	* @return string 
	
	//////////////////////////////////////////////////////////////////////////////*/	
	Protected function curl_get($url, $get)
	{
		//print_r($get);
		//$test = $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get);
		//$test = $url. (strpos($url, '?') === FALSE ? '?' : ''). $get;
		//print_r($test);
		//$ret = Message($test);
		$defaults = array(
			//CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
			CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). $get,
			CURLOPT_HEADER => 0,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 4
		);
		
		$ch = curl_init();
		curl_setopt_array($ch, ($defaults));
		
		$result = curl_exec($ch);
		
		
		$error = curl_error($ch);
		//print_r($result);
		
		//if( ! $result = curl_exec($ch))
		//{
			//trigger_error('Curl-Fehler:'.curl_error($ch));
		//}
		$errno = curl_errno($ch);
	
		//	print_r($errno);
		
		curl_close($ch);
		return $result;
	}

} //Ende der Klasse


?>
