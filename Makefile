build:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose up -d --build

down:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose down

start:
	COMPOSE_FILE=tests/docker/docker-compose.yml docker-compose up -d
