# **Setup:**

## Install Docker  and Docker Compose according to your local OS
[Docker documentation](https://docs.docker.com/install/).  
[Docker Compose documentation](https://docs.docker.com/compose/install/).


## Copy project
```bash
git clone {url}
```

## Run docker
Open tests directory
```bash
docker-compose up
```

## Open console
```bash
docker-compose run php bash
```

## Run tests
```bash
vendor/bin/phpunit -c phpunit.xml
```