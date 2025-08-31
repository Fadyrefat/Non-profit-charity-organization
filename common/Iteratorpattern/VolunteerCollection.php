<?php 
require_once 'Collection.php';
require_once __DIR__ . '/../../models/volunteer/volunteer.php';
require_once __DIR__ . '/../../models/volunteer/states/HelperState.php'; 
require_once __DIR__ . '/../../models/volunteer/states/LeaderState.php'; 
require_once __DIR__ . '/../../models/volunteer/states/ContributorState.php'; 
require_once __DIR__ . '/../../models/volunteer/states/SupporterState.php'; 
require_once __DIR__ . '/../../models/volunteer/states/ChampionState.php'; 
class VolunteerCollection implements Collection{

private SplDoublyLinkedList $volunteers;

public function __construct(){

$this->volunteers = new SplDoublyLinkedList();
$conn = Database::getInstance()->getConnection();
$sql="SELECT * From volunteers";
$result=mysqli_query($conn,$sql);
    while ($row = mysqli_fetch_assoc($result)) {

        $state=$row['state'];
        if($state=="Helper")
            $state=new HelperState();
        elseif($state=="Leader")
            $state=new LeaderState();
        elseif($state=="Supporter")
            $state=new SupporterState();
        elseif($state=="Contributor")
            $state=new ContributorState();
        elseif($state=="Champion")
            $state = new ChampionState();

        
        $newVolunteer=new Volunteer($row['id'],$row['name'],$row['email'],$row['phone'],$row['hours'],$state);
        $state->setVolunteer($newVolunteer);
        $this->volunteers->push($newVolunteer);

    }
}

public function createIterator():IteratorInterface{
    return new VolunteerIterator($this->volunteers);
}



}
?>