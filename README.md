# BetterCondos backend
## Docker

### First time

You have to build the containers with:

```bash
docker-compose build
```

You need to create a file called mysql.env with the following content:

```conf
MYSQL_HOST=db
MYSQL_PORT=3306
MYSQL_DATABASE=<the name of the database>
MYSQL_USER=<the user of the database>
MYSQL_PASSWORD=<the password of the user>
MYSQL_ROOT_PASSWORD=<the root password of the database>
COMPOSER_MEMORY_LIMIT=-1
```

Keep in mind that the parameters.yml MUST have the following:

```conf
parameters:
    database_host: '%env(MYSQL_HOST)%'
    database_port: '%env(MYSQL_PORT)%'
    database_name: '%env(MYSQL_DATABASE)%'
    database_user: '%env(MYSQL_USER)%'
    database_password: '%env(MYSQL_PASSWORD)%'
    ...
```

Then you can run it:

```bash
docker-compose up
# or
docker-compose up -d # for detached mode (no logs or for prod)
```

Then you have to install the composer libraries:

```bash
docker-compose exec tools composer install # or update if you wanted to
```

### Always

Run it:

```bash
docker-compose up
# or
docker-compose up -d # for detached mode (no logs or for prod)
```

### Useful Commands

```bash
docker-compose exec php bin/console doctrine:schema:update --dump-sql --force
docker-compose exec -T php bin/console cache:clear
# DB Backup
docker-compose exec db sh -c 'mysqldump --force --opt --user=$MYSQL_USER --password=$MYSQL_PASSWORD --databases $MYSQL_DATABASE' > ./restore/quick/db_backup.sql
# DB Restore
docker-compose exec db sh -c 'mysql --force --user=$MYSQL_USER --password=$MYSQL_PASSWORD < /restore/db_backup.sql'
docker-compose down
```