<?php

namespace App\Service;

use App\Repository\SettingRepository;

class BusinessRulesService
{

    private $settingRepository;
    private $removeRoomsNonCancellable;
    private $profitDefaultFeePercentage;

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
        $this->removeRoomsNonCancellable = filter_var(($this->settingRepository->get('hub_business_rules', 'remove_non_cancellable') || false), FILTER_VALIDATE_BOOLEAN);
        $this->profitDefaultFeePercentage = floatval($this->settingRepository->get('hub_business_rules', 'profit_default_fee_percentage') || 5.0);
    }


    /**
     * Apply business rules to the response from each connector.
     *
     * @param array $response The raw response from the connector.
     * @return array The processed response with business rules applied.
     */
    public function apply(array $response): array
    {
        foreach ($response as &$room) {
            foreach ($room['rates'] as $key => &$rate) {
                $rate['price']  = round(
                    $rate['price'] * (1 + $this->profitDefaultFeePercentage / 100),
                    2
                );


                // Example Rule: Remove non-cancellable rooms
                if (!$rate['isCancellable'] && $this->removeRoomsNonCancellable) {
                    // You can choose to remove or modify non-cancellable rooms

                    unset($room['rates'][$key]);
                }
            }
        }

        return $response;
    }
}
