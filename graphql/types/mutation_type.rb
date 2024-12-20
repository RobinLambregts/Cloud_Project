require_relative 'event_type'

module Types
  class MutationType < GraphQL::Schema::Object
    field :add_event, Types::EventType, null: false do
      argument :title, String, required: true
      argument :date, String, required: true
    end

    def add_event(title:, date:)
      parsed_date = Date.parse(date)
      event = {
        title: title,
        date: { day: parsed_date.day, month: parsed_date.month, year: parsed_date.year }
      }
      $events << event
      event
    rescue ArgumentError
      raise GraphQL::ExecutionError, "Invalid date format. Use YYYY-MM-DD."
    end
  end
end
