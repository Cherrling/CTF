#!/bin/bash

cd /home/ctf/

while true; do
    echo "Starting server at $(date)"
    ./server
    sleep 5
done
