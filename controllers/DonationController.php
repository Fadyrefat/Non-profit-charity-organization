<?php
    require_once 'models/Donation/DonationFactory.php';
    require_once 'models/Donation/DonationDecorators/ReceiptDecorator.php';
    require_once 'models/Donation/DonationDecorators/AckDecorator.php';
    require_once __DIR__ . '/../models/Inventory.php';
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

    public function showDonors(){
    require_once __DIR__ . '/../common/iteratorpattern/DonorCollection.php';
    require_once __DIR__ . '/../common/iteratorpattern/DonorIterator.php';

    $DonorCollection=new DonorCollection();
    $DonorIterator=$DonorCollection->createIterator();
    while($DonorIterator->hasnext()){
       $Donor= $DonorIterator->next();
       echo "  ";
       echo $Donor->getName();
    }
    }


    public function addDonation($data){
          
    $donation = DonationFactory::createDonation($data);

    $receipt = isset($data['receipt']) ? 1 : 0;
    if($receipt)
       {$donation = new ReceiptDecorator($donation,$data);}

    $ack = isset($data['acknowledgment']) ? 1 : 0;
    if($ack)
       {$donation = new AckDecorator($donation,$data);}   
  
    $donation->donate();

    }

public function showInventory(){
    $Inventory=Inventory::getInventory();
    require_once 'views/Donation/showInventory.html';
}
}
