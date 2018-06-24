<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Torsten
 */



trait upnp {

    
	
 
	
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion ContentDirectory_Browse()
	--------------------------------------------------------------------------------
	Parameter:  $ServerIP, 
				$ServerPort $Kernel, 
				$ServerContentDirectory, 
				$ObjectID, 
				$BrowseFlag, 
				$Filter, 
				$StartingIndex, 
				$RequestedCount, 
				$SortCriteria
	
	--------------------------------------------------------------------------------
	upnp Auslesen des Server Inhaltes einer ID
	--------------------------------------------------------------------------------
	return array ['Result'] as xml, ['NumberReturned'] as integer, ['TotalMatches'] as integer, ['UpdateID'] as integer;
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function ContentDirectory_Browse ($ServerIP, $ServerPort, $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria)
	{	
	    return $this->processSoapCall($ServerIP, $ServerPort, $ServerContentDirectory,
	
	                           "urn:schemas-upnp-org:service:ContentDirectory:1",
	
	                           "Browse",
	
	                           array( 
	                                  new SoapParam($ObjectID                ,"ObjectID"         ),
	                                  new SoapParam($BrowseFlag 			,"BrowseFlag"       ),
	                                  new SoapParam($Filter                 ,"Filter"           ),
	                                  new SoapParam($StartingIndex          ,"StartingIndex"	),
	                                  new SoapParam($RequestedCount         ,"RequestedCount"	),
									  new SoapParam($SortCriteria           ,"SortCriteria"		)	
	                                )
		);
		
	}
	
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion SetAVTransportURI()
	--------------------------------------------------------------------------------
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
				$file = URL des mp3 files = muss STRING sein ud kein array
				e.g. 'http://192.168.178.1:49200/AUDIO/DLNA-1-0/Musik/Katie_Melua%20-%20(Pictures)_%23M/1%20-%20If%20The%20Lights%20Go%20Out.mp3'
				$MetaData: meta daten (optional) = muss string sein und kein array
	--------------------------------------------------------------------------------
	upnp Übertragung eines files
	--------------------------------------------------------------------------------
	return: nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function SetAVTransportURI($ClientIP, $ClientPort, $ClientControlURL, $file, $MetaData){	
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetAVTransportURI",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         		),
	                                  new SoapParam($file 			,"CurrentURI"       		),
	                                  new SoapParam($MetaData       ,"CurrentURIMetaData"       )
	                                )
		);
		
	}
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion SetNextAVTransportURI()
	--------------------------------------------------------------------------------
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
				$file_next = URL des mp3 files = muss STRING sein ud kein array
				e.g. 'http://192.168.178.1:49200/AUDIO/DLNA-1-0/Musik/Katie_Melua%20-%20(Pictures)_%23M/1%20-%20If%20The%20Lights%20Go%20Out.mp3'
				$MetaData_next: meta daten (optional) = muss string sein und kein array
	--------------------------------------------------------------------------------
	upnp Übertragung eines files- es kann maximal ein file nachgeladen (Warteschlange) geladen werden.
	--------------------------------------------------------------------------------
	return: nur Fehler Code
	Status: checked
	
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function SetNextAVTransportURI($ClientIP, $ClientPort, $ClientControlURL, $file_next, $MetaData_next){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetNextAVTransportURI",
	
	                           array( 
	                                  new SoapParam('0'             	,"InstanceID"         	),
	                                  new SoapParam($file_next 			,"NextURI"       		),
	                                  new SoapParam($MetaData_next      ,"NextURIMetaData"     	)
	                                )
		);
	}

	Protected function Play_AV($ClientIP, $ClientPort, $ClientControlURL){	
		//IPSLog('start Play_AV Funktion ',$ClientPort);
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Play",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"       ),
	                                  new SoapParam('1' 			,"Speed"       		)
	                                )
		);
 
	}
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion Stop_AV($ClientIP, $ClientPort, $ClientControlURL)
	
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Stopped die upnp Übertragung
	--------------------------------------------------------------------------------
	return: nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Stop_AV($ClientIP, $ClientPort, $ClientControlURL){
		//IPSLog('start stop_AV Funktion ',$ClientControlURL);
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Stop",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}
	
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion Pause_AV($ClientIP, $ClientPort, $ClientControlURL)
	
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Hält die upnp Übertragung an-erneutes Pause Signal setzt Wiedergabe fort
	--------------------------------------------------------------------------------
	return: nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Pause_AV($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Pause",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}		
	
	Protected function Next_AV($ClientIP, $ClientPort, $ControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Next",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}		
	
	Protected function Previous_AV($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Previous",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}
	
	Protected function Seek_AV($ClientIP, $ClientPort, $ClientControlURL, $position){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Seek",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"       ),
	                                  new SoapParam('REL_TIME'      ,"Unit"         	),
	                                  new SoapParam($position       ,"Target"         	)  
	                                )
		);
	}
	
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	FunktionPlaymode()
	--------------------------------------------------------------------------------
	Parameter: $Playmode = 'NORMAL' , 'RANDOM', 'REPEAT_ONE', REPEAT_ALL
	
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Playmode($ClientIP, $ClientPort, $ClientControlURL, $Playmode){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetPlayMode",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"       ),
	                                  new SoapParam($PlaymMode       ,"NewPlayMode"      ) 
	                                )
		);
	}
	
	
	Protected function SetVolume($ClientIP, $ClientPort, $RenderingControlURL, $DesiredVolume){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "SetVolume",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 ),
	                                  new SoapParam($DesiredVolume       ,"DesiredVolume"    ) 									   
	                                )
		);
	}
	
	
	Protected function GetVolume($ClientIP, $ClientPort, $RenderingControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "GetVolume",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 )									   
	                                )
		);
	}
	
	
	Protected function SetMute($ClientIP, $ClientPort, $RenderingControlURL, $DesiredMute){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "SetMute",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 ),
	                                  new SoapParam($DesiredMute       	 ,"DesiredMute"    	 )								  									   
	                                )
		);
	}
	
	
	Protected function GetMute($ClientIP, $ClientPort, $RenderingControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "GetMute",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 )								  									   
	                                )
		);
	}

	Protected function GetDeviceCapabilities($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetDeviceCapabilities",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}


	Protected function GetMediaInfo ($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetMediaInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion GetTransportInfo($ClientIP, $ClientPort, $ClientControlURL)
	
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	HLiefert den Transport Status als array
	--------------------------------------------------------------------------------
	return:  Array
			(
    			[CurrentTransportState] => PLAYING // NO_MEDIA_PRESENT //STOPPED //PAUSED_PLAYBACK
    			[CurrentTransportStatus] => OK
    			[CurrentSpeed] => 1
			)
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function GetTransportInfo($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetTransportInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	Protected function GetTransportSettings($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetTransportSettings",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}


	Protected function GetCurrentTransportActions($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetCurrentTransportActions",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}
	
	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Funktion GetPositionInfo($ClientIP, $ClientPort, $ClientControlURL)
	
	Parameter:  $ClientIP = IP Adresse der Clients , 
				$ClientPort = Übertragungs Port des Clients', 
				$ClientControlURL = Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Liefert den Position Status als array
	--------------------------------------------------------------------------------
	return:  Array
			(
                            [Track] => 1
                            [TrackDuration] => 0:07:12
                            [TrackMetaData] =>
                            [TrackURI] =>
                            [RelTime] => 0:04:09
                            [AbsTime] => 5:30:22
                            [RelCount] => 249000
                            [AbsCount] => 19822482
			)
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function GetPositionInfo($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetPositionInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}




 
	




	/*//////////////////////////////////////////////////////////////////////////////
	--------------------------------------------------------------------------------
	Sub Functions()
	--------------------------------------------------------------------------------
	//////////////////////////////////////////////////////////////////////////////*/	
	Protected function processSoapCall($ip, $port, $path, $uri, $action, $parameter)
    {
	    try{
	    	$client     = new SoapClient(null, array("location"   => "http://".$ip.':'.$port.$path,
	                                               "uri"        => $uri,
	                                               "trace"      => true ));
	      	return $client->__soapCall($action, $parameter);
	    }
		catch(Exception $e){
	    	$faultstring = $e->faultstring;
  	      	$faultcode   = $e->faultcode;
	      	if(isset($e->detail->UPnPError->errorCode)){
	        	$errorCode   = $e->detail->UPnPError->errorCode;
	        	throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode." ".$errorCode." (".$this->resolveErrorCode($path,$errorCode).")");
	      	}
			else{
	        	throw new Exception("Error during Soap Call: ".$faultstring." ".$faultcode);
	      	}
	    }
    }




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
    	}
		else{
      		return "UNKNOWN";
    	}
   	}




}

