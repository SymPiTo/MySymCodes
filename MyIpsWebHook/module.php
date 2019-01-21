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
 

