<?php
class CommunityMessages {
    private $conn;
    private $user_id;

    public function __construct($conn, $user_id){
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    // Fetch messages in a community
    public function fetchCommunityMessages($community_id){
        $stmt = $this->conn->prepare("
            SELECT cm.*, u.name as sender_name 
            FROM community_messages cm
            JOIN users u ON cm.sender_id = u.id
            WHERE cm.community_id = :comm
            ORDER BY cm.sent_at ASC
        ");
        $stmt->execute([':comm' => $community_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Send a community message
    public function sendCommunityMessage($community_id, $message){
        $stmt = $this->conn->prepare("
            INSERT INTO community_messages (sender_id, community_id, message_text)
            VALUES (:sender, :comm, :msg)
        ");
        return $stmt->execute([
            ':sender' => $this->user_id,
            ':comm' => $community_id,
            ':msg' => $message
        ]);
    }
}
?>
