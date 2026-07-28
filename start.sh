#!/bin/bash

# Seed in background so server starts immediately (healthcheck passes)
php siakad/seed.php &

# Start PHP built-in server
php -S 0.0.0.0:${PORT:-8080} -t siakad/
