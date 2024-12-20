require_relative 'event_type'

module Types
  class QueryType < GraphQL::Schema::Object
    
    field :events, [Types::EventType], null: false

    def events
      $events || []
    end
  end
end
