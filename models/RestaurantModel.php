<?php

namespace app\models;

class RestaurantModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllRestaurants(array $filters): array
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

        return $this->fetchAll($query, $filters);
    }

    public function getSingleRestaurant(int $id): array
    {
        $query = 'SELECT * FROM restaurant WHERE restaurant_id = :restaurant_id';
        return $this->fetchSingle($query, ['restaurant_id' => $id]);
    }

    public function createSingleRestaurant(array $restaurant): int
    {
        $query =
            'INSERT INTO restaurant ' .
            '(location_fk, name, price_min, accessibility, charging_station, street, price_max) ' .
            'VALUES ' .
            '(:location_fk, :name, :price_min, :accessibility, :charging_station, :street, :price_max)';
        return $this->execute($query, $restaurant);
    }

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

    public function deleteRestaurant(int $id): int
    {
        $query = 'DELETE FROM restaurant WHERE restaurant_id = :restaurant_id';
        return $this->execute($query, ['restaurant_id' => $id]);
    }
}
