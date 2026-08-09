<?php
/**
 * LandmarkService
 * Handles data access abstraction for the city_landmarks table.
 */
class LandmarkService {
    /**
     * @var PDO Connection bridge instance
     */
    private $pdo;

    /**
     * Dependency injection constructor to deliver the active database connection.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Fetches a single landmark record by its unique primary key identifier.
     *
     * @param int $id The objectid value
     * @return array|false Returns associative record array, or false if not found
     */
    public function getById($id) {
        $sql = "SELECT objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                FROM city_landmarks 
                WHERE objectid = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int)$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves all normalized landmark records from the system.
     *
     * @return array Matrix array of associative rows
     */
    public function getAll() {
        $sql = "SELECT objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                FROM city_landmarks 
                ORDER BY objectid ASC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
/**
     * Searches landmarks by matching keywords in resource_name or street_address.
     *
     * @param string $keyword The search term entered by the user
     * @return array Matching associative rows
     */
    public function search($keyword) {
        $sql = "SELECT objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                FROM city_landmarks 
                WHERE resource_name LIKE :q1 
                   OR street_address LIKE :q2 
                ORDER BY objectid ASC";
        
        $searchTerm = '%' . $keyword . '%';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':q1' => $searchTerm,
            ':q2' => $searchTerm
        ]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	/**
     * Retrieves the total count of landmark records (supports search filtering).
     *
     * @param string $keyword Optional search term
     * @return int Total number of matching rows
     */
    public function getTotalCount($keyword = '') {
        if ($keyword !== '') {
            $sql = "SELECT COUNT(*) FROM city_landmarks 
                    WHERE resource_name LIKE :q1 OR street_address LIKE :q2";
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . $keyword . '%';
            $stmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm]);
        } else {
            $sql = "SELECT COUNT(*) FROM city_landmarks";
            $stmt = $this->pdo->query($sql);
        }
        return (int)$stmt->fetchColumn();
    }

    /**
     * Fetches a paginated subset of landmarks.
     *
     * @param int $limit Number of records per page
     * @param int $offset Starting row index
     * @param string $keyword Optional search term
     * @return array Paginated rows
     */
    public function getPaginated($limit = 25, $offset = 0, $keyword = '') {
        $limit = (int)$limit;
        $offset = (int)$offset;

        if ($keyword !== '') {
            $sql = "SELECT objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                    FROM city_landmarks 
                    WHERE resource_name LIKE :q1 OR street_address LIKE :q2 
                    ORDER BY objectid ASC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . $keyword . '%';
            $stmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm]);
        } else {
            $sql = "SELECT objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                    FROM city_landmarks 
                    ORDER BY objectid ASC 
                    LIMIT $limit OFFSET $offset";
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}