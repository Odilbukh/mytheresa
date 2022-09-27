I have prepared a file .env and sqlite database to make it easy for you to run the project.

<h2>Installation and Run project</h2>

1. Run the following commands to install and run the project

`composer install`<br><br>
2. To fill DB

`php artisan db:seed`<br><br>
3. To run the project

`php artisan serve`<br><br>
4. To run tests

`php artisan test`<br><br>

<h2>Endpoints</h2>

1. GET "/api/products" - get list of products<br>
       Parameters: <br>
            size (required, integer)<br>
            page (required, integer)<br>
       Filters:<br>
            category (string) - filter by category name of products<br>
            discount (integer) - filter by discount percentage of products<br>
            priceLess (integer) - filter by product price. This filter applies before
                                 discounts are applied and will show products with prices lesser than 
                                 or equal the value provided<br><br>
All filter works as a query string parameter. <br>
Example: http://127.0.0.1:8000/api/products/?page=1&size=5&category=boots
   <br><br>You can you multiple filters. It shows a list of products that matches all filters<br>
Example: http://127.0.0.1:8000/api/products/?page=1&size=5&category=boots&priceLess=89000
   <br><br>
2. GET "api/products/{id}" - get product by id<br><br>
3. POST "api/products" - create new product<br>
       Fields:<br>
           sku (required, string)<br>
           name (required, string)<br>
           category (required, string)<br>
           price (required, double like 150.00)<br>
           discount_id (optional, integer, exists:discounts,id)<br><br>
4. PUT "api/products/{id}" - update product<br>
       Fields:<br>
           sku (string)<br>
           name (string)<br>
           category (string)<br>
           price (double like 150.00)<br>
           discount_id (integer, exists:discounts,id)<br><br>
5. DELETE "api/products/{id}" - delete product<br>
