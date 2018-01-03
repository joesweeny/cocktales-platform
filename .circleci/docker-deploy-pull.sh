#!/bin/bash

set -e

ssh root@${STAGING_SERVER} "cd /opt; docker-compose -f docker-compose.staging.yml up -d"
