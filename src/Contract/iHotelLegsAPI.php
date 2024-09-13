<?php
namespace App\Contract;

interface iHotelLegsAPI
{
    public function search(array $hubRequest): array;
}