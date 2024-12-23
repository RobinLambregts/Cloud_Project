import grpc
import sports_pb2
import sports_pb2_grpc
from flask import Flask, jsonify
import os
from flask_cors import CORS

app = Flask(__name__)

CORS(app, origins='*')

@app.route('/')
def get_best_sport():
    try:
        with grpc.insecure_channel('localhost:50051') as channel:
            stub = sports_pb2_grpc.SportsServiceStub(channel)
            response = stub.GetSports(sports_pb2.EmptyRequest())

            best_sport = None
            max_votes = 0

            for sport in response.sports:
                if sport.votes > max_votes:
                    max_votes = sport.votes
                    best_sport = sport.name

            if best_sport is None:
                return jsonify({"message": "No sports available"}), 404

            return jsonify({"best_sport": best_sport, "votes": max_votes}), 200

    except grpc.RpcError as e:
        return jsonify({"error": "Failed to fetch sports data", "details": str(e)}), 500

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=7000, debug=True)
