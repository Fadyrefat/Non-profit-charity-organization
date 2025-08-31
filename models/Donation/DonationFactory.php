<?php 

require_once 'DonationTypes/FoodDonation.php';
require_once 'DonationTypes/ClothesDonation.php';
require_once 'DonationTypes/MoneyDonation.php';
require_once 'DonationTypes/CompositeDonation.php';
require_once 'PaymentMethods/DonationStrategy.php';
require_once 'PaymentMethods/CashStrategy.php';
require_once 'PaymentMethods/VisaStrategy.php';
require_once 'PaymentMethods/CheckStrategy.php';
require_once 'AdapterPattern/PaypalAdapter.php';
require_once 'AdapterPattern/PaypalService.php';

class DonationFactory {
    public static function createDonation(array $data): Donation {
        $donations = [];
        $donor_id=$data['donor_id'];

        if (!empty($data['food'])) {
            $donations[] = new FoodDonation($donor_id,$data['food_description'], (int)$data['food_amount']);
        }
        if (!empty($data['clothes'])) {
            $donations[] = new ClothesDonation($donor_id,$data['clothes_description'], (int)$data['clothes_amount']);
        }
        if (!empty($data['money'])) {

            $paymentmethod=$data['payment_method'];

            if ($paymentmethod=="visa")
                $paymentstrategy=new VisaStrategy();
            elseif($paymentmethod=="check")
                $paymentstrategy=new CheckStrategy();
            elseif($paymentmethod=="cash")
                $paymentstrategy=new CashStrategy();
            elseif($paymentmethod=="paypal"){
                $paymentservice= new PaypalService();
                $paymentstrategy=new PaypalAdapter($paymentservice);
            }

            $donations[] = new MoneyDonation($donor_id,$paymentstrategy,(float)$data['money_amount']);
        }

        if (count($donations) === 1) {
            return $donations[0]; 
        } else {
            $group = new CompositeDonation($donor_id);
            foreach ($donations as $donation) {
                $group->add($donation);
            }
            return $group; 
        }
    }
}
?>
