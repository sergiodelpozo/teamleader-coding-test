# Discounts Tech test

This is a test project to calculate the discounts to be applied to an order based on the defined rules.

## Features

[Here](1-discounts.md) you can find the product requirements for this API
- This test has two endpoints:
    1. GET /healtcheck: An endpoint ot verify that the web api is up and running
    2. POST /discounts: An endpoint that given an order request applies the discounts based on the business logic rules
- A basic set of data has been added to the database on the initialization of the project, in order to test the api

## Requirements

### Docker & docker-compose
Docker and docker-compose needs to be installed in your machine.
You can follow the official guides for [Docker](https://docs.docker.com/get-docker/) and [docker-compose](https://docs.docker.com/compose/install/).

## Environment Variables

This project uses an .env file, that should not change, but maybe you need to change that file to modify de `USERID` variable to be able to run the project..


## Installation

You can do the installation of the project by running the following the steps:

**Clone the project**

```bash
  git clone https://github.com/sergiodelpozo/teamleader-coding-test
```

**Go to the project directory**

```bash
  cd <path-to-cloned-project>
```

**Bootstrap the project**

```bash
  make init
```

## Running Tests

To run tests, run the following command

**Unit tests:**

```bash
  make tests
```

**Integration tests:**

```bash
  make tests-integration
```


**All tests:**

```bash
  make tests-all
```


## Run Locally

Clone the project

**Start the server**

```bash
  make start
```

This command will start a web server on the 8080 port with a database service using the 3306 port.

Then, to verify that the api is up and running:

```bash
$ curl --request GET --url http://localhost:8080/healthcheck
OK
```

To test the API, you can use an example from the folder *example-order*:

```bash
$ curl --request POST \
  --url http://localhost:8080/discounts \
  --header 'Content-Type: application/json' \
  --data '{
  "id": "1",
  "customer-id": "2",
  "items": [
    {
      "product-id": "A101",
      "quantity": "1",
      "unit-price": "9.75",
      "total": "19.50"
    },
    {
      "product-id": "A102",
      "quantity": "6",
      "unit-price": "10.50",
      "total": "63.00"
    }
  ]
}'
```

## API Reference

All the API responses are formatted like the following:
#### Ok response

```json
{
  "code": "000000",
  "message": "OK",
  "data": {
      // In data we return the actual response
  }
}
```

#### Error response

```json
{
  "code": "001000",
  "message": "Error message",
  "data": {
      // Extra error information
  }
}
```

[Here](docs/response-codes.md) you can see all the API response codes

#### Health check

```http
  GET /healthcheck
```


#### Calculate discounts

```http
  POST /discounts
```

*Request Body*

| Parameter            | Type     | Description                                                     |
|:---------------------|:---------|:----------------------------------------------------------------|
| `id`                 | `string` | **Required**. Id of the order                                   |
| `customer-id`        | `string` | **Required**. Id of the customer                                |
| `items`              | `array`  | **Required**. An array containing all the products in the order |
| `items`.`product-id` | `string` | **Required**. Id of the product                                 |
| `items`.`quantity`   | `int`    | **Required**. Number of items to be bought                      |
| `items`.`unit-price` | `float`  | **Required**. Price per unit                                    |
| `items`.`total`      | `float`  | **Required**. Total price without any discounts                 |

*Response*

| Parameter                   | Type     | Description                                     |
|:----------------------------|:---------|:------------------------------------------------|
| `id`                        | `string` | Id of the order                                 |
| `customerId`                | `string` | Id of the customer                              |
| `originalPrice`             | `float`  | The original price of the order                 |
| `totalPrice`                | `float`  | The total price after the discounts are applied |
| `discounts`                 | `array`  | An array containing all the discounts applied   |
| `discounts`.`discountPrice` | `float`  | The total discounted amount                     |
| `discounts`.`reason`        | `string` | The reason why the amount is discounted         |

## Tech Stack
**Services**
- [PHP 8.3](https://www.php.net/releases/8.3/en.php)
- [MySQL 8.4](https://dev.mysql.com/doc/relnotes/mysql/8.4/en/)
  **Framework**
- [Slim](https://www.slimframework.com/) - The framework used in this api
  **External dependencies**
- [Phinx](https://phinx.org/) - Library to manage the migrations of the DB
- [PHP DotEnv](https://github.com/vlucas/phpdotenv) - Library to manage the environment variables
- [PHP DI](https://php-di.org/) - Library to manage the injection of the dependencies
- [PHPUnit](https://phpunit.de/index.html) - Testing framework
- [PHP Faker](https://fakerphp.org/) - Library to generate fake data for the tests


## Authors

- [@sergiodelpozo](https://github.com/sergiodelpozo)
