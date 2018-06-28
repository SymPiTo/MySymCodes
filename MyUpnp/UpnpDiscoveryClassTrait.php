<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * class: UpnpDiscoveryClassTrait
 */
trait UpnpDiscoveryClassTrait {

    /*//////////////////////////////////////////////////////////////////////////////
    UPNP_Discovery_Functions_V2.6                             2015 by André Liebmann
    21.07.2015
    --------------------------------------------------------------------------------
    Funktionssammlung Discovery
    --------------------------------------------------------------------------------
    mögliche Aufrufe:
    $ST_ALL = "ssdp:all";
    $ST_RD = "upnp:rootdevice";
    $ST_AV = "urn:dial-multiscreen-org:service:dial:1";
    $ST_MR = "urn:schemas-upnp-org:device:MediaRenderer:1";
    $ST_MS = "urn:schemas-upnp-org:device:MediaServer:1";
    $ST_CD = "urn:schemas-upnp-org:service:ContentDirectory:1";
    $ST_RC = "urn:schemas-upnp-org:service:RenderingControl:1";
    --------------------------------------------------------------------------------
    für Tests:
    $ST = $ST_ALL;
    print_r (mSearch($ST));
    //////////////////////////////////////////////////////////////////////////////*/

    Protected function mSearch($ST){
            error_reporting(E_ALL | E_STRICT);

            //Variablen//////////////////////////////////////////////////////////////////
            $USER_AGENT = 'IP-Symcon, UPnP/1.0, IPSKernelVersion:'.IPS_GetKernelVersion();
            $MULTICASTIP = '239.255.255.250';
            $PORT = '1900';
            $MX = '3';
            $MAN = 'ssdp:discover';
            $socketTimeout =   array(
        "sec"=>60, // Timeout in seconds
        "usec"=>0  // I assume timeout in microseconds
        );

            //Message////////////////////////////////////////////////////////////////////
            $msg  = 'M-SEARCH * HTTP/1.1' . "\r\n";
            $msg .= 'HOST: '.$MULTICASTIP.':1900' . "\r\n";
            $msg .= 'MAN: "'. $MAN .'"' . "\r\n";
            $msg .= 'MX: '. $MX ."\r\n";
            $msg .= 'ST: ' . $ST ."\r\n";
            $msg .= 'USER-AGENT: '. $USER_AGENT . "\r\n";
            $msg .= '' ."\r\n";

            $this->SendDebug('mSearch', 'Erzeuge Multicast SSDP-Abfrage', 0);

            //Erzeuge Socket/////////////////////////////////////////////////////////////
            if (!$socket = socket_create( AF_INET, SOCK_DGRAM, getprotobyname('udp') )){
                $this->SendDebug('mSearch', 'socket_create() schlug fehl: Grund: '.socket_strerror(socket_last_error()), 0);
            }        
            else {
                $this->SendDebug('mSearch', 'Socketerstellung socket_create OK.', 0);
            }

            //Erzeuge Socketoptionen/////////////////////////////////////////////////////
            if (!socket_set_option( $socket, SOL_SOCKET, SO_BROADCAST, 1 )){
                $this->SendDebug('mSearch', 'Kann keine Option setzen für Socket: '.socket_strerror(socket_last_error()), 0);                    
            }
            else {
                $this->SendDebug('mSearch', 'Socketoption SO_BROADCAST OK.', 0);
            }

            if (!socket_set_option( $socket, SOL_SOCKET, SO_REUSEADDR, 1 )){
                $this->SendDebug('mSearch', 'Kann keine Option setzen für Socket: '.socket_strerror(socket_last_error()), 0);                           
            }
            else {
                $this->SendDebug('mSearch', 'Socketoption SO_REUSEADDR OK.', 0);
            }

            if (!socket_set_option( $socket, SOL_SOCKET, SO_RCVTIMEO, $socketTimeout )) {
                $this->SendDebug('mSearch', 'Kann keine Option setzen für Socket: '.socket_strerror(socket_last_error()), 0);                     
            }
            else { 
                $this->SendDebug('mSearch', 'Socketoption SO_RCVTIMEO OK.', 0);
            }

            //Sende Message//////////////////////////////////////////////////////////////
            if (!socket_sendto( $socket, $msg, strlen( $msg ), 0, $MULTICASTIP, $PORT)){
                 $this->SendDebug('mSearch', 'socket_sendto() schlug fehl.\nGrund: ($result) '.socket_strerror(socket_last_error($socket)), 0);                     
            }
            else {
                $this->SendDebug('mSearch', 'socket_sendto OK.\nMessage:'.$msg .'gesendet', 0);
            }

            //RESPONSE empfangen und bearbeiten -> parseMSearchResponse()////////////////
            $this->SendDebug('mSearch', 'Lese Response....', 0);
            
            $response = array();
            $buf = '';

            do
                    {
                    unset( $buf );
                    //socket_recv( $socket, $buf, 4096, MSG_WAITALL );
                    socket_recvfrom($socket, $buf, 4096, 0, $MULTICASTIP, $PORT);
                    if( !is_null($buf) ) $response[] = $this->parseMSearchResponse( $buf );
                    //$this->stdout("Buffer:\n$buf");
                    }

            while( !is_null($buf) );

            //SOCKET schliessen//////////////////////////////////////////////////////////
            $this->SendDebug('mSearch', 'Schliesse Socket...', 0);
        //	socket_shutdown($socket, 2);
            socket_close( $socket );
           
            //$this->SendDebug('mSearch', 'Response:'.$response, 0);
        return $response;
    }


