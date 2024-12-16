module Queries
  class Query < GraphQL::Schema::Object
    field :events, [Types::EventType], null: false

    def events
      [
        { date: { day: 20, month: 12, year: 2024 }, title: 'Studeren' },
        { date: { day: 31, month: 12, year: 2024 }, title: 'Oudejaar' }
      ]
    end
  end
end
