<?php

class RelianceCDMA {

    public function simCDMA(){
        return 'CMDA sim connected';
    }
}

$cdma = new RelianceCDMA();
//..echo $cdma->simCDMA();


interface GSM {
    public function simGSM();
}

function load(GSM $gsm) {

    echo $gsm->simGSM();
    echo $gsm->latest_offers();
}


class Stel implements GSM{
    
    public function simGSM(){
        return 'Stel is connect';    
    }

    public function latest_offers(){
        echo 'Latest offer will come soon...';    
    }
}

class airtel implements GSM{

    public function simGSM(){
        return 'Airtel is connect 4G';    
    }
 }

 load(new $argv[1]);
