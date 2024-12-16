# Use the official Node.js image as the base image
FROM node:16

# Install dependencies for Ruby, GPG, and build tools
RUN apt-get update -y && apt-get install -y \
  curl \
  gnupg2 \
  build-essential \
  libpq-dev \
  libssl-dev \
  libreadline-dev \
  zlib1g-dev \
  && rm -rf /var/lib/apt/lists/*

# Install the required GPG keys for RVM (Ruby Version Manager)
RUN curl -sSL https://rvm.io/mpapis.asc | gpg --import - \
  && curl -sSL https://rvm.io/pkuczynski.asc | gpg --import -

# Install RVM and Ruby 2.7.4
RUN curl -sSL https://get.rvm.io | bash -s stable --ruby=2.7.4

# Set environment variables for RVM and Ruby
ENV PATH /usr/local/rvm/rubies/ruby-2.7.4/bin:$PATH

# Install the required Ruby gems, such as GraphQL
RUN /bin/bash -l -c "gem install graphql"

# Set the working directory
WORKDIR /var/www/graphql

# Copy package.json and package-lock.json for npm installation
COPY ../package*.json ./

# Install Node.js dependencies
RUN npm install

# Copy the rest of the application files
COPY . .

RUN bundle install

# Expose the port the app runs on
EXPOSE 4000

# Command to run the application
CMD ["npm", "start"]
