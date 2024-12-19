require_relative 'date_type'

module Types
  class EventType < GraphQL::Schema::Object
    field :title, String, null: false
    field :date, Types::DateType, null: false
  end
end
