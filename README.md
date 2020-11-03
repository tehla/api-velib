# Velib

## Install

### php + nginx + postgresql :
`docker-compose build`
`docker-compose up -d`

### symfony + doctrine
`composer install`
`bin/console doctrine:schema:update --force`

### load api dataset 
(from https://opendata.paris.fr/explore/dataset/velib-emplacement-des-stations/information/)
`bin/console velib:stations:get`

## Issues : 
uuid_generate_v4 function should not be available on your postgresql database.
Remove the appropriate extension and create it again :
````
DROP EXTENSION "uuid-ossp";
CREATE EXTENSION "uuid-ossp";
SELECT uuid_generate_v4();
````
