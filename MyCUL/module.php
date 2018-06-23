 <?

class MyCULYSplitter extends IPSModule
{

    public function Create()
    {
	//Never delete this line!
        parent::Create();
		
		//These lines are parsed on Symcon Startup or Instance creation
        //You cannot use variables here. Just static values.
		$this->RequireParent("{6DC3D946-0D31-450F-A8C6-C42DB8D7D4F1}"); //  I/O
    }

    public function ApplyChanges()
    {
	//Never delete this line!
        parent::ApplyChanges();
       
    }
    
    // Data an Child weitergeben
    // Type String, Declaration can be used when PHP 7 is available
    //public function ReceiveData(string $JSONString)
	public function ReceiveData($JSONString)
	{
	 
		// Empfangene Daten vom I/O
		$data = json_decode($JSONString);
		$dataio = json_encode($data->Buffer);
		$this->SendDebug("Splitter ReceiveData:",$dataio,0);
			
		// Hier werden die Daten verarbeitet
		
	 
		// Weiterleitung zu allen Gerät-/Device-Instanzen
		$this->SendDataToChildren(json_encode(Array("DataID" => "{54D0F82F-B268-105F-01B6-7BA5B572EEC7}", "Buffer" => $data->Buffer))); // Splitter Interface GUI
	}
	
	// Type String, Declaration can be used when PHP 7 is available
    //public function ForwardData(string $JSONString)
    public function ForwardData($JSONString)
	{
	 
		// Empfangene Daten von der Device Instanz
		$data = json_decode($JSONString);
		$datasend = $data->Buffer;
		$datasend = json_encode($datasend);
		$this->SendDebug("Splitter Forward Data:",$datasend,0);
			
		// Hier würde man den Buffer im Normalfall verarbeiten
		// z.B. CRC prüfen, in Einzelteile zerlegen
		
		// Weiterleiten zur I/O Instanz
		$result = $this->SendDataToParent(json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => $data->Buffer))); // TX GUI
			
		return $result;
	 
	}
}
