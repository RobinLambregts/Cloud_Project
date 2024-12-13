from flask import Flask, jsonify
import mysql.connector
import os
from flask_cors import CORS

app = Flask(__name__)

CORS(app, origins='*')

# Function to get sports data from the database
def get_sports_from_db():
    try:
        # Connect to the MySQL database
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
        return [sport[0] for sport in sports]  # Convert list of tuples to a list of strings
    except mysql.connector.Error as err:
        return {"error": f"Database error: {str(err)}"}

@app.route('/sports', methods=['GET'])
def get_sports():
    sports = get_sports_from_db()
    if isinstance(sports, dict) and "error" in sports:
        # Return an error response if the database query failed
        return jsonify(sports), 500
    return jsonify(sports), 200  # Return the list of sports as JSON

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
