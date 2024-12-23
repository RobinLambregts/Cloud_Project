from flask import Flask, jsonify
import mysql.connector
import os
from flask_cors import CORS

app = Flask(__name__)

CORS(app, origins='*')

@app.route('/sports/vote/<string:sportName>/<int:vote>', methods=['POST'])
def vote_sport(sportName, vote):
    try:
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'database'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', 'root'),
            database=os.getenv('DB_NAME', 'Sport_db')
        )
        cursor = conn.cursor()

        print("vote: ", vote)
        
        cursor.execute("UPDATE sporten SET stemmen = stemmen + %s WHERE naam=%s", (vote, sportName))
        conn.commit()
        
        conn.close()
        return jsonify({"message": f"Vote for sport {sportName} updated successfully"}), 200
    except mysql.connector.Error as err:
        return jsonify({"error": f"Database error: {str(err)}"}), 500

@app.route('/sports/add/<string:sportName>/<string:sportLocation>/<string:eenheid>', methods=['POST'])
def add_sport(sportName, sportLocation, eenheid):
    try:
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'database'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', 'root'),
            database=os.getenv('DB_NAME', 'Sport_db')
        )
        cursor = conn.cursor()
        
        cursor.execute("INSERT INTO sporten (naam, locatie, eenheid) VALUES (%s, %s, %s)", (sportName, sportLocation, eenheid))
        conn.commit()
        
        conn.close()
        return jsonify({"message": f"Sport with name {sportName} added successfully"}), 200
    except mysql.connector.Error as err:
        return jsonify({"error": f"Database error: {str(err)}"}), 500

@app.route('/sports/remove/<string:sportName>', methods=['DELETE'])
def delete_sport(sportName):
    try:
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'database'),
            user=os.getenv('DB_USER', 'root'),
            password=os.getenv('DB_PASSWORD', 'root'),
            database=os.getenv('DB_NAME', 'Sport_db')
        )
        cursor = conn.cursor()
        
        cursor.execute("DELETE FROM sporten WHERE naam=%s", (sportName,))
        conn.commit()
        
        if cursor.rowcount == 0:
            return jsonify({"error": "Sport not found"}), 404
        
        conn.close()
        return jsonify({"message": f"Sport with name {sportName} deleted successfully"}), 200
    except mysql.connector.Error as err:
        return jsonify({"error": f"Database error: {str(err)}"}), 500


def get_sports_from_db():
    try:
        print("connecting...")
        conn = mysql.connector.connect(
            host=os.getenv('DB_HOST', 'database'),  
            user=os.getenv('DB_USER', 'root'),     
            password=os.getenv('DB_PASSWORD', 'root'),
            database=os.getenv('DB_NAME', 'Sport_db')
        )
        print("connection done")
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM sporten")  # Adjust the query as per your database schema
        sports = cursor.fetchall()
        conn.close()
        return sports
    except mysql.connector.Error as err:
        return {"error": f"Database error: {str(err)}"}

@app.route('/sports', methods=['GET'])
def get_sports():
    sports = get_sports_from_db()
    if isinstance(sports, dict) and "error" in sports:
        # Return an error response if the database query failed
        return jsonify(sports), 500
    return jsonify(sports), 200  # Return the list of sports as JSON

@app.route('/')
def reroute():
    return "Dit is de sports app, typ /sports achter de url om gegevens te bekijken"

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)