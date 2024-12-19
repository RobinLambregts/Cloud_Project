# Use the official Ubuntu image as the base image
FROM ruby:2.7.8

# Set the working directory
WORKDIR /var/www/graphql

# Copy the rest of the application files
COPY . .

RUN bundle install

# Expose the port the app runs on
EXPOSE 4000

ENTRYPOINT ["bash", "./entrypoint.sh"]
