RUN

<code>composer update</code>

Update .env

Set the database details with correct details you have, like below:

<code>DATABASE_URL="mysql://127.0.0.1:3306/rest_api_challenge"</code>

Run

-- php bin/console doctrine:database:create

-- php bin/console make:migration

-- php bin/console doctrine:migrations:migrate

-- php bin/console app:customer-import --numpages 6


REST API POINTS
-- /customers (will show all data under customers)

-- /customers/{id} (will show data of a certain customer)
