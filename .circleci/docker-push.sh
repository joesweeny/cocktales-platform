#!/bin/bash

set -e

${DOCKER_HUB_PASSWORD} | docker login -u ${DOCKER_HUB_USERNAME} --password-stdin
docker tag "${DOCKER_IMAGE}:latest" "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"
docker push "${DOCKER_HUB_REPOSITORY}/${DOCKER_IMAGE}:latest"