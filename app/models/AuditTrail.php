<?php
class AuditTrail extends BaseModel {
    protected $table = 'audit_trail';
    public function __construct() {
        parent::__construct();
    }

    public function log($data) {
        // If user is not logged in, only log non-sensitive actions
        if (is_null($data['user_id']) && !in_array($data['action'], ['login', 'login_failed'])) {
            return true;
        }

        // For login attempts, we don't need user_id
        if (in_array($data['action'], ['login', 'login_failed'])) {
            $sql = 'INSERT INTO audit_trail (id, action, details, ip_address, user_agent) 
                    VALUES (:id, :action, :details, :ip_address, :user_agent)';
        } else {
            $sql = 'INSERT INTO audit_trail (id, user_id, action, details, ip_address, user_agent) 
                    VALUES (:id, :user_id, :action, :details, :ip_address, :user_agent)';
        }
        
        $this->db->query($sql);
        
        $this->db->bind(':id', parent::generateGuid());
        if (!in_array($data['action'], ['login', 'login_failed'])) {
            $this->db->bind(':user_id', $data['user_id']);
        }
        $this->db->bind(':action', $data['action']);
        $this->db->bind(':details', $data['details']);
        $this->db->bind(':ip_address', $data['ip_address']);
        $this->db->bind(':user_agent', $data['user_agent']);

        return $this->db->execute();
    }

    public function getAuditTrails($limit = 100) {
        $this->db->query('SELECT a.*, u.username, u.name 
                         FROM audit_trail a 
                         LEFT JOIN users u ON a.user_id = u.id 
                         ORDER BY a.created_at DESC 
                         LIMIT :limit');
        
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

}
