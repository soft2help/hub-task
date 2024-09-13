<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Exception\ValidationException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

trait ValidatesRequest
{


    /**
     * Deserialize JSON data into a DTO object and validate it.
     *
     * @param Request $request The HTTP request containing JSON data
     * @param string $dtoClass The class name of the DTO to deserialize into
     * @param SerializerInterface $serializer The serializer service
     * @param ValidatorInterface $validator The validator service
     * @return object The validated DTO object
     * @throws ValidationException If validation fails
     */
    public function deserializeAndValidate(
        Request $request,
        string $dtoClass,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): object {
        try {
            // Deserialize the JSON request content into the specified DTO class
            $dto = $serializer->deserialize($request->getContent(), $dtoClass, 'json');

            // Validate the deserialized DTO
            $errors = $validator->validate($dto);

            // Throw ValidationException if any validation errors exist
            if (count($errors) > 0) {
                throw new ValidationException($errors);
            }

            return $dto;
        } catch (NotEncodableValueException $ex) {
            $violations = new ConstraintViolationList();
            $violations->add(new ConstraintViolation(
                'Invalid JSON format.', // The error message
                null,                   // The message template
                [],                     // Message parameters
                null,                   // The root object
                '',                     // The invalid field path (empty for global errors)
                null                    // The invalid value (null for global errors)
            ));

            // Throw ValidationException with the custom violations
            throw new ValidationException($violations);
        }
    }
}
