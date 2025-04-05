<?php
class Setting extends BaseModel {
    protected $table = 'store_settings';

    public function __construct() {
        parent::__construct();
    }

    // Get store settings
    public function getSettings() {
        $this->db->query("SELECT * FROM {$this->table} LIMIT 1");
        return $this->db->single();
    }

    // Update store settings
    public function updateSettings($data) {
        $this->db->query("UPDATE {$this->table} SET 
            store_name = :store_name,
            address = :address,
            phone = :phone,
            tax_percentage = :tax_percentage,
            updated_at = NOW()
            WHERE id = :id");

        $this->db->bind(':id', $data['id']);
        $this->db->bind(':store_name', $data['store_name']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':tax_percentage', $data['tax_percentage']);

        return $this->db->execute();
    }

    // Initialize default settings if not exists
    public function initializeSettings() {
        $this->db->query("SELECT COUNT(*) as count FROM {$this->table}");
        $result = $this->db->single();

        if ($result->count == 0) {
            $this->db->query("INSERT INTO {$this->table} (id, store_name, address, phone, tax_percentage) 
                             VALUES (:id, :store_name, :address, :phone, :tax_percentage)");

            $this->db->bind(':id', $this->generateUuid());
            $this->db->bind(':store_name', 'My Store');
            $this->db->bind(':address', 'Store Address');
            $this->db->bind(':phone', '08123456789');
            $this->db->bind(':tax_percentage', 10.00);

            return $this->db->execute();
        }
        return true;
    }

    private function generateUuid() {
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