    //------------------------------------------------------------------------------
    //UPNP_Discovery_Functions.ips.php---------------------2013 von André Liebmann--
    //UPNP_Discovery_Functions_V1.3-------------------------------------------------
    //07.09.2013--------------------------------------------------------------------
    //------------------------------------------------------------------------------
    //mögliche Aufrufe:-------------------------------------------------------------
    //$ST_ALL = "ssdp:all";
    //$ST_RD = "upnp:rootdevice";
    //$ST_AV = "urn:dial-multiscreen-org:service:dial:1";
    //$ST_MS = "urn:schemas-upnp-org:device:MediaServer:1";
    //$ST_CD = "urn:schemas-upnp-org:service:ContentDirectory:1";
    //$ST_RC = "urn:schemas-upnp-org:service:RenderingControl:1";
    //------------------------------------------------------------------------------
    //Test:-------------------------------------------------------------------------
    //$ST = $ST_ALL;
    //print_r (mSearch($ST));
    //------------------------------------------------------------------------------

    Protected function mSearchNeu($ST){
        //Variablen------------------------------------------------------------------
        $USER_AGENT = 'WINDOWS, UPnP/1.0, Intel MicroStack/1.0.1497';
        $MULTICASTIP = '239.255.255.250';
        $MX = 10;
        $MAN = 'ssdp:discover';
        $sockTimout = '10';

        //Message--------------------------------------------------------------------
        $msg  = 'M-SEARCH * HTTP/1.1' . '\r\n';
        $msg .= 'HOST: '.$MULTICASTIP.':1900' . '\r\n';
        $msg .= 'MAN: "'. $MAN .'"' . '\r\n';
        $msg .= 'MX: '. $MX .'\r\n';
        $msg .= 'ST: ' . $ST .'\r\n';
        $msg .= 'USER-AGENT: '. $USER_AGENT . '\r\n';
        $msg .= '' ."\r\n";

        //MULTICAST MESSAGE absetzen-------------------------------------------------
        $sock = socket_create( AF_INET, SOCK_DGRAM, getprotobyname('udp') );
        $opt_ret = socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
        $send_ret = socket_sendto( $sock, $msg, strlen( $msg ), 0, $MULTICASTIP, 1900);

        //TIMEOUT setzen-------------------------------------------------------------
        socket_set_option( $sock, SOL_SOCKET, SO_RCVTIMEO, array( 'sec'=>$sockTimout, 'usec'=>'0' ) );

        //RESPONSE empfangen und bearbeiten (parseMSearchResponse())-----------------
        $response = array();
        do
        {
                unset( $buf );
                @socket_recv( $sock, $buf, 1024, MSG_WAITALL );
                if( !is_null($buf) ) $response[] = parseMSearchResponse( $buf );
        }

        while( !is_null($buf) );

        //SOCKET schliessen----------------------------------------------------------
        socket_close( $sock );

        return $response;
        print_r ($response);
    }



