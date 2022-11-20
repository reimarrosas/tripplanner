<?php

namespace app\models;

class CarModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    // Getting all cars 
    public function getAllCars(int $car_rental_fk): array
    {
        $query = 'SELECT * FROM car WHERE car_rental_fk = :car_rental_fk';
        return $this->fetchAll($query, ['car_rental_fk' => $car_rental_fk]);
    }

    // Getting a car by id 
    public function getCarById(int $car_rental_fk, int $car_id): array
    {
        $query = 'SELECT * FROM car WHERE car_rental_fk = :car_rental_fk AND car_id = :car_id';
        return $this->fetchSingle($query, ['car_rental_fk' => $car_rental_fk, 'car_id' => $car_id]);
    }

    // Creating a car
    public function createCar($data)
    {
        $data = $this->insert("car", $data);
        return $data;
    }

   // Updating car
   public function updateCar(int $car_id, array $car): int
   {
       $car['car_id'] = $car_id;
       $query =
           'UPDATE car ' .
           'SET car_rental_fk = :car_rental_fk, ' .
           'make = :make, ' .
           'model = :model, ' .
           'passenger = :passenger, ' .
           'year = :year , ' .
           'type = :type , ' .
           'price = :price ' .
           'WHERE car_id = :car_id';
       return $this->execute($query, $car);
   }

    // Deleting a car
    public function deleteCar(int $car_id, int $car_rental_fk): int
    {
        $query = 'DELETE FROM car WHERE car_id = :car_id AND car_rental_fk = :car_rental_fk';
        return $this->execute($query, ['car_id' => $car_id, 'car_rental_fk' => $car_rental_fk]);
    }
}