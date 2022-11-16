<?php

namespace app\models;

class LocationModel extends BaseModel {

    private $table_name = "location";

    /**
     * A model class for the `location` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all location from the `location` table.
     * @return array A list of location. 
     */
    public function getAllLocations() {
        $sql = "SELECT * FROM location";
        $data = $this->rows($sql);
        return $data;
    }

    /**
     * Get a list of location names that matches or contains the provided value.       
     * @param string $country
     * @return array An array containing the matches found.
     */
    public function getWhereCountryLike($country) {
        $sql = "SELECT * FROM location WHERE country LIKE :country";
        $data = $this->run($sql, [":country" => $country . "%"])->fetchAll();
        
        return $data;
    }

    /**
     * Get a list of location names that matches or contains the provided value.       
     * @param string $country
     * @return array An array containing the matches found.
     */
    public function getWhereCityLike($city) {
        $sql = "SELECT * FROM location WHERE city LIKE :city";
        $data = $this->run($sql, [":city" => $city . "%"])->fetchAll();
        
        return $data;
    }

     /**
     * Get a list of location names that matches or contains the provided value.       
     * @param string $country and $city
     * @return array An array containing the matches found.
     */
    public function getWhereCityAndCountryLike($city, $country) {
        $sql = "SELECT * FROM location WHERE city LIKE :city AND country LIKE :country";
        $data = $this->run($sql, [":city" => $city . "%", ":country" => $country . "%"])->fetchAll();
        
        return $data;
    }

    /**
     * 2. Get the details of a given location
     * @param int $locationId the id of the location.
     * @return array an array containing information about a given location.
     */
    public function getSingleLocation($locationId) {
        $sql = "SELECT * FROM location WHERE location_id = ?";
        $data = $this->run($sql, [$locationId])->fetch();
        
        return $data;
    }

    /**
     * Update information about one or more location (the /locations resource collection must support this operations).
     */
    public function updateLocation($city, $country, $location_id) {        
        $sql = "UPDATE location SET city = :city, country = :country WHERE location_id = :location_id";
        $data = $this->run($sql, [":location_id" => $location_id, ":country" => $country, ":city" => $city]);
        return $data;      
    }

    /**
     * Creates one or multiple locations
     */
    public function createLocation($data) {
        $data = $this->insert("location", $data);
        
        return $data;
    }

    /**
     * Delete a specific location      
     * @param string $locationid 
     * @return array An array containing the matches found.
     */
    public function deleteLocation($locationid) {
        $sql = "DELETE FROM location WHERE location_id = :locationid";
        $data = $this->run($sql, [":locationid" => $locationid]);

        return $data;
    }
}