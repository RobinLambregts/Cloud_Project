module Types
  class DateType < GraphQL::Schema::Object
    field :day, Int, null: true
    field :month, Int, null: true
    field :year, Int, null: true
  end
end
