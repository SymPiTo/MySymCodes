  
    
    function Navigation(){ 
        document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
        document.getElementsByClassName("RadioStation")[0].style.width = "0px";
        document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
        document.getElementsByClassName("ServerCtrl")[0].style.width = "0px";
        document.getElementsByClassName("Navigation")[0].style.width = "27vw";
    }
    

   function goodNight(){
       var x = "ghjgh";
   }
   
   function Bye(){
       var x = "ghjgh";
   }

    function SetCDCover(player){
            var x = document.getElementById("placeCDLibhere").childElementCount;
            if (x < 1){
            for(var i=1;i<99;i++){
                            
                            var elem = document.createElement("img");
                            var n = i.toString();
                            var laenge = n.length;

                            if (laenge == 1) {
                                    n = "000" + i.toString();
                            }	
                            if (laenge == 2) {
                                    n = "00" + i.toString();
                            }	
                            if (laenge == 3) {
                                    n = "0" + i.toString();
                            }
                            if (laenge == 4) {
                                    n = i.toString();
                            }
                            elem.setAttribute("src", "CDs/"+ n +".jpg");
                            elem.setAttribute("height", "125");
                            elem.setAttribute("width", "125");
                            elem.setAttribute("alt", n);
                            elem.setAttribute("id", "CDicon" + n);
                             
                             
                            elem.setAttribute("onclick", 'changeAlb("'+ n + player + '");');
                            document.getElementById("placeCDLibhere").appendChild(elem);
                            
                    }
            }
    }

     function changeAlb(wert){
            
             var albumpic = 'CDs/'+ wert.substring(0, 4) +'.jpg';           
            
            document.getElementById("CDimg_A").src=albumpic;

            document.getElementById("CDCover_A").src=albumpic;
            send("command(" + wert.substring(4, wert.length) + ",loadCDPlaylist," + wert.substring(0, 4) + ")") ;
    }
 
 
 
        function setCEOLctrl(){
            document.getElementsByClassName("RadioStation")[0].style.width = "0px";
            document.getElementsByClassName("CEOLctrl")[0].style.width = "26vw";
            send('UPNP' + '*' + 'setClient' + '*' + 'CEOL');			
    }
    
    

       /* ******************************
        *    Denon CEOL FUNKTIONEN   
       ********************************* */

   function setCEOLSource(source){
        send('command(DenonCeol,source,'+ source + ')');
        document.getElementsByClassName("RadioStation")[0].style.width = "0px";

        document.getElementsByClassName("CEOLServerCtrl")[0].style.width = "0px";
        document.getElementsByClassName("Navigation")[0].style.width = "0px";
        switch(source){
            case 'Radio':
                document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
                document.getElementsByClassName("CEOLCDctrl")[0].style.width = "0px";
                document.getElementsByClassName("CoverCD")[0].style.width = "0px";
                document.getElementsByClassName("RadioStation")[0].style.width = "27vw";
                break;
            case 'Server':
                document.getElementsByClassName("CoverCD")[0].style.width = "0px";
                document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
                document.getElementsByClassName("CEOLCDctrl")[0].style.width = "0px";
                document.getElementsByClassName("CEOLServerCtrl")[0].style.width = "27vw";
                break;
            case 'USB':
                break;
            case 'IPOD':
                break;
            case 'AuxA':
                break;
            case 'AuxD':
                break;
            case 'Time':
                document.getElementsByClassName("TimePicker")[0].style.width = "27vw";
                break;
        }

    }
    function DenonCEOLCDctrl(){ 
        document.getElementsByClassName("Left")[0].style.width = "8vw";
        document.getElementsByClassName("MenuMultimedia")[0].style.width = "8vw";
        document.getElementsByClassName("CoverCD")[0].style.width = "0px";
        document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
        document.getElementsByClassName("RadioStation")[0].style.width = "0px";
        document.getElementsByClassName("Ceol")[0].style.width = "58vw"
        document.getElementsByClassName("CEOLCDctrl")[0].style.width = "26vw"; 
    }  
    function DenonCeolCDLib(){ 
            document.getElementsByClassName("CEOLServerCtrl")[0].style.width = "0px";
            document.getElementsByClassName("CEOLctrl")[0].style.width = "0px";
            document.getElementsByClassName("RadioStation")[0].style.width = "0px";
            document.getElementsByClassName("CEOLCDctrl")[0].style.width = "0px";         
            document.getElementsByClassName("CoverCD")[0].style.width = "26vw";
            SetCDCover('DenonCeol');
            send('command(DenonCeol,source,Server)');
 
    }
 
 
       /* ******************************
        *    Samsung TV FUNKTIONEN    
       ********************************* */
    function showTVGuide(){ 
        document.getElementsByClassName("TVINet")[0].style.width = "0px";  
        document.getElementsByClassName("TVChannel")[0].style.width = "0px";         
        document.getElementsByClassName("TVGuide")[0].style.width = "26vw";  
        send('command(TV,Guide,all)');
    }  

    function showTvChannels(){
        document.getElementsByClassName("TVINet")[0].style.width = "0px";  
        document.getElementsByClassName("TVGuide")[0].style.width = "0px";
        document.getElementsByClassName("TVChannel")[0].style.width = "26vw";
    }
 
 
     function showTVINet(){
              
         document.getElementsByClassName("TVGuide")[0].style.width = "0px";
         document.getElementsByClassName("TVChannel")[0].style.width = "0px";
         document.getElementsByClassName("TVINet")[0].style.width = "26vw";   
        
    }
    
    function wwwLink(www){
         //var www01 = "https://www.twitch.tv/summonersinnlive";
         send('command(TV,www,https://www.twitch.tv/summonersinnlive) ');
    }
 
 
        /* ******************************
        *    Rollo FUNKTIONEN    
       ********************************* */
 
     function showTimePicker(){
              
              
         document.getElementsByClassName("RolloB")[0].style.width = "0px";
         document.getElementsByClassName("RolloCtrlB")[0].style.width = "0px";
         document.getElementsByClassName("TimePicker")[0].style.width = "86vw";   
        
    }
    

        

    function slideSwitchTimeRollo(room, group){
        let ctrlClass = "RolloCtrl" + room;
        Timer1.room = room; 
        Timer2.room = room;
        Timer1.group = group;
        Timer2.group = group;
        Timer1.direction = "up";
        Timer2.direction = "down";
        document.getElementsByClassName(ctrlClass)[0].style.width = "0px";
        document.getElementsByClassName("TimePicker")[0].style.width = "26vw"; 
    }
    
function TbtnRolloB1Sub1(){
    
}

function showkeypad(){
    AList.minimize();
}

function showAlarmList(){
    AList.maximize();
    send('command(security,AA,AlarmList)');
}