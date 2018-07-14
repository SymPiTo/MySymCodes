<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * class: upnp TRAIT
 * 
 * 
 */
trait upnp {

	//*****************************************************************************
	/* Function: ContentDirectory_Browse()
   	...............................................................................
        upnp Auslesen des Server Inhaltes einer ID
	...............................................................................
	Parameter:  
         *   $ClientIP                  - IP Adresse der Clients.
         *   $ClientPort                - Übertragungs Port des Clients.
         *   $ClientControlURL          - Stammverzeichnis des Clients
         *   $Kernel                    -  Stammverzeichnis IPS
   	 *   $ServerContentDirectory    -  /MediaServer/ContentDirectory/Control = Stammverzeichnis Server
	 *   $ObjectID                  -  Object ID
	 *   $BrowseFlag                -  "BrowseDirectChildren"; //"BrowseMetadata"; //"BrowseDirectChildren"
         *   $Filter                    -  "*"; //GetValue()
	 *   $StartingIndex             -  "0"; //GetValue()  = Start Object ID
	 *   $RequestedCount            -  "0"; //GetValue();
	 *   $SortCriteria              -  ""; //GetValue();
 	--------------------------------------------------------------------------------
	Returns:
            array ['Result'] as xml, 
          --- Code
          ['NumberReturned'] as integer, 
          ['TotalMatches'] as integer, 
          ['UpdateID'] as integer;
          --- 
         *          */

	//////////////////////////////////////////////////////////////////////////////*/
	Protected function ContentDirectory_Browse (string $ServerIP, string $ServerPort, string $Kernel, $ServerContentDirectory, $ObjectID, $BrowseFlag, $Filter, $StartingIndex, $RequestedCount, $SortCriteria)
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
	