    /*//////////////////////////////////////////////////////////////////////////////
    function parseMSearchResponse( $response )
    Aufarbeitung der SSDP-Response
    /*//////////////////////////////////////////////////////////////////////////////

    Protected function parseMSearchResponse( $response ){
        $responseArray = explode( "\r\n", $response );

        $parsedResponse = array();

        //Response auslesen und bearbeiten, da häufig unterschiedlich in Groß- und Kleinschreibung sowie Leerzeichen dazwischen
        foreach( $responseArray as $row )
                {
                if( stripos( $row, 'HTTP' ) === 0 ){
                        $parsedResponse['HTTP'] = $row;
                        }
                if( stripos( $row, 'CACHE-CONTROL:' ) === 0 )
                        {
                        $parse = str_ireplace( 'CACHE-CONTROL:', '', $row );

                        $parsedResponse['CACHE-CONTROL'] = str_ireplace( 'CACHE-CONTROL:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['CACHE-CONTROL'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['CACHE-CONTROL'] = $parse;
                                        }
                        }
                if( stripos( $row, 'DATE:') === 0 )
                        {
                        $parse = str_ireplace( 'DATE:', '', $row );

                        $parsedResponse['DATE'] = str_ireplace( 'DATE:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['DATE'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['DATE'] = $parse;
                                        }
                        }
                if( stripos( $row, 'LOCATION:') === 0 )
                        {
                        $parse = str_ireplace( 'LOCATION:', '', $row );

                        $parsedResponse['LOCATION'] = str_ireplace( 'LOCATION:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['LOCATION'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['LOCATION'] = $parse;
                                        }
                        }
                if( stripos( $row, 'SERVER:') === 0 )
                        {
                        $parse = str_ireplace( 'SERVER:', '', $row );

                        $parsedResponse['SERVER'] = str_ireplace( 'SERVER:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['SERVER'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['SERVER'] = $parse;
                                        }
                        }
                if( stripos( $row, 'ST:') === 0 )
                        {
                        $parse = str_ireplace( 'ST:', '', $row );

                        $parsedResponse['ST'] = str_ireplace( 'ST:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['ST'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['ST'] = $parse;
                                        }
                        }
                if( stripos( $row, 'USN:') === 0 )
                        {
                        $parse = str_ireplace( 'USN:', '', $row );

                        $parsedResponse['USN'] = str_ireplace( 'USN:', '', $row );

                        //prüfen, ob erstes Zeichen ein "Leerzeichen" ist, da
                        if ( 0 === strpos($parse, " ") )
                                {
                                $parsedResponse['USN'] = trim( $parse, " " );

                        }else{
                                        $parsedResponse['USN'] = $parse;
                                        }
                        }
        }
        return $parsedResponse;
    }
	
    /*//////////////////////////////////////////////////////////////////////////////
    function array_multi_unique($multiArray)
    Entfernung von Duplikaten
    /*//////////////////////////////////////////////////////////////////////////////

    Protected function array_multi_unique($multiArray){
            $uniqueArray = array();

            foreach($multiArray as $subArray) //alle Array-Elemente durchgehen
                    {
                    if(!in_array($subArray, $uniqueArray)) //prüfen, ob Element bereits im Unique-Array
                            {
                            $uniqueArray[] = $subArray; //Element hinzufügen, wenn noch nicht drin
                            }
                    }
            return $uniqueArray;
    }
	
    /*//////////////////////////////////////////////////////////////////////////////
    function directory($Directory)
    nur ein Verzeichnis und keine komplette URL als ServerContentDirectory,
    RenderingControlURL und ControlURL wird übergeben
    /*//////////////////////////////////////////////////////////////////////////////

    Protected function directory($Directory){
            if ( stristr($Directory, "http") == true) //komplette URL vorhanden ?
                    {
                    $vars1 = explode("//", $Directory, 2);	//cut nach http
                    $cutted1 			= $vars1[0];
                    $cutted2 			= $vars1[1];
                    $vars2 = explode("/", $cutted2, 2);	//cut nach Port (", 2" --> 2 Teile)
                    $cutted3 			= $vars2[0];
                    $Directory 			= $vars2[1];
                    }
            if( strpos($Directory, "/") == 0 ) //prüfen, ob erstes Zeichen ein "/" ist
                    {
                    $raw_Directory = trim( end($Directory), "/" );
                    }
            else
                    {
                    $raw_Directory = $Directory;
                    }
            $parsed_Directory = ("/".$raw_Directory);

            return $parsed_Directory;
    }
	
