require 'sinatra'
require 'graphql'
require 'json'
require_relative 'types/date_type'
require_relative 'types/event_type'
require_relative 'controllers/graphql_controller'
require_relative 'types/query_type'
require_relative 'schema'
require_relative 'route'

set :cross_origin, true
before do
  response.headers['Access-Control-Allow-Origin'] = '*'
end

post '/graphql' do
  request_payload = JSON.parse(request.body.read)
  result = MySchema.execute(request_payload['query'])
  result.to_json
  return result
end

set :port, 4000
