from concurrent import futures
import grpc
import mysql.connector
import sports_pb2
import sports_pb2_grpc
import os

class SportsService(sports_pb2_grpc.SportsServiceServicer):
    def __init__(self):
        self.db_config = {
            "host": os.getenv("DB_HOST", "database"),
            "user": os.getenv("DB_USER", "root"),
            "password": os.getenv("DB_PASSWORD", "root"),
            "database": os.getenv("DB_NAME", "Sport_db")
        }

    def get_db_connection(self):
        return mysql.connector.connect(**self.db_config)

    def GetSports(self, request, context):
        try:
            conn = self.get_db_connection()
            cursor = conn.cursor()
            cursor.execute("SELECT naam, locatie, eenheid, stemmen FROM sporten")
            rows = cursor.fetchall()
            conn.close()
            
            sports = [sports_pb2.Sport(name=row[0], location=row[1], unit=row[2], votes=row[3]) for row in rows]
            return sports_pb2.GetSportsResponse(sports=sports)
        except mysql.connector.Error as err:
            context.set_details(str(err))
            context.set_code(grpc.StatusCode.INTERNAL)
            return sports_pb2.GetSportsResponse()

def serve():
    server = grpc.server(futures.ThreadPoolExecutor(max_workers=10))
    sports_pb2_grpc.add_SportsServiceServicer_to_server(SportsService(), server)
    server.add_insecure_port('[::]:50051')
    server.start()
    print("Server started on port 50051")
    server.wait_for_termination()

if __name__ == "__main__":
    serve()
