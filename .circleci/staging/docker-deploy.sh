#!/bin/bash

set -e

scp docker-compose.staging.yml root@${STAGING_SERVER}:~/staging
scp ./.circleci/staging/env.sh root@{$STAGING_SERVER}:
ssh root@${STAGING_SERVER} "docker-compose -f ./staging/docker-compose.staging.yml pull web && docker-compose -f ./staging/docker-compose.staging.yml up -d"