    /*//////////////////////////////////////////////////////////////////////////////
    function create_UPNP_Device_Array()

    /*///////////////////////////////////////////////////////////////////////////////

    Protected function create_UPNP_Device_Array($Device_SSDPArray) {
            //include_once ("13355 /*[DLNA\UPnP Class\UPnP_API]*/.ips.php"); //UPNP_Klasse
            //include_once ('32114 /*[Testumgebung\Logger\Logger]*/.ips.php');
            //$DLNA = new DLNA();

            $LoadArray = GetValue($this->GetIDForIdent("upnp_ClientArray"));
            $Device_Array = unserialize($LoadArray);
            //$this->stdout(print_r($Device_Array));
            $this->SendDebug('create_UPNP_Device_Array', '$Device_Array:', 0);

            for($i=0,$size=count($Device_SSDPArray);$i<$size;$i++)
            {

                            $DeviceDescription = $Device_SSDPArray[$i]['LOCATION'];

                            //$this->stdout(print_r($DeviceDescription));
                            $this->SendDebug('create_UPNP_Device_Array', 'DeviceDescription:'.$DeviceDescription, 0);
                            //Rootverzeichnis/IP/Port

                            $vars1 = explode("//", $DeviceDescription, 2);	//cut nach http
                            $cutted1 	= $vars1[0];
                            $cutted2 	= $vars1[1];
                            $vars2 = explode("/", $cutted2, 2);					//cut nach Port
                            $cutted3 	= $vars2[0];
                            $cutted4 	= $vars2[1];
                            $vars3 = explode(":", $cutted3, 2);					//IP und Port
                            $DeviceIP 	= $vars3[0];
                            $DevicePort = $vars3[1];

                            $root = "http://"."$cutted3"."/";
                            /*///////////////////////////////////////////////////////////////////////////
                            nur auswerten, wenn enicht 121.0.0.1
                            -----------------------------------------------------------------------------
                            /*///////////////////////////////////////////////////////////////////////////
                    if ($DeviceIP != "127.0.0.1"){
                            /*///////////////////////////////////////////////////////////////////////////
                            //nur auswerten, wenn erreichbar und auslesbar
                            //-----------------------------------------------------------------------------
                            //nach einem SSDP-Request immer erreichbar,
                            //somit $Device_Party_Array[$i]['FriendlyName'] nur bei Wiederholung aufgerufen
                            ///////////////////////////////////////////////////////////////////////////*/

                            if ($this->ping($DeviceIP, $DevicePort, $timeout="1") == "false")
                            {
                                    /*////////////////////////////////////////////////////////////////////////
                                    nicht erreichbares Device:
                                    FriendlyName aus DeviceArray (vorher geladen) sowie Image "not connected"
                                    /*////////////////////////////////////////////////////////////////////////

                                    $DeviceDescription = "";
                                    $root = "";
                                    $DeviceIP = "";
                                    $DevicePort = "";
                                    $modelName = "";
                                    $UDN = "";

                                    if(isset($Device_Array[$i]['FriendlyName']))
                                    {
                                            $friendlyName = $Device_Array[$i]['FriendlyName'];
                                    }
                                    else
                                    {
                                            $friendlyName = "unknown";
                                    }

                                    $iconurl = "";
                                    $DeviceControlServiceType = "";
                                    $DeviceControlURL = "";
                                    $DeviceRenderingServiceType = "";
                                    $DeviceRenderingControlURL = "";
                                    $DeviceActiveIcon = ("image/not connected.png");

                                    
                                    $this->SendDebug('create_UPNP_Device_Array', 'Device nicht erreichbar !', 0);
                            }
                            else
                            {//2
                                    $ctx = stream_context_create(array('http' => array('timeout' => 1000)));

                                    if(!file_get_contents("$DeviceDescription", -1, $ctx))
                                    {
                                        
                                        $this->SendDebug('create_UPNP_Device_Array', 'DeviceDescription nicht ladbar !', 0);
                                    }
                                    else
                                            {
                                          
                                            $this->SendDebug('create_UPNP_Device_Array', 'Device erreichbar !', 0);
                                           
                                            $this->SendDebug('create_UPNP_Device_Array', 'DeviceDescription ladbar !', 0);
                                            /*/////////////////////////////////////////////////////////////////////
                                            erreichbares Device:
                                            Description des Gerätes abrufen und auswerten
                                            /*/////////////////////////////////////////////////////////////////////

                                            $xml = @file_get_contents("$DeviceDescription", -1);
                                            $xml	= str_replace("&", "&amp;", $xml);
                                            //print_r($xml);

                                            libxml_use_internal_errors(true);

                                            $xmldesc = new SimpleXMLElement($xml);

                                            //Modelname lesen
                                            $modelName = (string)$xmldesc->device->modelName;

                                            //UDN lesen
                                            $UDN = (string)$xmldesc->device->UDN;

                                            //Name 
                                            $friendlyName_raw = $xmldesc->device->friendlyName;
                                            $this->SendDebug('create_UPNP_Device_Array', 'friendlyName:'.$friendlyName, 0);

                                            if (stripos($friendlyName_raw, " ")) //wenn Leerzeichen nur ersten Teil
                                            {
                                                    $var = explode(" ", $friendlyName_raw);
                                                    $friendlyName_raw	= $var[0];
                                            }
                                            if (stripos($friendlyName_raw, "/")) //wenn "/" nur ersten Teil
                                            {
                                                    $var = explode(" ", $friendlyName_raw);
                                                    $friendlyName_raw	= $var[0];
                                            }


                                            $friendlyName = substr("$friendlyName_raw", 0, 10);

                                            if ($modelName == "Sonos PLAY:1"){
                                                    $friendlyName = "SonosK";
                                            }

                                            if ($modelName == "Sonos PLAY:3"){
                                                    $friendlyName = "SonosSZ";
                                            }

                                            /*/////////////////////////////////////////////////////////////////////
                                            verfügbare Icons ermitteln
                                            /*/////////////////////////////////////////////////////////////////////

                                            if (isset($xmldesc->device->iconList))
                                                    {
                                               $icons = array();
                                                    //Icons auslesen und nach Grösse 120x120 suchen
                                                    foreach($xmldesc->device->iconList->icon as $icon)
                                                    {
                                                            //Icons durchsuchen nach 1. Größe 120x120
                                                            if ($icon->height == "120")
                                                                          {
                                                                    $icons[] = $icon->url;

                                                                    //PNG Icon mit Größe 120x120 suchen
                                                                    if (preg_grep('/png/i', $icons))
                                                                    {
                                                                            $iconpng = preg_grep('/png/i', $icons);
                                                                            $icon120 = end($iconpng);
                                                                    }
                                                                    //wenn nicht vorhanden letztes mit Größe 120x120 übernehmen
                                                                    else
                                                                    {
                                                                            $icon120 = end($icons);
                                                                    }
                                                    }
                                                            else
                                                            {
                                                                    $icons[] = $icon->url;
                                                                    $icon120 = $icons[0];
                                                            }
                                                    }

                                                            //wenn komplette URL bereits enthalten
                                                            if ( stristr($icon120, "http") )
                                                            {
                                                                    $iconurl = (string)$icon120;
                                                            }
                                                            else //sonst $root davor setzen und vorher auf"/" prüfen
                                                            {
                                                                    if ( strpos($icon120, "/") == 0 ) //prüfen, ob erstes Zeichen ein "/" ist
                                                                            {
                                                                            $iconurl = (string)$root.(trim( end($icon120), "/" ));
                                                                            }
                                                                    else
                                                                    {
                                                                            $iconurl = (string)$root.$icon120;
                                                                    }
                                                            }
                                            }
                                            else //wenn kein icon vorhanden Dummy nehmen
                                            {
                                                    $iconurl = ("image/UPNP.png");
                                            }

                                            /*/////////////////////////////////////////////////////////////////////
                                            Services auslesen und auf AVTransport und RenderingControl beschränken
                                            /*/////////////////////////////////////////////////////////////////////

                                            if (isset ($xmldesc->device->serviceList->service))
                                            {
                                                    foreach($xmldesc->device->serviceList->service as $service)
                                                    {
                                                            $serviceType = $service->serviceType;

                                                            if (stristr($serviceType, "urn:schemas-upnp-org:service:AVTransport"))
                                                            {
                                                                    $DeviceControlServiceType = (string)$service->serviceType;
                                                                    $Directory = $service->controlURL;
                                                                    $this->SendDebug('Directory CONTROLURL',  $Directory, 0);
                                                                    $DeviceControlURL = $this->directory($Directory);
                                                            }
                                                            else
                                                            {
                                                                    $DeviceControlServiceType = "";
                                                                    $DeviceControlURL = "";
                                                            }
                                                            if (stristr($serviceType, "urn:schemas-upnp-org:service:RenderingControl"))
                                                            {
                                                                    $DeviceRenderingServiceType = (string)$service->serviceType;
                                                                    $Directory = $service->controlURL;
                                                                    $this->SendDebug('Directory CONTROLURL', $Directory, 0);
                                                                    $DeviceRenderingControlURL = $this->directory($Directory);
                                                            }
                                                            else
                                                            {
                                                                    $DeviceRenderingServiceType = "";
                                                                    $DeviceRenderingControlURL = "";
                                                            }
                                                    }
                                            }
                                    }
                            }//2
                            // alle Lokalen Devices ausschliesen
                            if ($DeviceIP != "127.0.0.1"){
                                    //Ausgangszustand nicht selektiert, also ohne Icon-Haken
                                    $DeviceActiveIcon = "";

                                    //DeviceArray erstellen
                                    $Device_Array[$i]['DeviceDescription'] = $DeviceDescription;
                                    $Device_Array[$i]['Root'] = $root;
                                    $Device_Array[$i]['DeviceIP'] = $DeviceIP;
                                    $Device_Array[$i]['DevicePort'] = $DevicePort;
                                    $Device_Array[$i]['ModelName'] = $modelName;
                                    $Device_Array[$i]['UDN'] = $UDN;
                                    $Device_Array[$i]['FriendlyName'] = $friendlyName;
                                    $Device_Array[$i]['IconURL'] = $iconurl;
                                    $Device_Array[$i]['DeviceControlServiceType'] = $DeviceControlServiceType;
                                    $Device_Array[$i]['DeviceControlURL'] = $DeviceControlURL;
                                    $Device_Array[$i]['DeviceRenderingServiceType'] = $DeviceRenderingServiceType;
                                    $Device_Array[$i]['DeviceRenderingControlURL'] = $DeviceRenderingControlURL;
                                    $Device_Array[$i]['DeviceActiveIcon'] = $DeviceActiveIcon;
                            }

                    }
            }//1
            // Array von Doppelten Einträgen bereinigen
            $Clean_Device_Array = $this->array_multi_unique($Device_Array);
            $SaveArray = serialize($Clean_Device_Array);
            SetValue($this->GetIDForIdent("upnp_ClientArray"), $SaveArray);
            //$this->stdout("DeviceArray:$Device_Array\r\n");
             $this->SendDebug('create_UPNP_Device_Array', 'ENDE: Client Array abgespeichert!' , 0);
            //print_r($Clean_Device_Array);
            return $SaveArray;
    }
	
