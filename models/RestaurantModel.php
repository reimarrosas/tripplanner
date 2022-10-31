<?php

namespace app\models;

use Slim\Exception\HttpInternalServerErrorException;

class Restaurant extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllRestaurants(array $filters): array
    {
        $query = 'SELECT * FROM restaurant';

        if (!empty($filter)) {
            $query .= ' WHERE';
            foreach ($filters as $key => $val) {
                if ($key == 'name') {
                    $query .= " $key LIKE :$key AND";
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
            '(location_fk, name, price_range, accessibility, charging_station, street) ' .
            'VALUES ' .
            '(:location, :name, :price, :accessibilty, :charging_station, :street)';
        return $this->execute($query, $restaurant);
    }

    public function updateSingleRestaurant(array $restaurant): int
    {
        $query = 'UPDATE restaurant SET';
        foreach ($restaurant as $key => $val) {
            if ($key != 'id') {
                $query .= " $key = :$key,";
            }
        }
        $query = rtrim($query, ',') . ' WHERE restaurant_id = :restaurant_id';

        return $this->execute($query, $restaurant);
    }

    public function deleteRestaurant(int $id): int
    {
        $query = 'DELETE FROM restaurant WHERE restaurant_id = :restaurant_id';
        return $this->execute($query, ['restaurant_id' => $id]);
    }
}
