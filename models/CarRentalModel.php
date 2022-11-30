<?php

namespace app\models;

use Slim\Exception\HttpInternalServerErrorException;

class CarRentalModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    // Getting all car rentals
     // This function executes the sql statements to fetch all the car rentals 
    public function getAllCarRentals(): array
    {
        $query = 'SELECT * FROM car_rental';
        return $this->fetchAll($query);
    }

    // Getting a car rental by id
    // This function executes the sql statements to fetch a specific car rental 
    public function getCarRentalById(int $car_rental_id): array
    {
        $query = 'SELECT * FROM car_rental WHERE car_rental_id = :car_rental_id';
        return $this->fetchSingle($query, ['car_rental_id' => $car_rental_id]);
    }

    // Creating a car rental
    // This function executes the sql statements to create a car rental based on specific car rental attributes specified by the user
    public function createCarRental($data)
    {
        $data = $this->insert("car_rental", $data);
        return $data;
    }

    // Updating car rental 
    // This function executes the sql statements to update an existing car rental based on car rental attributes specified by the user
    public function updateCarRental(int $car_rental_id, array $car_rental): int
    {
        $car_rental['car_rental_id'] = $car_rental_id;
        $query =
            'UPDATE car_rental ' .
            'SET location_fk = :location_fk, ' .
            'price_min = :price_min, ' .
            'rental_duration = :rental_duration, ' .
            'street = :street, ' .
            'price_max = :price_max ' .
            'WHERE car_rental_id = :car_rental_id';
        return $this->execute($query, $car_rental);
    }

    // Deleting car rental
    // This function executes the sql statements to delete an existing car rental 
    public function deleteCarRental(int $car_rental_id): int
    {
        $query = 'DELETE FROM car_rental WHERE car_rental_id = :car_rental_id';
        return $this->execute($query, ['car_rental_id' => $car_rental_id]);
    }
}