    /*//////////////////////////////////////////////////////////////////////////////
    function create_UPNP_Server_Array()
    /*//////////////////////////////////////////////////////////////////////////////
    Protected function create_UPNP_Server_Array($Server_SSDPArray){
    /*//////////////////////////////////////////////////////////////////////////////
    Auslesen des Arrays und Abfrage der Descriptions, wenn erreichbar
    /*//////////////////////////////////////////////////////////////////////////////
            //include_once ("13355 /*[DLNA\UPnP Class\UPnP_API]*/.ips.php"); //UPNP_Klasse
            //include_once ('32114 /*[Testumgebung\Logger\Logger]*/.ips.php');
            //$DLNA = new DLNA();

            $LoadArray = GetValue(self::ID_SERVER_ARRAY);
            $Server_Array = unserialize($LoadArray);
            //print_r($Server_Array);

            //$Server_Array = array();


            for($i=0,$size=count($Server_SSDPArray);$i<$size;$i++)
            {
                    $ServerDescription = $Server_SSDPArray[$i]['LOCATION'];

                   
                    $this->SendDebug('create_UPNP_Server_Array', 'ServerDescription'.$ServerDescription, 0);   
                    //Rootverzeichnis/IP/Port----------------------------------------------------

                    $vars1 = explode("//", $ServerDescription, 2);	//cut nach http
                    $cutted1 	= $vars1[0];
                    $cutted2 	= $vars1[1];
                    $vars2 = explode("/", $cutted2, 2);					//cut nach Port
                    $cutted3 	= $vars2[0];
                    $cutted4 	= $vars2[1];
                    $vars3 = explode(":", $cutted3, 2);					//IP und Port
                    $ServerIP 	= $vars3[0];
                    $ServerPort = $vars3[1];

                    $root = "http://"."$cutted3"."/";

                    if ($ServerIP != "127.0.0.1"){

                            /*///////////////////////////////////////////////////////////////////////////
                            nur auswerten, wenn erreichbar und auslesbar
                            -----------------------------------------------------------------------------
                            nach einem SSDP-Request immer erreichbar,
                            somit $Device_Party_Array[$i]['FriendlyName'] nur bei Wiederholung aufgerufen
                            /*///////////////////////////////////////////////////////////////////////////

                            if ($this->ping($ServerIP, $ServerPort, $timeout="1") == "false")
                            {
                                    /*////////////////////////////////////////////////////////////////////////
                                    nicht erreichbarer Server:
                                    FriendlyName aus DeviceArray sowie Image "not connected"
                                    /*////////////////////////////////////////////////////////////////////////

                                    $ServerDescription = "";
                                    $root = "";
                                    $ServerIP = "";
                                    $ServerPort = "";
                                    $modelName = "";
                                    $UDN = "";

                                    if(isset($Server_Array[$i]['FriendlyName']))
                                            {
                                            $friendlyName = $Server_Array[$i]['FriendlyName'];
                                            }
                                    else
                                            {
                                            $friendlyName = "unknown";
                                            }

                                    $iconurl = "";
                                    $ServerServiceType = "";
                                    $ServerContentDirectory = "";
                                    $ServerActiveIcon = ("image/not connected.png");
                                    $this->SendDebug('create_UPNP_Server_Array', 'Server nicht erreichbar !', 0);  
                                    
                            }
                            else
                            {//2
                                    $ctx = stream_context_create(array('http' => array('timeout' => 1000)));

                                    if(!file_get_contents("$ServerDescription", -1, $ctx))
                                    {
                                      
                                       $this->SendDebug('create_UPNP_Server_Array', 'ServerDescription nicht ladbar !', 0); 
                                    }
                                    else
                                    {
                                            
                                            $this->SendDebug('create_UPNP_Server_Array', 'Server erreichbar !', 0);     
                                             
                                            $this->SendDebug('create_UPNP_Server_Array', 'ServerDescription ladbar !', 0); 
                                            /*/////////////////////////////////////////////////////////////////////
                                            erreichbarer Server:
                                            Description des Server abrufen und auswerten
                                            /*/////////////////////////////////////////////////////////////////////

                                            $xml = @file_get_contents("$ServerDescription", -1);
                                            $xml	= str_replace("&", "&amp;", $xml);
                                            //print_r($xml);

                                            libxml_use_internal_errors(true);

                                            $xmldesc = new SimpleXMLElement($xml);

                                            //Modelname lesen
                                            $modelName = (string)$xmldesc->device->modelName;

                                            //UDN lesen
                                            $UDN = (string)$xmldesc->device->UDN;

                                            //Name -> nur erste 10 Zeichen anzeigen, sonst passt es nicht in den Button
                                            $friendlyName_raw = $xmldesc->device->friendlyName;

                                            if (stripos($friendlyName_raw, " ")) //wenn Leerzeichen nur ersten Teil
                                            {
                                                    $var = explode(" ", $friendlyName_raw);
                                                    $friendlyName_raw	= $var[0];
                                            }
                                            if (stripos($friendlyName_raw, "/")) //wenn "/" nur ersten Teil
                                            {
                                                    $var = explode(" ", $friendlyName_raw);
                                                    $friendlyName_raw	= $var[0];
                                            }

                                            $friendlyName = substr("$friendlyName_raw", 0, 10);

                                            /*/////////////////////////////////////////////////////////////////////
                                            verfügbare Icons ermitteln
                                            /*/////////////////////////////////////////////////////////////////////

                                            if (isset($xmldesc->device->iconList))
                                            {
                                               $icons = array();
                                                    //Icons auslesen und nach Grösse 120x120 suchen
                                                    foreach($xmldesc->device->iconList->icon as $icon)
                                                    {
                                                            //Icons durchsuchen nach 1. Größe 120x120
                                                            if ($icon->height == "120")
                                                            {
                                                                    $icons[] = $icon->url;

                                                                    //PNG Icon mit Größe 120x120 suchen
                                                                    if (preg_grep('/png/i', $icons))
                                                                    {
                                                                            $iconpng = preg_grep('/png/i', $icons);
                                                                            $icon120 = end($iconpng);
                                                                    }
                                                                    //wenn nicht vorhanden letztes mit Größe 120x120 übernehmen
                                                                    else
                                                                    {
                                                                            $icon120 = end($icons);
                                                                    }
                                                    }
                                                            else
                                                            {
                                                                    $icons[] = $icon->url;
                                                                    $icon120 = $icons[0];
                                                            }
                                                    }

                                                    //wenn komplette URL bereits enthalten
                                                    if ( stristr($icon120, "http") )
                                                    {
                                                            $iconurl = (string)$icon120;
                                                    }
                                                    else //sonst $root davor setzen und vorher auf"/" prüfen
                                                    {
                                                            if ( strpos($icon120, "/") == 0 ) //prüfen, ob erstes Zeichen ein "/" ist
                                                            {
                                                                    $iconurl = (string)$root.(trim( end($icon120), "/" ));
                                                            }
                                                            else
                                                            {
                                                                    $iconurl = (string)$root.$icon120;
                                                            }
                                                    }
                                            }
                                            else //wenn kein icon vorhanden Dummy nehmen
                                            {
                                                    $iconurl = ("image/UPNP.png");
                                            }

                                            /*/////////////////////////////////////////////////////////////////////
                                            verfügbare Services
                                            /*/////////////////////////////////////////////////////////////////////

                                            //Services auslesen und auf ContentDirectory beschränken------------------
                                            if (isset ($xmldesc->device->serviceList->service))
                                            {
                                                    foreach($xmldesc->device->serviceList->service as $service)
                                                    {
                                                            $serviceType = $service->serviceType;

                                                            if (stristr($serviceType, "urn:schemas-upnp-org:service:ContentDirectory"))
                                                            {
                                                                    $ServerServiceType = (string)$service->serviceType;
                                                                    $Directory = $service->controlURL;
                                                                    $ServerContentDirectory = $this->directory($Directory);
                                                            }
                                                    }
                                            }
                                    }
                            }//2

                            //Ausgangszustand nicht selektiert, also ohne Icon-Haken
                            $ServerActiveIcon = "";

                            //ServerArray erstellen
                            $Server_Array[$i]['ServerDescription'] = $ServerDescription;
                            $Server_Array[$i]['Root'] = $root;
                            $Server_Array[$i]['ServerIP'] = $ServerIP;
                            $Server_Array[$i]['ServerPort'] = $ServerPort;
                            $Server_Array[$i]['ModelName'] = $modelName;
                            $Server_Array[$i]['UDN'] = $UDN;
                            $Server_Array[$i]['FriendlyName'] = $friendlyName;
                            $Server_Array[$i]['IconURL'] = $iconurl;
                            $Server_Array[$i]['ServerServiceType'] = $ServerServiceType;
                            $Server_Array[$i]['ServerContentDirectory'] = $ServerContentDirectory;
                            $Server_Array[$i]['ServerActiveIcon'] = $ServerActiveIcon;

                    }
            }//1

            // Array von Doppelten Einträgen bereinigen
            $Clean_ServerArray = $this->array_multi_unique($Server_Array);
            $SaveArray = serialize($Clean_ServerArray);
            SetValue($this->GetIDForIdent("upnp_ServerArray"), $SaveArray);
            //$this->stdout("DeviceArray:$Server_Array\r\n");
             
            return $SaveArray;
    }



}
