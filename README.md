This is end-to-end tests for OpenWeatherMap project to check basic user scenarios.  
API documentation is available [here](https://openweathermap.org/api).  
OpenWeatherMap weather service is based on the VANE Geospatial Data Science platform for collecting, processing, and distributing information about our planet through easy to use tools and APIs.

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