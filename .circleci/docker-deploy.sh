#!/bin/bash

set -e

scp docker-compose.staging.yml root@${STAGING_SERVER}:
ssh root@${STAGING_SERVER} "docker-compose down -v; docker-compose -f docker-compose.staging.yml up -d --build"