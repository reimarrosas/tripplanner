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
     * Retrieve all hotel from the `hotel` table. It also filters based on name, 
     * price minimum, price maximun, accessibility and charging station
     * @return array A list of hotel. 
     */
    public function getAllHotels(array $filters): array {
        $query = 'SELECT * FROM hotel';

        if (!empty($filters)) {
            $query .= ' WHERE';
            foreach ($filters as $key => $val) {
                if ($key == 'name') {
                    $query .= " $key LIKE :$key AND";
                } else if ($key == 'price_min') {
                    $query .= " $key >= :$key AND";
                } else if ($key == 'price_max') {
                    $query .= " $key <= :$key AND";
                } else if ($key == 'accessibility') {
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
     * 2. Get the details of a given hotel
     * @param int $hotelId the id of the location.
     * @return array an array containing information about a given hotel.
     */
    public function getSingleHotel($hotelId) {
        $sql = "SELECT * FROM hotel WHERE hotel_id = ?";
        $data = $this->run($sql, [$hotelId])->fetch();
        
        return $data;
    }

     /**
     * Update information about one or more hotels (the /hotels resource collection must support this operations).
     */
    public function updateHotel($hotel_id, $name, $charging_station, $street, $price_min, $price_max, $accessibility, $location_fk) {        
        $sql = "UPDATE hotel SET name = :name, charging_station = :charging_station, street = :street, price_min = :price_min, price_max = :price_max, accessibility = :accessibility,location_fk = :location_fk   WHERE hotel_id = :hotel_id";
        $data = $this->run($sql, [":hotel_id" => $hotel_id, ":name" => $name, ":charging_station" => $charging_station, ":street" => $street, ":price_min" => $price_min, ":price_max" => $price_max, ":accessibility" => $accessibility, ":location_fk" => $location_fk]);
        return $data;      
    }

    /**
     * Creates one or multiple hotels
     */
    public function createHotel($data) {
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

    public function getHotelRecommendations(array $filters): array
    {
        $placeholders = '';
        $count = count($filters);
        for ($i = 0; $i < $count; ++$i) {
            if ($i == $count - 1) {
                $placeholders .= '?';
            } else {
                $placeholders .= '?, ';
            }
        }
        $query = <<< EOD
            SELECT h.*
            FROM
                hotel AS h
                JOIN tagged_hotel AS th ON h.hotel_id = th.hotel_id
                JOIN tags AS t ON th.tag_id = t.tag_id
            WHERE t.tag_name in ($placeholders)
            GROUP BY h.hotel_id
            HAVING count(h.hotel_id) = $count;
        EOD;

        return $this->rows($query, $filters);
    }
}