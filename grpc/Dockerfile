# Use an official Python base image
FROM python:3.9-slim

# Set the working directory in the container
WORKDIR /app

# Copy the rest of the application code
COPY . .

RUN pip3 install -r requirements.txt

# Expose the gRPC server port
EXPOSE 7000

RUN chmod +x ./entrypoint.sh

ENTRYPOINT ["bash", "./entrypoint.sh"]