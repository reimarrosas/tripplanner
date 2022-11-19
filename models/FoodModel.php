<?php

namespace app\models;

class FoodModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllFood(int $restaurant_id): array
    {
        $query = 'SELECT * FROM FOOD WHERE restaurant_fk = :restaurant_id';
        return $this->fetchAll($query, ['restaurant_id' => $restaurant_id]);
    }
    public function getSingleFood(int $restaurant_fk, int $food_id): array
    {
        $query = 'SELECT * FROM FOOD WHERE restaurant_fk = :restaurant_fk AND food_id = :food_id';
        return $this->fetchSingle($query, ['food_id' => $food_id, 'restaurant_fk' => $restaurant_fk]);
    }

    public function createSingleFood(array $food): int
    {
        $query =
            'INSERT INTO food ' .
            '(restaurant_fk, type, name, price) ' .
            'VALUES ' .
            '(:restaurant_fk, :type, :name, :price)';
        return $this->execute($query, $food);
    }

    public function createMultipleFood(array $foods): int
    {
        $count = 0;

        $query =
            'INSERT INTO food ' .
            '(restaurant_fk, type, name, price) ' .
            'VALUES ' .
            '(:restaurant_fk, :type, :name, :price)';

        $db = $this->getPdo();
        $stmt = $db->prepare($query);
        $stmt->bindParam('restaurant_fk', $restaurant_fk);
        $stmt->bindParam('type', $type);
        $stmt->bindParam('name', $name);
        $stmt->bindParam('price', $price);

        try {
            $db->beginTransaction();

            foreach ($foods as $food) {
                extract($food);
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

    public function updateSingleFood(array $food): int
    {
        $query =
            'UPDATE food ' .
            'SET restaurant_fk = :restaurant_fk, ' .
            'type = :type, ' .
            'name = :name, ' .
            'price = :price ' .
            'WHERE food_id = :food_id';
        return $this->execute($query, $food);
    }

    public function deleteFood(int $restaurant_fk, int $food_id): int
    {
        $query = 'DELETE FROM food WHERE restaurant_fk = :restaurant_fk AND food_id = :food_id';
        return $this->execute($query, ['food_id' => $food_id, 'restaurant_fk' => $restaurant_fk]);
    }
}
