class GraphqlController < Sinatra::Base
  def execute
    query = params[:query]
    variables = params[:variables] || {}
    result = Schema.execute(query, variables: variables)
    render json: result
  end
end