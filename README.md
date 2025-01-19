# Cloud_Project

app2:
    - REST
    - PORT: 5000
    - complete automatic startup with docker

graphql:
    - GraphQL
    - PORT: 4000
    - complete automatic startup with docker

grpc:
    - GRPC
    - PORT: -> server: 50051
            -> client: 7000
    - automatic startup with docker: server.py
    - manual startup: client.py (python3 client.py in de grpc container) !!!!

soap:
    - SOAP
    - PORT: 5299
    - complete automatic startup with docker

mosquitto: 
    - MQTT
    - PORT: 1883
    - complete automatic startup with docker

    - WEBSOCKETS
    - PORT: 9001
    - complete automatic startup with docker

    - met image in docker-compose.yml