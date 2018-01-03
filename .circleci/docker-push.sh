#!/bin/bash

set -e

docker login -u ${DOCKER_HUB_USERNAME} --password-stdin {DOCKER_HUB_PASSWORD} -e {DOCKER_HUB_EMAIL}
docker tag "${DOCKER_IMAGE}:latest" "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"
docker push "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"