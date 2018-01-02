#!/usr/bin/env bash
set -e

docker-compose down --rmi local --remove-orphans
docker-compose up --force-recreate -d --remove-orphans
