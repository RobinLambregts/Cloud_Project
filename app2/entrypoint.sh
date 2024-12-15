#!/bin/bash

pip3 install --no-cache-dir --break-system-packages -r ./requirements.txt

# # DEVELOPMENT mode
echo "RUNNING in development mode"
tail -f /dev/null

# LIVE mode
# echo "RUNNING in live mode"
# flask run --host=0.0.0.0

exec python3 app.py
