# Symfony with Docker

```bash
cd s4lab_project
git clone git@bitbucket.org:corvet/s4lab_20180101_docker_symfony.git ./
git remote remove origin
sudo chmod -R 777 ./
cd docker_symfony/
docker-compose down
docker-compose up -d --build
docker exec -it s4lab_php_fpm bash
composer install
```
## Setup connection for database
file __.env__
```bash
DATABASE_URL=mysql://db_user:db_password@mysql:3306/db_name
```