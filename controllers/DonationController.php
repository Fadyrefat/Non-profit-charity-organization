<?php
require_once 'models/donation/DonationFactory.php';
require_once 'models/donation/DonationDecorators/ReceiptDecorator.php';
require_once 'models/donation/DonationDecorators/AckDecorator.php';

class DonationController
{

    public function Index()
    {
        require_once 'views/Donation/index.html';
    }
    public function DonerForm()
    {
        require_once 'views/Donation/addDoner.html';
    }
    public function addDoner($data)
    {
        require_once 'models/Donation/Donor.php';
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'] ?? null;

        if (Donor::create($name, $email, $phone)) {
            header('Location: index.php?action=showDonors');
        } else {
            echo "Error adding donor!";
        }
    }
    public function DonationForm()
    {
        require_once 'models/Donation/Donor.php';
        $donors = Donor::getDonors();
        require_once 'views/Donation/addDonation.html';
    }

    public function addDonation($data)
    {

        $donation = DonationFactory::createDonation($data);

        /* $receipt = isset($_POST['receipt']) ? 1 : 0;
         if($receipt)
            {$donation = new ReceiptDecorator($donation);}

         $ack = isset($_POST['acknowledgment']) ? 1 : 0;
         if($receipt)
            {$donation = new AckDecorator($donation);}   */

        $donation->donate();

    }

    public function showDonors()
    {
        require_once 'models/Donation/Donor.php';
        $donors = Donor::getDonors();
        require_once 'views/Donation/showDonors.html';
    }

    public function editDonor($id)
    {
        require_once 'models/Donation/Donor.php';
        $donor = Donor::getDonorByID($id);
        if ($donor) {
            require_once 'views/Donation/editDonor.html';
        } else {
            echo "Donor not found!";
        }
    }

    public function updateDonor($data)
    {
        require_once 'models/Donation/Donor.php';
        $id = $data['id'];
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];

        if (Donor::update($id, $name, $email, $phone)) {
            header('Location: index.php?action=showDonors');
        } else {
            echo "Error updating donor!";
        }
    }

    public function deleteDonor($id)
    {
        require_once 'models/Donation/Donor.php';
        if (Donor::delete($id)) {
            header('Location: index.php?action=showDonors');
        } else {
            echo "Error deleting donor!";
        }
    }

}
