trait MyLogger
{
    /**
     * Ergänzt SendDebug um die Möglichkeit Objekte und Array auszugeben.
     *
     * @access protected
     * @param string $Message Nachricht für Data.
     * @param WebSocketFrame|mixed $Data Daten für die Ausgabe.
     * @return int $Format Ausgabeformat für Strings.
     */
    protected function ModErrorLog($ModName, $Text, $array)(
    {
        $path = "/home/pi/pi-share/"; 
        $file=$ModName.log;

        if (!$array){

                $array = '-';
        }

        //prüfen, ob file vorhanden.
        //if (file_exists($path.$filename)) {
 
 
		
		if(($FileHandle = fopen($Directory.$File, "a")) === false) {
			//SetValue($ID_OutEnabled, false);
			Exit;
		}
		if (is_array($array)){
			//$comma_seperated=implode("\r\n",$array);
			$comma_seperated=print_r($array, true);
		}
		else {
			$comma_seperated=$array;
		}
		fwrite($FileHandle, $Text.": ");
		fwrite($FileHandle, $comma_seperated."\r\n");
		fclose($FileHandle);
       //}
    }
}
