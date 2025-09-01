<?php
require_once __DIR__ . '/../config/Database.php';

// Models
require_once __DIR__ . '/../models/Event_Management/Event.php';
require_once __DIR__ . '/../models/Event_Management/Attendee.php';
require_once __DIR__ . '/../models/Event_Management/EventLog.php';
require_once __DIR__ . '/../models/Event_Management/EventReceiver.php';
require_once __DIR__ . '/../models/Event_Management/EventInvoker.php';

// Commands
require_once __DIR__ . '/../models/Event_Management/Command/EventCommandInterface.php';
require_once __DIR__ . '/../models/Event_Management/Command/RegisterCommand.php';
require_once __DIR__ . '/../models/Event_Management/Command/BookTicketCommand.php';
require_once __DIR__ . '/../models/Event_Management/Command/UpdateAttendanceCommand.php';
require_once __DIR__ . '/../models/Event_Management/Command/SendReminderCommand.php';

// Observers interface (Donor, Volunteer, Beneficiary implement this)
require_once __DIR__ . '/../models/Event_Management/Observer/ObserverInterface.php';

class EventController {

    private $eventModel;
    private $attendeeModel;
    private $receiver;
    private $invoker;

    public function __construct() {
        $event = Event::getInstance(); 
        $this->eventModel    = $event;
        $this->attendeeModel = new Attendee();
        $this->receiver      = new EventReceiver($event);
        $this->invoker       = new EventInvoker();
    }

    // Show all events
    public function index() {
        $events = $this->eventModel->getAll();
        require __DIR__ . '/../views/Event_Management/index.php';
    }

    // Show form to create event
    public function create() {
        require __DIR__ . '/../views/Event_Management/create.php';
    }

    // Store new event
    public function store($data) {
        $id = $this->eventModel->create($data);
        header("Location: ?action=showEvent&id=$id");
    }

    // Show one event + attendees
    public function show($id) {
        $event     = $this->eventModel->find($id);
        $attendees = $this->attendeeModel->getByEvent($id);
        require __DIR__ . '/../views/Event_Management/show.php';
    }

// EventController.php - Fixed parameter passing
public function register($data) {
    $cmd = new RegisterCommand(
        $this->receiver,
        $data['event_id'],
        $data
    );
    $this->invoker->setCommand($cmd);
    $this->invoker->execute();

    header("Location: ?action=showEvent&id=" . $data['event_id']);
}

public function bookTicket($data) {
    $cmd = new BookTicketCommand(
        $this->receiver,
        $data['event_id'],
        $data
    );
    $this->invoker->setCommand($cmd);
    $this->invoker->execute();

    header("Location: ?action=showEvent&id=" . $data['event_id']);
}

public function updateAttendance($data) {
    $cmd = new UpdateAttendanceCommand(
        $this->receiver,
        $data['event_id'],
        $data['attendee_id'],
        $data['checked_in']
    );
    $this->invoker->setCommand($cmd);
    $this->invoker->execute();

    header("Location: ?action=showEvent&id=" . $data['event_id']);
}

public function sendReminder($data) {
    $cmd = new SendReminderCommand(
        $this->receiver,
        $data['event_id'],
        $data['message'] ?? 'Reminder about upcoming event'
    );
    $this->invoker->setCommand($cmd);
    $this->invoker->execute();

    header("Location: ?action=showEvent&id=" . $data['event_id']);
}
}
