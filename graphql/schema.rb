require_relative 'types/query_type'
require_relative 'types/mutation_type'

class MySchema < GraphQL::Schema
  query(Types::QueryType) # query voor GET requests
  mutation(Types::MutationType) # mutation voor POST, PUT of DELETE requests
end
