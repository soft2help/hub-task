<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SearchRequest
{
    /**
     * @Assert\NotBlank(message="Hotel ID is required.")
     * @Assert\Type(type="integer", message="Hotel ID must be an integer.")
     */
    public $hotelId;

    /**
     * @Assert\NotBlank(message="Check-in date is required.")
     * @Assert\Date(message="Check-in date must be a valid date.")
     */
    public $checkIn;

    /**
     * @Assert\NotBlank(message="Check-out date is required.")
     * @Assert\Date(message="Check-out date must be a valid date.")
     */
    public $checkOut;

    /**
     * @Assert\NotBlank(message="Number of guests is required.")
     * @Assert\Type(type="integer", message="Number of guests must be an integer.")
     * @Assert\GreaterThan(value=0, message="Number of guests must be greater than zero.")
     */
    public $numberOfGuests;

    /**
     * @Assert\NotBlank(message="Number of rooms is required.")
     * @Assert\Type(type="integer", message="Number of rooms must be an integer.")
     */
    public $numberOfRooms;

    /**
     * @Assert\NotBlank(message="Currency is required.")
     * @Assert\Currency(message="Invalid currency.")
     */
    public $currency;

    /**
     * Transform the SearchRequest DTO into an associative array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hotelId' => $this->hotelId,
            'checkIn' => $this->checkIn,
            'checkOut' => $this->checkOut,
            'numberOfGuests' => $this->numberOfGuests,
            'numberOfRooms' => $this->numberOfRooms,
            'currency' => $this->currency,
        ];
    }
}
