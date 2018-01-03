#!/bin/bash

set -e

docker login --username=${DOCKER_HUB_USERNAME} --email={DOCKER_HUB_EMAIL}
${DOCKER_HUB_PASSWORD}
docker tag "${DOCKER_IMAGE}:latest" "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"
docker push "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"