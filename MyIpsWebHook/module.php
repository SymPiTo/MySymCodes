<?php

    class MyIpsWebHook extends IPSModule {

        public function Create() {
                //Never delete this line!
                parent::Create();

                $this->RegisterPropertyString("Username", "");
                $this->RegisterPropertyString("Password", "");
        }

        public function ApplyChanges() {
                //Never delete this line!
                parent::ApplyChanges();
            $this->RegisterHook("/hook/myipshook");

        }

        /**
        * This function will be called by the hook control. Visibility should be protected!
        */
        protected function ProcessHookData() { 
            
            
if(!isset($_SERVER['PHP_AUTH_USER']))
    $_SERVER['PHP_AUTH_USER'] = "";
if(!isset($_SERVER['PHP_AUTH_PW']))
    $_SERVER['PHP_AUTH_PW'] = "";
 
if(($_SERVER['PHP_AUTH_USER'] != "Symcon") || ($_SERVER['PHP_AUTH_PW'] != "passwort")) {
    header('WWW-Authenticate: Basic Realm="MyIpsHomeCOntrol"');
    header('HTTP/1.0 401 Unauthorized');
    echo "Authorization required";
    return;
}
echo "Willkommen im geschützten Bereich";
            
            $root = realpath(__DIR__ . "/www");

            //reduce any relative paths. this also checks for file existance
            $path = realpath($root . "/" . substr($_SERVER['SCRIPT_NAME'], strlen("/hook/myipshook/")));
            if($path === false) {
                    http_response_code(404);
                    die("File not found!");
            }
                if(substr($path, 0, strlen($root)) != $root) {
                        http_response_code(403);
                        die("Security issue. Cannot leave root folder!");
                }
			//check dir existance
                if(substr($_SERVER['SCRIPT_NAME'], -1) != "/") {
                    if(is_dir($path)) {
                        http_response_code(301);
                        //header("Location: " . $_SERVER['SCRIPT_NAME'] . "/\r\n\r\n");
                        return;
                    }
                }   
                //append index
                if(substr($_SERVER['SCRIPT_NAME'], -1) == "/") {
                    if(file_exists($path . "/index.html")) {
                        $path .= "/index.html";
                        echo $path;
                    } 
                    else if(file_exists($path . "/index.php")) {
                        $path .= "/index.php";
                    }
                }
                
                $extension = pathinfo($path, PATHINFO_EXTENSION);

                if($extension == "php") {
                        include_once($path);
                } else {
                    header("Content-Type: ".$this->GetMimeType($extension));
                    readfile($path);
                }
                
        }  
        
        private function RegisterHook($WebHook) {
                $ids = IPS_GetInstanceListByModuleID("{015A6EB8-D6E5-4B93-B496-0D3F77AE9FE1}");
                if(sizeof($ids) > 0) {
                        $hooks = json_decode(IPS_GetProperty($ids[0], "Hooks"), true);
                        $found = false;
                        foreach($hooks as $index => $hook) {
                                if($hook['Hook'] == $WebHook) {
                                        if($hook['TargetID'] == $this->InstanceID)
                                                return;
                                        $hooks[$index]['TargetID'] = $this->InstanceID;
                                        $found = true;
                                }
                        }
                        if(!$found) {
                                $hooks[] = Array("Hook" => $WebHook, "TargetID" => $this->InstanceID);
                        }
                        IPS_SetProperty($ids[0], "Hooks", json_encode($hooks));
                        IPS_ApplyChanges($ids[0]);
                }

        }
        
        private function GetMimeType($extension) {

                $lines = file(IPS_GetKernelDirEx()."mime.types");
                foreach($lines as $line) {
                        $type = explode("\t", $line, 2);
                        if(sizeof($type) == 2) {
                                $types = explode(" ", trim($type[1]));
                                foreach($types as $ext) {
                                        if($ext == $extension) {
                                                return $type[0];
                                        }
                                }
                        }
                }
                return "text/plain";

        }

 
    }
 

