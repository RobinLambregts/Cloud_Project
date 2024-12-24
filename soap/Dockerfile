# Use the official .NET SDK for .NET 8.0 as a build environment
FROM mcr.microsoft.com/dotnet/sdk:8.0 AS build

# Set the working directory inside the container
WORKDIR /app

# Copy the project file and restore dependencies
COPY *.csproj ./
RUN dotnet restore

# Copy the rest of the application files, including entrypoint.sh
COPY . ./

# Build the application
RUN dotnet publish -c Release -o /publish

# Use the official ASP.NET Core runtime image for .NET 8.0
FROM mcr.microsoft.com/dotnet/aspnet:8.0 AS runtime

# Set the working directory inside the container
WORKDIR /app

# Copy the built application into the runtime image
COPY --from=build /publish ./

# Ensure entrypoint.sh is copied to the container
COPY entrypoint.sh /app/entrypoint.sh

# Ensure entrypoint.sh has executable permissions
RUN chmod +x /app/entrypoint.sh

# Expose port 8000 for the service
EXPOSE 5299

# Set the entry point to the script
ENTRYPOINT ["bash", "/app/entrypoint.sh"]
