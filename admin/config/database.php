<?php
class Database {
    private $db_file = __DIR__ . '/../data/realestate.db';
    private $pdo;
    
    public function __construct() {
        $this->connect();
        $this->createTables();
    }
    
    private function connect() {
        try {
            // Create data directory if it doesn't exist
            $data_dir = dirname($this->db_file);
            if (!is_dir($data_dir)) {
                mkdir($data_dir, 0755, true);
            }
            
            $this->pdo = new PDO('sqlite:' . $this->db_file);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    
    private function createTables() {
        try {
            // Create admin users table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS admin_users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create properties table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS properties (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    category VARCHAR(100) NOT NULL,
                    image VARCHAR(255),
                    cost VARCHAR(100) NOT NULL,
                    location VARCHAR(255) NOT NULL,
                    status VARCHAR(50) NOT NULL,
                    description TEXT,
                    bedrooms INTEGER,
                    bathrooms INTEGER,
                    area VARCHAR(100),
                    features TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create contact_info table
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS contact_info (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    type VARCHAR(50) NOT NULL,
                    label VARCHAR(100) NOT NULL,
                    value VARCHAR(255) NOT NULL,
                    is_active BOOLEAN DEFAULT 1,
                    sort_order INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Insert default admin user if not exists
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
            $stmt->execute(['admin']);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $this->pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
                $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT)]);
            }
            
            // Insert default contact info if not exists
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contact_info");
            $stmt->execute();
            if ($stmt->fetchColumn() == 0) {
                $contact_data = [
                    ['phone', 'Primary Phone', '7091651137'],
                    ['phone', 'Secondary Phone', '6290379907'],
                    ['email', 'Primary Email', 'property@kartikeyarealty.com'],
                    ['address', 'Faridabad Office', '716 Piyush Heights, Sector 89 Faridabad'],
                    ['address', 'Narmadapuram Office', 'no. 15 near new bus stand seoni malwa Narmadapuram . M.P'],
                    ['social', 'Facebook', '#'],
                    ['social', 'Instagram', '#'],
                    ['social', 'Twitter', '#'],
                    ['social', 'LinkedIn', '#']
                ];
                
                $stmt = $this->pdo->prepare("INSERT INTO contact_info (type, label, value, sort_order) VALUES (?, ?, ?, ?)");
                foreach ($contact_data as $index => $data) {
                    $stmt->execute([$data[0], $data[1], $data[2], $index]);
                }
            }
            
        } catch (PDOException $e) {
            die('Table creation failed: ' . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }
    
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}

// Initialize database
$database = new Database();
$pdo = $database->getConnection();
?> 