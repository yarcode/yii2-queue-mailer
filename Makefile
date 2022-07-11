build:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose up -d --build

down:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose down

start:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose up -d

test: test80
test80:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose build --pull php80
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose run php80 vendor/bin/phpunit --verbose --colors=always
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose down
