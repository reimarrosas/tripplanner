<?php

namespace app\models;

class RestaurantModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * this function gets all the data from restaurant table
     */
    public function getAllRestaurants(array $filters, $page_num, $page_size): array
    {
        $query = 'SELECT * FROM restaurant';

        if (!empty($filters)) {
            $query .= ' WHERE';
            foreach ($filters as $key => $val) {
                if ($key == 'name') {
                    $query .= " $key LIKE :$key AND";
                } else if ($key == 'price_min') {
                    $query .= " $key >= :$key AND";
                } else if ($key == 'price_max') {
                    $query .= " $key <= :$key AND";
                } else {
                    $query .= " $key = :$key AND";
                }
            }
            $query = preg_replace('/ AND$/', '', $query);
        }

        $calc_page = ($page_num - 1) * $page_size;
        $query .= " ORDER BY restaurant_id ASC LIMIT $page_size OFFSET $calc_page";

        return $this->fetchAll($query, $filters);
    }

    /**
     * Gets data of a single restaurant
     */
    public function getSingleRestaurant(int $id): array
    {
        $query = 'SELECT * FROM restaurant WHERE restaurant_id = :restaurant_id';
        return $this->fetchSingle($query, ['restaurant_id' => $id]);
    }

    // This function executes the sql statements to fetch a specific restaurant and its location
    public function getRestaurantLocation(int $id): array
    {
        $sql = "SELECT restaurant.*, location.* FROM restaurant JOIN location ON location.location_id=restaurant.location_fk WHERE restaurant_id = ? ";
        $data = $this->run($sql, [$id])->fetch();
        return $data;
    }

    /**
     * this function creates a single restaurant
     */
    public function createSingleRestaurant(array $restaurant): int
    {
        $query =
            'INSERT INTO restaurant ' .
            '(location_fk, name, price_min, accessibility, charging_station, street, price_max) ' .
            'VALUES ' .
            '(:location_fk, :name, :price_min, :accessibility, :charging_station, :street, :price_max)';
        return $this->execute($query, $restaurant);
    }

    /**
     * This function creates multiple restaurants
     */
    public function createMultipleRestaurant(array $restaurants): int
    {
        $count = 0;

        $query =
            'INSERT INTO restaurant ' .
            '(location_fk, name, price_min, accessibility, charging_station, street, price_max) ' .
            'VALUES ' .
            '(:location_fk, :name, :price_min, :accessibility, :charging_station, :street, :price_max)';

        $db = $this->getPdo();
        $stmt = $db->prepare($query);
        $stmt->bindParam('location_fk', $location_fk);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('price_min', $price_min);
        $stmt->bindParam('accessibility', $accessibility);
        $stmt->bindParam('charging_station', $charging_station);
        $stmt->bindParam('street', $street);
        $stmt->bindParam('price_max', $price_max);

        try {
            $db->beginTransaction();

            foreach ($restaurants as $restaurant) {
                extract($restaurant);
                $stmt->execute();
                $count++;
            }

            $db->commit();
        } catch (\Throwable $th) {
            $db->rollBack();
            throw $th;
        }


        return $count;
    }

    /**
     * This function updates a single restaurant
     */
    public function updateSingleRestaurant(int $restaurant_id, array $restaurant): int
    {
        $restaurant['restaurant_id'] = $restaurant_id;
        $query =
            'UPDATE restaurant ' .
            'SET location_fk = :location_fk, ' .
            'name = :name, ' .
            'price_min = :price_min, ' .
            'accessibility = :accessibility, ' .
            'charging_station = :charging_station, ' .
            'street = :street, ' .
            'price_max = :price_max ' .
            'WHERE restaurant_id = :restaurant_id';
        return $this->execute($query, $restaurant);
    }

    /**
     * This function updates multiple restaurants
     */
    public function updateMultipleRestaurant(array $restaurants): int
    {
        $count = 0;

        $query =
            'UPDATE restaurant ' .
            'SET location_fk = :location_fk, ' .
            'name = :name, ' .
            'price_min = :price_min, ' .
            'accessibility = :accessibility, ' .
            'charging_station = :charging_station, ' .
            'street = :street, ' .
            'price_max = :price_max ' .
            'WHERE restaurant_id = :restaurant_id';

        $db = $this->getPdo();
        $stmt = $db->prepare($query);
        $stmt->bindParam('location_fk', $location_fk);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('price_min', $price_min);
        $stmt->bindParam('accessibility', $accessibility);
        $stmt->bindParam('charging_station', $charging_station);
        $stmt->bindParam('street', $street);
        $stmt->bindParam('price_max', $price_max);
        $stmt->bindParam('restaurant_id', $restaurant_id);

        try {
            $db->beginTransaction();

            foreach ($restaurants as $restaurant) {
                extract($restaurant);
                $stmt->execute();
                $count++;
            }

            $db->commit();
        } catch (\Throwable $th) {
            $db->rollBack();
            throw $th;
        }

        return $count;
    }

    /**
     * This function deletes a single restaurant
     */
    public function deleteRestaurant(int $id): int
    {
        $query = 'DELETE FROM restaurant WHERE restaurant_id = :restaurant_id';
        return $this->execute($query, ['restaurant_id' => $id]);
    }

    // This function executes the SQL statements to fetch recommended restaurants based on the specified theme tag 
    public function getRestaurantRecommendations(array $filters): array
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
            SELECT r.*
            FROM
                restaurant AS r
                JOIN tagged_restaurant AS tr ON r.restaurant_id = tr.restaurant_id
                JOIN tags AS t ON tr.tag_id = t.tag_id
            WHERE t.tag_name in ($placeholders)
            GROUP BY r.restaurant_id
            HAVING count(r.restaurant_id) = $count;
        EOD;

        return $this->rows($query, $filters);
    }
}
