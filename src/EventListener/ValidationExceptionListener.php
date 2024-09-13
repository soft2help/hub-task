<?php

namespace App\EventListener;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ValidationExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle ValidationException
        if ($exception instanceof ValidationException) {
            $errors = [];
            foreach ($exception->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            $response = new JsonResponse([
                'status' => 'error',
                'errors' => $errors
            ], JsonResponse::HTTP_BAD_REQUEST);

            // Set the new response for the event
            $event->setResponse($response);
        }
    }
}
