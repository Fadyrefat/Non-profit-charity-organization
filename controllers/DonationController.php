<?php
    require_once 'models/donation/DonationFactory.php';
    require_once 'models/donation/DonationDecorators/ReceiptDecorator.php';
    require_once 'models/donation/DonationDecorators/AckDecorator.php';
    
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
    require_once __DIR__ . '/../common/iteratorpattern/VolunteerCollection.php';
    require_once __DIR__ . '/../common/iteratorpattern/VolunteerIterator.php';

    $volunteerCollection=new VolunteerCollection();
    $volunteerIterator=$volunteerCollection->createIterator();
    while($volunteerIterator->hasnext()){
       $volunteer = $volunteerIterator->next();
       echo "  ";
       echo $volunteer->getName();
    }
    }


    public function addDonation($data){
          
    $donation = DonationFactory::createDonation($data);

   /* $receipt = isset($_POST['receipt']) ? 1 : 0;
    if($receipt)
       {$donation = new ReceiptDecorator($donation);}

    $ack = isset($_POST['acknowledgment']) ? 1 : 0;
    if($receipt)
       {$donation = new AckDecorator($donation);}   */ 
  
    $donation->donate();

    }

}
