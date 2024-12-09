#!/bin/bash

pip3 install --no-cache-dir --break-system-packages -r ./requirements.txt

# # DEVELOPMENT mode
echo "RUNNING in development mode"
tail -f /dev/null

# LIVE mode
# echo "RUNNING in live mode"
# flask run --host=0.0.0.0





# Attach shel (RUN on host: docker exec -it flask_api /bin/bash) or attach VSCode
##### COMMANDS FOR INSIDE THE CONTAINER
# RUN: python3 app.py