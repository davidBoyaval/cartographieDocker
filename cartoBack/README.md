Docker, Symfony, PostGis & cie...
=================================

## Prérequis

- Comprendre docker
    - https://fr.wikipedia.org/wiki/Docker_%28logiciel%29
    - https://www.ovh.sn/news/articles/a2185.seance-rattrapage-docker
- Installer docker
- Installer docker-compose

## PostGis seul

https://hub.docker.com/r/mdillon/postgis/

```bash
docker run --name postgis \
    -e POSTGRES_DB=somerandomdatabasename \
    -e POSTGRES_USER=somerandomusername \
    -e POSTGRES_PASSWORD=somesecuredpassword \
    -d mdillon/postgis
```

## I ♥ docker  

```bash
git clone https://github.com/leahpar/dok <project-name>
cd <project-name>
```

### Création des containers

Configuration des paramètres postgres dans `docker-compose.yml`, puis

```bash
docker-compose build
```

### Lancement des containers

```bash
docker-compose up
```

### Création projet symfony

```bash
docker-compose exec php composer create-project symfony/website-skeleton .
```

```dotenv
# app/.env
DATABASE_URL=postgresql://<user>:<password>@<container>:5432/<dbname>?serverVersion=11&charset=utf8
```

Enjoy ! http://localhost:8080/

### Bundle PostGis 

```bash
docker-compose exec php composer require jsor/doctrine-postgis
```
Voir https://github.com/jsor/doctrine-postgis pour la configuration symfony.

### Quelques commandes utiles...

```bash
docker-compose exec php php bin/console make:controller
docker-compose exec php php bin/console make:entity
docker-compose exec php php bin/console doctrine:schema:update --dump-sql
docker-compose exec php php bin/console doctrine:schema:update --force
```

### Pour aller plus loin...

- `/Makefile` pour des commandes plus pratiques
- `/.env` pour une configuration de docker-compose plus propre
