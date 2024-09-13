<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\HubService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SettingRepository;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use App\Traits\ValidatesRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\SearchRequest;

class HubController extends AbstractController
{
    use ValidatesRequest;
    /**
     * @Route("/api/search", name="app_search", methods={"POST"})
     * 
     * @OA\Post(
     *     path="/api/search",
     *     summary="Search for rooms across multiple providers",
     *     description="This endpoint allows users to search for room availability across various hotel providers.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="hotelId", type="integer", example=1),
     *             @OA\Property(property="checkIn", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="checkOut", type="string", format="date", example="2024-01-05"),
     *             @OA\Property(property="numberOfGuests", type="integer", example=2),
     *             @OA\Property(property="numberOfRooms", type="integer", example=1),
     *             @OA\Property(property="currency", type="strin", example="EUR"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="rooms",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="roomId", type="integer", example=1),
     *                     @OA\Property(
     *                         property="rates",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="mealPlanId", type="integer", example=1),
     *                             @OA\Property(property="isCancellable", type="boolean", example=false),
     *                             @OA\Property(property="price", type="number", format="float", example=123.48)
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input or validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string", example="Hotel ID is required.")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     * 
     */
    public function search(Request $request, HubService $hubService, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $searchRequest = $this->deserializeAndValidate(
            $request,
            SearchRequest::class,
            $serializer,
            $validator
        );

        return new JsonResponse($hubService->search($searchRequest->toArray()));
    }
}
