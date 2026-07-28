#!/bin/bash
# Railway startup script
# DB_HOST, DB_USER, DB_PASS, DB_NAME, TOKEN_SECRET harus di-set dari Railway dashboard

php -S 0.0.0.0:${PORT:-8080} -t siakad/
