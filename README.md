RUN

<code>composer update</code>

Update .env

Set the database details with correct details you have, like below:

<code>DATABASE_URL="mysql://127.0.0.1:3306/rest_api_challenge"</code>

Run Commands (On root folder)

<code>php bin/console doctrine:database:create</code>

<code>php bin/console make:migration</code>

<code>php bin/console doctrine:migrations:migrate</code>

<code>php bin/console app:customer-import --numpages 6</code>


REST API POINTS

<code>/customers (will show all data under customers)</code>

<code>/customers/{id} (will show data of a certain customer)</code>
