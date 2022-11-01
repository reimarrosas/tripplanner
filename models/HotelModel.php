<?php

namespace app\models; 

class HotelModel extends BaseModel {

    private $table_name = "hotel";

    /**
     * A model class for the `hotel` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all hotel from the `hotel` table.
     * @return array A list of hotel. 
     */
    public function getAll() {
        $sql = "SELECT * FROM hotel";
        $data = $this->rows($sql);
        return $data;
    }
   
    /**
     * 2. Get the details of a given hotel
     * @param int $hotelId the id of the location.
     * @return array an array containing information about a given hotel.
     */
    public function getHotelById($hotelId) {
        $sql = "SELECT * FROM hotel WHERE hotel_id = ?";
        $data = $this->run($sql, [$hotelId])->fetch();
        
        return $data;
    }

    /**
     * Update information about one or more location (the /hotels resource collection must support this operations).
     */
    public function updateLocation($data, $where) {        
        $data = $this->update("hotel", $data, $where);
        return $data;      
    }

    public function createLocation($data) {
        $data = $this->insert("hotel", $data);
        
        return $data;
    }

    /**
     * Delete a specific hotel      
     * @param string $hotelid 
     * @return array An array containing the matches found.
     */
    public function deleteHotel($hotelid) {
        $sql = "DELETE FROM hotel WHERE hotel_id = :hotelid";
        $data = $this->run($sql, [":hotelid" => $hotelid]);

        return $data;
    }

    /**
     * Get a list of hotel names that matches or contains the provided value.       
     * @param string $hotel
     * @return array An array containing the matches found.
     */
    public function getWhereNameLike($name) {
        $sql = "SELECT * FROM hotel WHERE name LIKE :name";
        $data = $this->run($sql, [":name" => $name . "%"])->fetchAll();
        
        return $data;
    }

    /**
     * Get a list of hotel prices that matches or contains the provided value.       
     * @param string $hotel
     * @return array An array containing the matches found.
     */
    public function getWherePriceLike($price_range) {
        $sql = "SELECT * FROM hotel WHERE price_range LIKE :price_range";
        $data = $this->run($sql, [":price_range" => $price_range . "%"])->fetchAll();
        
        return $data;
    }

    /**
     * Get a list of hotel accessibility's that matches or contains the provided value.       
     * @param string $hotel
     * @return array An array containing the matches found.
     */
    public function getWhereAccessibilityLike($accessibility) {
        $sql = "SELECT * FROM hotel WHERE accessibility LIKE :accessibility";
        $data = $this->run($sql, [":accessibility" => $accessibility . "%"])->fetchAll();
        
        return $data;
    }

    /**
     * Get a list of hotel charging stations that matches or contains the provided value.       
     * @param string $hotel
     * @return array An array containing the matches found.
     */
    public function getWhereChargingStationLike($charging_station) {
        $sql = "SELECT * FROM hotel WHERE charging_station LIKE :charging_station";
        $data = $this->run($sql, [":charging_station" => $charging_station . "%"])->fetchAll();
        
        return $data;
    }
}