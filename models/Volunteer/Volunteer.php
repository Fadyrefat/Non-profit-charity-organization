<?php

require_once __DIR__ . '/VolunteerState.php';
require_once __DIR__ . '/states/HelperState.php';
require_once __DIR__ . '/states/ContributorState.php';
require_once __DIR__ . '/states/SupporterState.php';
require_once __DIR__ . '/states/LeaderState.php';
require_once __DIR__ . '/states/ChampionState.php';

class Volunteer
{
    private int $id;
    private string $name;
    private string $email;
    private string $phone;
    private int $hours;
    private VolunteerState $state;

    public function __construct(int $id = 0, string $name = '', string $email = '', string $phone = '', int $hours = 0, VolunteerState $state = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->hours = $hours;
        $this->state = $state ?? new HelperState($this);
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getHours(): int
    {
        return $this->hours;
    }

    public function getState(): VolunteerState
    {
        return $this->state;
    }

    public function getStateName(): string
    {
        return $this->state->getStateName();
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function setState(VolunteerState $state): void
    {
        $this->state = $state;
        if ($this->id > 0) {
            $this->updateStateInDatabase();
        }
    }


    public static function create(string $name, string $email, string $phone): bool
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        if (self::emailExists($email)) {
            return false;
        }

        // All new volunteers start with 0 hours and Helper state
        $hours = 0;
        $state = 'Helper';

        $stmt = $conn->prepare("INSERT INTO volunteers (name, email, phone, hours, state) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $name, $email, $phone, $hours, $state);

        return $stmt->execute();
    }

    public static function getVolunteers(): array
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        $result = $conn->query("SELECT * FROM volunteers ORDER BY hours DESC, name ASC");
        $volunteers = [];

        while ($row = $result->fetch_assoc()) {
            $volunteer = new Volunteer(
                (int) $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                (int) $row['hours']
            );

            // Set the correct state based on database value
            $volunteer->setState(self::createStateFromString($row['state'], $volunteer));
            $volunteers[] = $volunteer;
        }

        return $volunteers;
    }

    public static function getById(int $id): ?Volunteer
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("SELECT * FROM volunteers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $volunteer = new Volunteer(
                (int) $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                (int) $row['hours']
            );

            // Set the correct state based on database value
            $volunteer->setState(self::createStateFromString($row['state'], $volunteer));
            return $volunteer;
        }

        return null;
    }

    public function update_volunteer(): bool
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        // Only update name, email, phone - hours and state are managed through events
        $stmt = $conn->prepare("UPDATE volunteers SET name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("sssi", $this->name, $this->email, $this->phone, $this->id);

        return $stmt->execute();
    }

    public function updateStateInDatabase(): bool
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        $stateName = $this->getStateName();
        $stmt = $conn->prepare("UPDATE volunteers SET state = ? WHERE id = ?");
        $stmt->bind_param("si", $stateName, $this->id);

        return $stmt->execute();
    }


    public static function delete(int $id): bool
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        $stmt = $conn->prepare("DELETE FROM volunteers WHERE id = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public static function emailExists(string $email, int $excludeId = 0): bool
    {
        require_once __DIR__ . '/../../config/Database.php';
        $conn = Database::getInstance()->getConnection();

        if ($excludeId > 0) {
            $stmt = $conn->prepare("SELECT id FROM volunteers WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $conn->prepare("SELECT id FROM volunteers WHERE email = ?");
            $stmt->bind_param("s", $email);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    private static function createStateFromString(string $stateName, Volunteer $volunteer): VolunteerState
    {
        switch ($stateName) {
            case 'Helper':
                return new HelperState($volunteer);
            case 'Contributor':
                return new ContributorState($volunteer);
            case 'Supporter':
                return new SupporterState($volunteer);
            case 'Leader':
                return new LeaderState($volunteer);
            case 'Champion':
                return new ChampionState($volunteer);
            default:
                return new HelperState($volunteer); // Default fallback
        }
    }
}
