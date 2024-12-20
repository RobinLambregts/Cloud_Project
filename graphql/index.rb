require 'sinatra'
require 'graphql'
require 'json'
require 'rack/cors'
require_relative 'types/date_type'
require_relative 'types/event_type'
require_relative 'types/query_type'
require_relative 'schema'

$events ||= [] # Global variable to store events

# Use CORS middleware
use Rack::Cors do
  allow do
    origins 'http://localhost:8080' # Specify the origin(s) you want to allow
    resource '*',
             headers: :any,
             methods: [:get, :post, :put, :delete, :options, :head],
             max_age: 600
  end
end

post '/graphql' do
  begin
    request_payload = JSON.parse(request.body.read)
    result = MySchema.execute(
      request_payload['query'],
      variables: request_payload['variables'] || {},
      operation_name: request_payload['operationName']
    )
    result.to_json
  rescue JSON::ParserError => e
    halt 400, { error: "Invalid JSON: #{e.message}" }.to_json
  rescue StandardError => e
    halt 500, { error: "Server Error: #{e.message}" }.to_json
  end
end


get '/' do
  'GraphQL server is running'
end

set :port, 4000
set :bind, '0.0.0.0'
