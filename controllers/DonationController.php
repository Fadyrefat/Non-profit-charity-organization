<?php

class DonationController {

    public function Index() {
        require_once 'views/Donation/index.html';
    }
    public function DonerForm(){
        require_once 'views/Donation/addDoner.html';
    }
    public function addDoner($data){
        require_once 'models/Donation/Donor.php';
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'] ?? null;
        Donor::create($name,$email,$phone);
        }
    public function DonationForm(){
        require_once 'models/Donation/Donor.php';
        $donors = Donor::getDonors();
        require_once 'views/Donation/addDonation.html';
    }

    public function addDonation($data){
          
    $donor_id = $_POST['donor_id'];
    $amount = $_POST['amount'] ;
    $donation_type = $_POST['donation_type'];
    
    require_once 'models/donation/DonationFactory.php';
    require_once 'models/donation/ReceiptDecorator.php';
    require_once 'models/donation/AckDecorator.php';
    $donation = DonationFactory::createDonation($donor_id,$donation_type,$amount);

    $receipt = isset($_POST['receipt']) ? 1 : 0;
    if($receipt)
       {$donation = new ReceiptDecorator($donation);}

    $ack = isset($_POST['acknowledgment']) ? 1 : 0;
    if($ack)
       {$donation = new AckDecorator($donation);}    

    $donation->donate();

    }

}
