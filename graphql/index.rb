require 'sinatra'
require 'graphql'
require 'json'
require 'rack/cors'
require_relative 'types/date_type'
require_relative 'types/event_type'
require_relative 'controllers/graphql_controller'
require_relative 'types/query_type'
require_relative 'schema'

# Use CORS middleware
use Rack::Cors do
  allow do
    origins '*'
    resource '*', headers: :any, methods: [:get, :post]
  end
end

post '/graphql' do
  request_payload = JSON.parse(request.body.read)
  result = MySchema.execute(request_payload['query'])
  result.to_json
end

set :port, 4000
set :bind, '0.0.0.0'
