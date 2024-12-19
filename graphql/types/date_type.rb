module Types
  class DateType < GraphQL::Schema::Object
    field :day, Integer, null: false
    field :month, Integer, null: false
    field :year, Integer, null: false
  end
end
