#!/bin/bash
set -e

# Auto-seed database on first run
php siakad/seed.php

# Start PHP built-in server
php -S 0.0.0.0:${PORT:-8080} -t siakad/
