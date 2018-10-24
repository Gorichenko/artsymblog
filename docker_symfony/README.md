# Docker for Symfony

## s4lab project
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

vim .env
DATABASE_URL=mysql://db_user:db_password@mysql:3306/db_name
```

## Quick tour

```bash
cd my_project
mkdir var/docker_symfony
cd var/docker_symfony
git clone git@bitbucket.org:corvet/docker_symfony.git ./
cp .env.dist .env
docker-compose ps
docker-compose down
docker-compose up -d --build
```

See more: https://habr.com/post/346086/

## Install docker

* [Install Docker CE](https://docs.docker.com/install/linux/docker-ce/ubuntu/).
Perform all necessary actions on the instructions and perform the installation.
```bash
sudo apt-get install docker-ce
```
## Check permission for Docker
```bash
docker ps -a
```
If you can not run the command without sudo, you need to add the group, log out and log in again.
```bash
sudo usermod -aG docker $USER
```
## Install Docker Compose

* [Install Docker Compose](https://docs.docker.com/compose/install/). 
```bash
docker-compose --version
```

## Docker Helper

These are often used commands that you need to learn:
```bash
history | grep docker
docker-compose down
docker-compose ps
docker ps -a
docker-compose up -d --build
docker exec -it s3sylius_php_fpm bash
```

## Permission access
Before you execute any command, you must understand what this command is doing!!!
```bash
groups $USER
groups www-data
sudo usermod -aG $USER www-data
```

## Delete images, containers & networks
This is useful when you want to clean the docker. )))
```bash
docker images -a
docker rmi $(docker images -a -q)

docker ps -a
docker stop $(docker ps -a -q)
docker rm $(docker ps -a -q)

docker network ls
```
* [Remove all stopped containers](https://docs.docker.com/engine/reference/commandline/container_prune/)
```bash
docker container prune
```
* [Remove unused images](https://docs.docker.com/engine/reference/commandline/image_prune/)
```bash
docker image prune
```
* [Remove all unused networks](https://docs.docker.com/engine/reference/commandline/network_prune/)
```bash
docker network prune
```
* [Remove all unused local volumes](https://docs.docker.com/engine/reference/commandline/volume_prune/)
```bash
docker volume prune
```



See more: https://habr.com/company/flant/blog/336654/
