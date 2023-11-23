# Stock Monitor


## How to run

### Step 1: Launch Docker
```
# Generate SSL certificates This is NOT the Symfony's bin folder
bash bin/certgen.sh
docker-compose up -d
```

### Step2 Config DB

For local development Set at .env.local (credentials are for local only development):

```
DATABASE_URL="mysql://php_app_usr:php_app_pwd@mariadb:3306/php_app?serverVersion=10.4.31-MariaDB-1&charset=utf8mb4"
```

Then run migrations:

```
docker exec -ti -u www-data php_app /bin/bash
php bin/console doctrine:migrations:execute
php bin/console data:populate:symbols
```

## Step 3 APP URL

Visit https://172.161.0.2/ at the browser

## App Location

The app is located upon `./app` folder.

## Email configuration

A mailhog is shipped in the docker-compose.yaml. At .env set

```
MAILER_DSN=smtp://mailhog:1025?verify_peer=false
```
The `mailhog` is the service name and used as in-docker domain. In order for the mails the following command must run as well:

```
php bin/console messenger:consume
```

Then at https://172.161.0.6:8085 you'll see the sent emails. The emails are not sent actually mailhog emulates the transport.