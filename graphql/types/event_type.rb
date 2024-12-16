module Types
  class EventType < GraphQL::Schema::Object
    field :date, Types::DateType, null: true
    field :title, String, null: true
  end
end
