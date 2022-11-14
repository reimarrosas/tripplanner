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
     * Retrieve all attraction from the `attraction` table.
     * @return array A list of attraction. 
     */
    public function getAllAttractions() {
        $sql = "SELECT * FROM attraction";
        $data = $this->rows($sql);
        return $data;
    }
    
    /**
     * 2. Get the details of a given attraction
     * @param int $attractionId the id of the attraction.
     * @return array an array containing information about a given attraction.
     */
    public function getSingleAttraction($attraction_id) {
        $sql = "SELECT * FROM attraction WHERE attraction_id = ?";
        $data = $this->run($sql, [$attraction_id])->fetch();
        
        return $data;
    }

     /**
     * Update information about one or more location (the /locations resource collection must support this operations).
     */
    public function updateAttraction($attraction_id, $name,  $charging_station, $street, $price_min, $price_max, $parking) {        
        $sql = "UPDATE attraction SET name = :name, charging_station = :charging_station, street = :street, price_min = :price_min, price_max = :price_max, parking = :parking  WHERE attraction_id = :attraction_id";
        $data = $this->run($sql, [":attraction_id" => $attraction_id, ":name" => $name, ":charging_station" => $charging_station, ":street" => $street, ":price_min" => $price_min, ":price_max" => $price_max, ":parking" => $parking]);
        return $data;      
    }

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

     /**
     * Get a list of attraction names that matches or contains the provided value.       
     * @param string $name
     * @return array An array containing the matches found.
     */
    public function getWhereNameLike($name) {
        $sql = "SELECT * FROM attraction WHERE name LIKE :name";
        $data = $this->run($sql, [":name" => $name . "%"])->fetchAll();
        
        return $data;
    }

     /**
     * Get a list of attraction price ranges that matches or contains the provided value.       
     * @param string $price_range
     * @return array An array containing the matches found.
     */
    public function getWherePriceRangeLike($price_range) {
        $sql = "SELECT * FROM attraction WHERE price_range LIKE :price_range";
        $data = $this->run($sql, [":price_range" => $price_range . "%"])->fetchAll();
        
        return $data;
    }

     /**
     * Get a list of attraction parkings that matches or contains the provided value.       
     * @param string $parking
     * @return array An array containing the matches found.
     */
    public function getWhereParkingLike($parking) {
        $sql = "SELECT * FROM attraction WHERE parking LIKE :parking";
        $data = $this->run($sql, [":parking" => $parking . "%"])->fetchAll();
        
        return $data;
    }

     /**
     * Get a list of attraction charging stations that matches or contains the provided value.       
     * @param string $charging_station
     * @return array An array containing the matches found.
     */
    public function getWhereChargingStationLike($charging_station) {
        $sql = "SELECT * FROM attraction WHERE charging_station LIKE :charging_station";
        $data = $this->run($sql, [":charging_station" => $charging_station . "%"])->fetchAll();
        
        return $data;
    }
}