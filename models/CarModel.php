<?php

namespace app\models;

class CarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    // Getting all cars 
    public function getAllCars(int $car_rental_id): array
    {
        $query = 'SELECT * FROM car WHERE car_rental_fk = :car_id';
        return $this->fetchAll($query, ['car_id' => $car_rental_id]);
    }

    // Getting a car by id 
    public function getCarById(int $car_id): array
    {
        $query = 'SELECT * FROM car WHERE car_id = :car_id';
        return $this->fetchSingle($query, ['car_id' => $car_id]);
    }

    // Adding a car
    public function createCar(array $car): int
    {
        $query =
            'INSERT INTO car ' .
            '(car_rental_fk, make, model, passenger, year, type) ' .
            'VALUES ' .
            '(:car_rental_fk, :make, :model, :passenger, :year, :type)';
        return $this->execute($query, $car);
    }

    // Updating a car 
    public function updateCar(array $car): int
    {
        $query = 'UPDATE car SET';
        foreach ($car as $key => $value) {
            if ($key != 'car_id') {
                $query .= " $key = :$key,";
            }
        }
        $query = rtrim($query, ',') . ' WHERE car_id = :car_id';
        return $this->execute($query, $car);
    }

    // Deleting a car
    public function deleteCar(int $car_id): int
    {
        $query = 'DELETE FROM car WHERE car_id = :car_id';
        return $this->execute($query, ['car_id' => $car_id]);
    }
}