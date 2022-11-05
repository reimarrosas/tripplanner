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
    public function getAllCarRentals(): array
    {
        $query = 'SELECT * FROM car_rental';
        return $this->fetchAll($query, $filters);
    }

    // Getting a car rental by id
    public function getCarRentalById(int $car_rental_id): array
    {
        $query = 'SELECT * FROM car_rental WHERE car_rental_id = :car_rental_id';
        return $this->fetchSingle($query, ['car_rental_id' => $car_rental_id]);
    }

    // Creating a car rental
    public function createCarRental(array $car_rental): int
    {
        $query =
            'INSERT INTO car_rental ' .
            '(location_fk, price_range, rental_duration, street) ' .
            'VALUES ' .
            '(:location, :price, :duration, :street)';
        return $this->execute($query, $car_rental);
    }

    // Updating car rental 
    public function updateCarRental(array $car_rental): int
    {
        $query = 'UPDATE car_rental SET';
        foreach ($car_rental as $key => $val) {
            if ($key != 'car_rental_id') {
                $query .= " $key = :$key,";
            }
        }
        $query = rtrim($query, ',') . ' WHERE car_rental_id = :car_rental_id';

        return $this->execute($query, $car_rental);
    }

    // Deleting car rental
    public function deleteCarRental(int $car_rental_id): int
    {
        $query = 'DELETE FROM car_rental WHERE car_rental_id = :car_rental_id';
        return $this->execute($query, ['car_rental_id' => $car_rental_id]);
    }
}
