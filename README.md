# Moonhotels’ HUB (Task)

This solution addresses the Moon Hotels Task, which focuses on implementing a search functionality that integrates data from multiple hotel providers. The solution ensures that incoming requests are properly validated, transformed, and processed through various hotel connectors, applying business rules such as profit margins and room availability filters.


## Requirements

* **docker**
* **docker-compose**

## Installation

```bash
git clone <repo_url>
cd <project_directory>
docker-compose up --build -d
```

### Migrations and fixtures
```bash
docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
docker-compose exec php php bin/console doctrine:fixtures:load
```


### Testing

```bash
docker-compose exec php bin/phpunit
```

### Access and test api

Visit **http://localhost:8000/api/doc** to view the Swagger UI, and test endpoint.


## Topics Covered

* **Data Transfer Objects (DTOs):** Encapsulates request data into specific objects.

* **Custom Validation:** Symfony’s Validator Component is used to enforce business rules on incoming data by defining constraints within the DTOs. This ensures data integrity and reduces the risk of processing invalid or incomplete requests.

* **Custom Exception Handling:** Custom exceptions like ValidationException are used to provide meaningful feedback when validation or deserialization errors occur. Centralized exception handling ensures consistent error responses across the API.

* **Serialization and Deserialization:** The Serializer Component is used to deserialize incoming JSON into DTOs and transform those DTOs into arrays when necessary. This allows for a clear and structured way to handle input and output data.

* **OpenAPI Documentation (Swagger):** Using NelmioApiDocBundle, the API is documented with Swagger annotations to provide clear documentation for developers, allowing them to interact with the API easily and understand its structure.

* **Caching strategies:** Redis caching is implemented to optimize performance by caching search results per hotel provider. The cache keys are generated using a hash of request parameters and the connector identifier, reducing unnecessary calls to external services and improving response times.


* **Dependency Injection:** Symfony’s Dependency Injection Container is used to inject services such as the Validator, Serializer, Cache, and other dependencies into the controller and services, promoting loose coupling and easier testing.


* **Normalization:** Data is normalized across multiple hotel providers to ensure consistency in processing, regardless of the input format differences. This ensures that responses from different connectors can be easily aggregated and processed.

* **Trait Reusability:** A trait (ValidatesRequest) is used to centralize deserialization and validation logic, ensuring that controllers can focus on business logic while reusing common functionality, thereby reducing duplication.

* **Transformation of DTOs:** The DTOs are equipped with methods to transform them into associative arrays, making it easier to work with them in different parts of the application, such as passing data to services or repositories.



docker-compose down
docker-compose up --build -d

docker-compose exec php php bin/console doctrine:database:create
docker-compose exec php php bin/console doctrine:migrations:migrate
docker-compose exec php php bin/console doctrine:fixtures:load
docker-compose exec php bin/phpunit


check endpoint

http://localhost:8000/api/doc