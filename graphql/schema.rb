require_relative 'types/query_type'

class MySchema < GraphQL::Schema
  query(Types::QueryType)
end