	//*****************************************************************************
	/* Function: SetAVTransportURI($ClientIP, $ClientPort, $ClientControlURL, $file, $MetaData)
   	...............................................................................
        uupnp Übertragung eines files
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $file_next - URL des mp3 files = muss STRING sein ud kein array.
         *   e.g. 'http://192.168.178.1:49200/AUDIO/DLNA-1-0/Musik/Katie_Melua%20-%20(Pictures)_%23M/1%20-%20If%20The%20Lights%20Go%20Out.mp3'.
         *   $MetaData          -  meta daten (optional) = muss string sein und kein array
 	--------------------------------------------------------------------------------
	Returns:
            nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function SetAVTransportURI(string $ClientIP, string $ClientPort, string $ClientControlURL, string $file, string $MetaData){
            $this->SendDebug('SetAVTransportURI', 'IP: '.$ClientIP, 0);
            $this->SendDebug('SetAVTransportURI', 'Port: '.$ClientPort, 0);
            $this->SendDebug('SetAVTransportURI', 'FILE: '.$file, 0);
            $this->SendDebug('SetAVTransportURI', 'MetaData: '.$MetaData, 0);
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetAVTransportURI",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         		),
	                                  new SoapParam($file 		,"CurrentURI"       		),
	                                  new SoapParam($MetaData       ,"CurrentURIMetaData"       )
	                                )
		);
		
	}
        
	//*****************************************************************************
	/* Function: SetNextAVTransportURI($ClientIP, $ClientPort, $ClientControlURL, $file_next, $MetaData_next)
  	...............................................................................
        upnp Übertragung eines files- es kann maximal ein file nachgeladen (Warteschlange) geladen werden.
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $file_next         - URL des mp3 files = muss STRING sein ud kein array
         *   e.g. 'http://192.168.178.1:49200/AUDIO/DLNA-1-0/Musik/Katie_Melua%20-%20(Pictures)_%23M/1%20-%20If%20The%20Lights%20Go%20Out.mp3'
         *   $MetaData_next     - meta daten (optional) = muss string sein und kein array
 	--------------------------------------------------------------------------------
	Returns:
            nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function SetNextAVTransportURI(string $ClientIP, string $ClientPort, string $ClientControlURL, string $file_next, string $MetaData_next){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetNextAVTransportURI",
	
	                           array( 
	                                  new SoapParam('0'                 ,"InstanceID"         	),
	                                  new SoapParam($file_next          ,"NextURI"       		),
	                                  new SoapParam($MetaData_next      ,"NextURIMetaData"     	)
	                                )
		);
	}
        
	//*****************************************************************************
	/* Function:  Play_AV($ClientIP, $ClientPort, $ClientControlURL)
  	...............................................................................
        Spielt den stream auf Device
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns:
         *  Error Code
         */
	Protected function Play_AV(string $ClientIP, string $ClientPort, string $ClientControlURL){	
            $this->SendDebug('Play_AV', 'IP: '.$ClientIP, 0);
            $this->SendDebug('Play_AV', 'Port: '.$ClientPort, 0);
            $this->SendDebug('Play_AV', 'ClientControlURL: '.$ClientControlURL, 0);
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Play",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"       ),
	                                  new SoapParam('1' 		,"Speed"            )
	                                )
		);
 
	}
        
	//*****************************************************************************
	/* Function: Stop_AV($ClientIP, $ClientPort, $ClientControlURL)
  	...............................................................................
        Stopped die upnp Übertragung
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns:
             nur Fehler Code
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Stop_AV(string $ClientIP, string $ClientPort, string $ClientControlURL){
		//IPSLog('start stop_AV Funktion ',$ClientControlURL);
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Stop",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}
	
	//*****************************************************************************
	/* Function: Pause_AV($ClientIP, $ClientPort, $ClientControlURL)
  	...............................................................................
        Hält die upnp Übertragung an-erneutes Pause Signal setzt Wiedergabe fort
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns:
            FehlerCode
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Pause_AV(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Pause",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}	
        
	//*****************************************************************************
	/* Function: Next_AV($ClientIP, $ClientPort, $ControlURL)
  	...............................................................................
  	This is a convenient action to advance to the next track. This action is functionally equivalent to
        Seek(“TRACK_NR”, “CurrentTrackNr+1”). This action does not cycle back to the first track.
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns:
         */
	Protected function Next_AV(string $ClientIP, string $ClientPort, string $ControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Next",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}		

	//*****************************************************************************
	/* Function: Previous_AV($ClientIP, $ClientPort, $ClientControlURL)
 	...............................................................................
  	This is a convenient action to advance to the previous track. This action is functionally 
        equivalent to Seek(“TRACK_NR”, “CurrentTrackNr-1”). This action does not cycle back to the last track.
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns:
         */        
	Protected function Previous_AV(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "Previous",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"         )
	                                )
		);
	}

	//*****************************************************************************
	/* Function: Seek_AV($ClientIP, $ClientPort, $ClientControlURL, $position)
 	...............................................................................
	This action starts seeking through the resource controlled by the specified instance - 
        as fast as possible - to the position, specified in the Target argument. 
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $position =        - '0:00:00.000'
 	--------------------------------------------------------------------------------
	Returns:
         */       
	Public function Seek_AV(string $ClientIP, string $ClientPort, string $ClientControlURL,string $position){
            $this->SendDebug('Seek_AV', $position, 0);
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
	
	//*****************************************************************************
	/* Function: Playmode($ClientIP, $ClientPort, $ClientControlURL, $Playmode)
 	...............................................................................
	Setzt den Playmode für das Device
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $Playmode          - $Playmode = 'NORMAL' , 'RANDOM', 'REPEAT_ONE', REPEAT_ALL
 	--------------------------------------------------------------------------------
	Returns:

	//////////////////////////////////////////////////////////////////////////////*/
	Protected function Playmode_AV(string $ClientIP, string $ClientPort, string $ClientControlURL, string $Playmode){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "SetPlayMode",
	
	                           array( 
	                                  new SoapParam('0'             ,"InstanceID"       ),
	                                  new SoapParam($PlaymMode       ,"NewPlayMode"      ) 
	                                )
		);
	}
	
	//*****************************************************************************
	/* Function: SetVolume($ClientIP, $ClientPort, $RenderingControlURL, $DesiredVolume)
 	...............................................................................
	Setzt die Lautstärke des Device auf Wert $DesiredVolume
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $DesiredVolume     - Lautstärke Wert
 	--------------------------------------------------------------------------------
	Returns:
         */
	Protected function SetVolume_AV(string $ClientIP, string $ClientPort, string $RenderingControlURL, $DesiredVolume){
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
	
	//*****************************************************************************
	/* Function: GetVolume($ClientIP, $ClientPort, $RenderingControlURL)
  	...............................................................................
	Gibt die Lautstärke des Device zurück
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns: 
         */
	Protected function GetVolume_AV($ClientIP, $ClientPort, $RenderingControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "GetVolume",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 )									   
	                                )
		);
	}
	
       
        
	//*****************************************************************************
	/* Function: SetMute($ClientIP, $ClientPort, $RenderingControlURL, $DesiredMute)
  	...............................................................................
	Schaltet Upnp Device stumm
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
         *   $DesiredMute       - 1 // 0
	--------------------------------------------------------------------------------
	Returns: 
         */
	Protected function SetMute_AV(string $ClientIP, string $ClientPort, string $RenderingControlURL, $DesiredMute){
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
	
	//*****************************************************************************
	/* Function: GetMute($ClientIP, $ClientPort, $RenderingControlURL)
 	...............................................................................
	Gibt den Mute Status zurück
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns: 
         */
	Protected function GetMute_AV(string $ClientIP, string $ClientPort, string $RenderingControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $RenderingControlURL,
	
	                           "urn:schemas-upnp-org:service:RenderingControl:1",
	
	                           "GetMute",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       ),
	                                  new SoapParam('Master'       		 ,"Channel"      	 )								  									   
	                                )
		);
	}

	//*****************************************************************************
	/* Function: GetDeviceCapabilities($ClientIP, $ClientPort, $ClientControlURL)
 	...............................................................................
	This action returns information on device capabilities of the specified instance, 
        such as the supported playback and recording formats, and the supported quality levels 
        for recording. This action has no effect on state.
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns: 
         * PlayMedia
         * RecMedia
         * RecQualityModes 
         */        
	Protected function GetDeviceCapabilities(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetDeviceCapabilities",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	//*****************************************************************************
	/* Function: GetMediaInfo ($ClientIP, $ClientPort, $ClientControlURL)
	...............................................................................
	Liefert den Transport Status als array
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns:   
         * This action returns information associated with the current media of the specified instance; 
         * it has no effect on state
         * Array()
            --- Code
            * NumberOfTracks
            * CurrentMediaDuration
            * AVTransportURI
            * AVTransportURIMetaData
            * NextAVTransportURI
            * NextAVTransportURIMetaData
            * PlaybackStorageMedium
            * RecordStorageMedium
            * RecordMediumWriteStatus 
            ---
        --------------------------------------------------------------------------------
	Status: checked 
         */
	Protected function GetMediaInfo(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetMediaInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	//*****************************************************************************
	/* Function: GetTransportInfo($ClientIP, $ClientPort, $ClientControlURL)
	...............................................................................
	Liefert den Transport Status als array
	...............................................................................
	Parameter:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns:   
         * Array()
            --- Code
                [CurrentTransportState] => PLAYING // NO_MEDIA_PRESENT //STOPPED //PAUSED_PLAYBACK
                [CurrentTransportStatus] => OK
                [CurrentSpeed] => 1
             ---
        --------------------------------------------------------------------------------
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	Protected function GetTransportInfo(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetTransportInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	//*****************************************************************************
	/* Function: GetTransportSettings($ClientIP, $ClientPort, $ClientControlURL)
	...............................................................................
	 
	...............................................................................
	Parameters:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns: 
         * This action returns information on various settings of the specified instance, 
         * such as the current play mode
         * and the current recording quality mode.This action has no effect on state.
         * as Array ['PlayMode'] and [RecQualityMode]  
         */        
	Protected function GetTransportSettings(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetTransportSettings",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}

	//*****************************************************************************
	/* Function: GetCurrentTransportActions($ClientIP, $ClientPort, $ClientControlURL)
	...............................................................................
	 
	...............................................................................
	Parameters:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
	--------------------------------------------------------------------------------
	Returns: 
         * Returns the CurrentTransportActions state variable for the specified instance.
         */ 
	Protected function GetCurrentTransportActions($ClientIP, $ClientPort, $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetCurrentTransportActions",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}
	
	//*****************************************************************************
	/* Function: GetPositionInfo($ClientIP, $ClientPort, $ClientControlURL)
	...............................................................................
	*Liest die Positions Informationen des upnp streams aus und 
        * gibt Ergebnis als Array zurück
	...............................................................................
	Parameters:  
         *   $ClientIP          - IP Adresse der Clients.
         *   $ClientPort        - Übertragungs Port des Clients.
         *   $ClientControlURL  - Stammverzeichnis des Clients
 	--------------------------------------------------------------------------------
	Returns: 
            * Array ()
		   --- Code
                            [Track] => 1
                            [TrackDuration] => 0:07:12
                            [TrackMetaData] =>
                            [TrackURI] =>
                            [RelTime] => 0:04:09
                            [AbsTime] => 5:30:22
                            [RelCount] => 249000
                            [AbsCount] => 19822482
		    ---
	Status: checked
	//////////////////////////////////////////////////////////////////////////////*/
	public function GetPositionInfo(string $ClientIP, string $ClientPort, string $ClientControlURL){
	    return $this->processSoapCall($ClientIP, $ClientPort, $ClientControlURL,
	
	                           "urn:schemas-upnp-org:service:AVTransport:1",
	
	                           "GetPositionInfo",
	
	                           array( 
	                                  new SoapParam('0'             	 ,"InstanceID"       )								  									   
	                                )
		);
	}




 
	




	//*****************************************************************************
	/* Function: processSoapCall($ip, $port, $path, $uri, $action, $parameter)
	...............................................................................
	sendet einen HTTP string per curl an Netzwerk device
	...............................................................................
	Parameters:  
         *  $ip         - IP des device
         *  $port       - Port des device
         *  $path       - ClientControlURL
         *  $uri        - "urn:schemas-upnp-org:service:AVTransport:1"
         *  $action     - "GetPositionInfo"
         *  $parameter  - Soap Parameter
	--------------------------------------------------------------------------------
	Returns: 
         * output_headers
         *      Sofern vorhanden, wird dieses Array mit den Headern des SOAP-Response gefüllt.
         *      Sonst Fehler Meldung  
         */	
	Protected function processSoapCall(string $ip, string $port, string $path, string $uri, string $action, array $parameter)
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



	//*****************************************************************************
	/* Function: resolveErrorCode($path,$errorCode)
	...............................................................................
	FehlerCode Auswertung. Wandelt Error Code in einen Fehler Text um. 
	...............................................................................
	Parameters:  
         *  $path  -   "/AVTransport/ctrl" // "/RenderingControl/ctrl" // "/ContentDirectory/ctrl" 
         *  $errorCode    
	--------------------------------------------------------------------------------
	Returns: 
         * $errorList[$path][$errorCode]
         * "UNKNOWN"
         */
  	private function resolveErrorCode($path, $errorCode){
                $this->SendDebug('resolveErrorCode', $path.'- '.$errorCode, 0);
   		$errorList = array( "/AVTransport/ctrl"      => array(
                                                                           "701" => "ERROR_AV_UPNP_AVT_INVALID_TRANSITION",
                                                                           "702" => "The media does not contain any contents that can be played. ",
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

