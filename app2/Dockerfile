FROM python:3.10

RUN pip install mysql-connector-python
RUN pip install flask flask-cors


WORKDIR /app

COPY . .

USER root
EXPOSE 5000

RUN chmod +x ./entrypoint.sh
ENTRYPOINT ["bash", "./entrypoint.sh"]