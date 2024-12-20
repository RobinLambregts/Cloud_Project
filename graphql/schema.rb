require_relative 'types/query_type'
require_relative 'types/mutation_type'

class MySchema < GraphQL::Schema
  query(Types::QueryType)
  mutation(Types::MutationType)
end
