<?php
class Messages {
    private $conn;
    private $user_id;

    public function __construct($conn, $user_id){
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    // Fetch messages for a conversation (peer or lecturer)
    public function fetchMessages($receiver_id, $type){
        $stmt = $this->conn->prepare("
            SELECT * FROM messages 
            WHERE (sender_id = :user OR receiver_id = :user) 
              AND (sender_id = :recv OR receiver_id = :recv) 
              AND message_type = :type
            ORDER BY sent_at ASC
        ");
        $stmt->execute([
            ':user' => $this->user_id,
            ':recv' => $receiver_id,
            ':type' => $type
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Send a message
    public function sendMessage($receiver_id, $message, $type){
        $stmt = $this->conn->prepare("
            INSERT INTO messages (sender_id, receiver_id, message_text, message_type)
            VALUES (:sender, :receiver, :msg, :type)
        ");
        return $stmt->execute([
            ':sender' => $this->user_id,
            ':receiver' => $receiver_id,
            ':msg' => $message,
            ':type' => $type
        ]);
    }
}
?>
