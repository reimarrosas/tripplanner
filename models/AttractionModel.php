<?php

namespace app\models;

class AttractionModel extends BaseModel {

    private $table_name = "attraction";

    /**
     * A model class for the `attraction` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all attraction from the `attraction` table. It also filters based on name, maximun price, minimum price, parking
     * and charging stations.
     * @return array A list of attraction. 
     */
    public function getAllAttractions(array $filters): array {
        $query = 'SELECT * FROM attraction';

        if (!empty($filters)) {
            $query .= ' WHERE';
            foreach ($filters as $key => $val) {
                if ($key == 'name') {
                    $query .= " $key LIKE :$key AND";
                } else if ($key == 'price_min') {
                    $query .= " $key >= :$key AND";
                } else if ($key == 'price_max') {
                    $query .= " $key <= :$key AND";
                } else if ($key == 'parking') {
                    $query .= " $key = :$key AND";
                } else if ($key == 'charging_station') {
                    $query .= " $key = :$key AND";
                } else {
                    $query .= " $key = :$key AND";
                }
            }
            $query = preg_replace('/ AND$/', '', $query);
        }
        
        return $this->fetchAll($query, $filters);
    }
    
    /**
     * 2. Get the details of a given attraction.
     * @param int $attractionId the id of the attraction.
     * @return array an array containing information about a given attraction.
     */
    public function getSingleAttraction($attraction_id) {
        $sql = "SELECT * FROM attraction WHERE attraction_id = ?";
        $data = $this->run($sql, [$attraction_id])->fetch();
        
        return $data;
    }

     /**
     * Update information about one or more attraction (the /attractionss resource collection must support this operations).
     */
    public function updateAttraction($attraction_id, $name,  $charging_station, $street, $price_min, $price_max, $parking) {        
        $sql = "UPDATE attraction SET name = :name, charging_station = :charging_station, street = :street, price_min = :price_min, 
        price_max = :price_max, parking = :parking  WHERE attraction_id = :attraction_id";

        $data = $this->run($sql, [":attraction_id" => $attraction_id, ":name" => $name, ":charging_station" => $charging_station, 
        ":street" => $street, ":price_min" => $price_min, ":price_max" => $price_max, ":parking" => $parking]);
        return $data;      
    }

    /**
     * Create one or multiple attractions
     */
    public function createAttraction($data) {
        $data = $this->insert("attraction", $data);
        
        return $data;
    }

    /**
     * Delete a specific attraction     
     * @param string $attractionid 
     * @return array An array containing the matches found.
     */
    public function deleteAttraction($attraction_id) {
        $sql = "DELETE FROM attraction WHERE attraction_id = :attraction_id";
        $data = $this->run($sql, [":attraction_id" => $attraction_id]);

        return $data;
    }
}