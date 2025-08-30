<?php 
require_once 'DonationDecorator.php';
class AckDecorator extends DonationDecorator{
    public function donate():void {
        parent::donate();
        $this->sendAcknowledgement();
    }

    private function sendAcknowledgement(){
        echo "Thank-you acknowledgement sent to donor.<br>";
    }

}