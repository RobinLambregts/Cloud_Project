#!/bin/bash

pip3 install --no-cache-dir --break-system-packages -r ./requirements.txt

# # DEVELOPMENT mode
echo "RUNNING in development mode"
python app.py

# LIVE mode
# echo "RUNNING in live mode"
# flask run --host=0.0.0.0
