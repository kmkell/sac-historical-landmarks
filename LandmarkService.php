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
}