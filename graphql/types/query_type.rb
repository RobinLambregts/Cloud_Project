module Types
  class QueryType < GraphQL::Schema::Object
    field :events, [Types::EventType], null: false do
      argument :date, String, required: false
    end

    def events(date: nil)
      return $events || [] unless date

      # Parse the input date
      parsed_date = Date.parse(date) rescue nil
      return [] unless parsed_date

      # Filter events by date
      ($events || []).select do |event|
        event_date = Date.new(event[:date][:year], event[:date][:month], event[:date][:day]) rescue nil
        event_date == parsed_date
      end
    end
  end
end
