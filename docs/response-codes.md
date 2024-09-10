# API Response codes

These are the codes that the API is currently returning, with the explanation of each one, so you are able to adapt the translations or to the code depending on these codes. 

| Code     | HTTP Status     | Message                                                | Parameters                                                  | Explanation                                                                             |
|----------|-----------------|--------------------------------------------------------|-------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| `000000` | 200 Ok          | Ok                                                     |                                                             | The request has been executed correctly.                                                |
| `999000` | 400 Bad Request | Item list is empty.                                    |                                                             | The list of items specified is empty                                                    |
| `999001` | 400 Bad Request | Request body is invalid                                |                                                             | The request body is not a valid json                                                    |
| `001000` | 400 Bad Request | Customer with id "{id}" not found.                     | `id`: Id of the customer not found in the system            | The customer specified don't exists in the system                                       |
| `002000` | 400 Bad Request | Order with id "{id}" already has a discount applied.   | `orderId`: Id of the order that already has been discounted | The order already has been discounted                                                   |
| `003000` | 400 Bad Request | Invalid price {price}, price cannot be less than zero. | `price`: The negative price                                 | The order contains a negative value for a price field                                   |
| `004000` | 400 Bad Request | Product with id "{id}" not found.                      | `id`: Id of the product not found in the system             | The order contains a product that cannot be found in the system                         |
| `004001` | 400 Bad Request | Quantity value for product %s is not valid             | `productId`: Id of the product that has a wrong quantity    | The order contains a product that has an invalid quantity (should be a positive number) |
