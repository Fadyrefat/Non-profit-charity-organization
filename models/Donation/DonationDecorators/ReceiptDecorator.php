<?php 
require_once 'DonationDecorator.php';
class ReceiptDecorator extends DonationDecorator{
    public function donate():void{
        parent::donate();
        $this->generateReceipt();

    }
  
        private function generateReceipt(): void {
        echo "Receipt generated and emailed to donor.<br>";
    }
}