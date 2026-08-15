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
     * Fetches a single landmark record by its unique primary key identifier, 
     * along with its associated research notes.
     *
     * @param int $id The landmark id value
     * @return array|false Returns associative record array, or false if not found
     */
    public function getById($id) {
        $sql = "SELECT 
                    l.id,
                    l.objectid,
                    l.apn,
                    l.resource_name,
                    l.street_address,
                    l.ordinance,
                    l.shape__area,
                    l.shape__length,
                    r.year_built,
                    r.architect,
                    r.notes
                FROM city_landmarks l
                LEFT JOIN landmark_research r ON l.id = r.landmark_id
                WHERE l.id = :id";
                
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
        $sql = "SELECT id, objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
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
        $sql = "SELECT id, objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
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
            $sql = "SELECT id, objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                    FROM city_landmarks 
                    WHERE resource_name LIKE :q1 OR street_address LIKE :q2 
                    ORDER BY objectid ASC 
                    LIMIT $limit OFFSET $offset";
            
            $stmt = $this->pdo->prepare($sql);
            $searchTerm = '%' . $keyword . '%';
            $stmt->execute([':q1' => $searchTerm, ':q2' => $searchTerm]);
        } else {
            $sql = "SELECT id, objectid, apn, resource_name, street_address, ordinance, shape__area, shape__length 
                    FROM city_landmarks 
                    ORDER BY objectid ASC 
                    LIMIT $limit OFFSET $offset";
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	/**
     * Fetches all architectural styles associated with a specific landmark primary key (id).
     *
     * @param int $landmarkId
     * @return array List of styles with type and notes
     */
    public function getStylesForLandmark($landmarkId) {
        $sql = "SELECT s.style_id, s.style_name, s.era, s.approx_start_year, s.approx_end_year, ls.is_primary
                FROM landmark_styles ls
                JOIN architectural_styles s ON ls.style_id = s.style_id
                WHERE ls.landmark_id = :id
                ORDER BY ls.is_primary DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int)$landmarkId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function saveResearch($landmarkId, $yearBuilt, $architect, $notes) {
        $sql = "INSERT INTO landmark_research (landmark_id, year_built, architect, notes) 
                VALUES (:landmark_id, :year_built, :architect, :notes)
                ON DUPLICATE KEY UPDATE 
                year_built = VALUES(year_built), 
                architect = VALUES(architect), 
                notes = VALUES(notes)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':landmark_id' => $landmarkId,
            ':year_built' => $yearBuilt,
            ':architect' => $architect,
            ':notes' => $notes
        ]);
    }
    /**
     * Fetches all available architectural styles from the master list.
     */
    public function getAllStyles() {
        $sql = "SELECT style_id, style_name, era FROM architectural_styles ORDER BY style_name ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Updates the architectural styles and their primary designations assigned to a landmark.
     */
   public function updateLandmarkStyles($landmarkId, $stylesData) {
        // 1. Delete existing style links for this landmark
        $delSql = "DELETE FROM landmark_styles WHERE landmark_id = :landmark_id";
        $delStmt = $this->pdo->prepare($delSql);
        $delStmt->execute([':landmark_id' => $landmarkId]);

        // 2. Insert newly selected styles with their respective is_primary status
        if (!empty($stylesData) && is_array($stylesData)) {
            $insSql = "INSERT INTO landmark_styles (landmark_id, style_id, is_primary) VALUES (:landmark_id, :style_id, :is_primary)";
            $insStmt = $this->pdo->prepare($insSql);
            
            foreach ($stylesData as $styleId => $data) {
                // Check if 'selected' was submitted and is true
                if (isset($data['selected']) && $data['selected'] == '1') {
                    $insStmt->execute([
                        ':landmark_id' => $landmarkId,
                        ':style_id' => (int)$styleId,
                        ':is_primary' => (int)($data['is_primary'] ?? 0)
                    ]);
                }
            }
        }
    }
}