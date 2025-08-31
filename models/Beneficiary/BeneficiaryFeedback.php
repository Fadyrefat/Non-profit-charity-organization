<?php

class BeneficiaryFeedback
{
    private $id;
    private $request_id;
    private $beneficiary_id;
    private $satisfaction_rating;
    private $outcome_notes;
    private $reported_at;

    // Extra display fields
    private $request_type;
    private $beneficiary_name;

    public function __construct(
        $request_id,
        $beneficiary_id,
        $satisfaction_rating = null,
        $outcome_notes = null,
        $id = null,
        $reported_at = null,
        $request_type = null,
        $beneficiary_name = null
    ) {
        $this->id                 = $id;
        $this->request_id         = $request_id;
        $this->beneficiary_id     = $beneficiary_id;
        $this->satisfaction_rating = $satisfaction_rating;
        $this->outcome_notes      = $outcome_notes;
        $this->reported_at        = $reported_at;
        $this->request_type       = $request_type;
        $this->beneficiary_name   = $beneficiary_name;
    }

    // ===================== Getters =====================
    public function getId() { return $this->id; }
    public function getRequestId() { return $this->request_id; }
    public function getBeneficiaryId() { return $this->beneficiary_id; }
    public function getSatisfactionRating() { return $this->satisfaction_rating; }
    public function getOutcomeNotes() { return $this->outcome_notes; }
    public function getReportedAt() { return $this->reported_at; }
    public function getRequestType() { return $this->request_type; }
    public function getBeneficiaryName() { return $this->beneficiary_name; }

    // ===================== Insert Feedback =====================
    public function insert()
    {
        $conn = Database::getInstance()->getConnection();

        // Check if feedback already exists for this request
        $stmtCheck = $conn->prepare("SELECT id FROM beneficiaryFeedback WHERE request_id = ?");
        $stmtCheck->bind_param("i", $this->request_id);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows > 0) {
            throw new Exception("Feedback for this distribution already exists.");
        }
        $stmtCheck->close();

        // Insert new feedback
        $stmt = $conn->prepare("
            INSERT INTO beneficiaryFeedback (request_id, beneficiary_id, satisfaction_rating, outcome_notes)
            VALUES (?, ?, ?, ?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "iiss",
            $this->request_id,
            $this->beneficiary_id,
            $this->satisfaction_rating,
            $this->outcome_notes
        );

        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $this->id = $stmt->insert_id;
        $stmt->close();
    }

    // ===================== Get All Feedbacks =====================
    public static function getAll(): array
    {
        $conn = Database::getInstance()->getConnection();
        $sql  = "
            SELECT f.id, f.request_id, f.beneficiary_id, f.satisfaction_rating, f.outcome_notes, f.reported_at,
                   r.request_type, b.name AS beneficiary_name
            FROM beneficiaryFeedback f
            JOIN requests r ON f.request_id = r.id
            JOIN beneficiaries b ON f.beneficiary_id = b.id
            ORDER BY f.reported_at DESC
        ";

        $result = $conn->query($sql);
        $items  = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = new BeneficiaryFeedback(
                    $row['request_id'],
                    $row['beneficiary_id'],
                    $row['satisfaction_rating'],
                    $row['outcome_notes'],
                    $row['id'],
                    $row['reported_at'],
                    $row['request_type'],
                    $row['beneficiary_name']
                );
            }
        }

        return $items;
    }

    // ===================== Get Feedback by Request ID =====================
    public static function getByRequestId($request_id)
    {
        $conn = Database::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM beneficiaryFeedback WHERE request_id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $feedbacks = [];
        while ($row = $result->fetch_assoc()) {
            $feedbacks[] = new BeneficiaryFeedback(
                $row['request_id'],
                $row['beneficiary_id'],
                $row['satisfaction_rating'],
                $row['outcome_notes'],
                $row['id'],
                $row['reported_at']
            );
        }

        $stmt->close();
        return $feedbacks;
    }
}
?>
