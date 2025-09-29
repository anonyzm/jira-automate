#!/bin/bash

# Ждем пока Kafka будет готов
echo "Waiting for Kafka to be ready..."
while ! kafka-topics --bootstrap-server kafka:29092 --list > /dev/null 2>&1; do
  echo "Kafka is not ready yet, waiting..."
  sleep 5
done

echo "Kafka is ready, creating topics..."

# Создаем топики
kafka-topics --bootstrap-server kafka:29092 --create --topic jira.internal.event.1 --partitions 3 --replication-factor 1 --if-not-exists
kafka-topics --bootstrap-server kafka:29092 --create --topic jira.internal.ai.1 --partitions 3 --replication-factor 1 --if-not-exists
kafka-topics --bootstrap-server kafka:29092 --create --topic jira.internal.report.1 --partitions 3 --replication-factor 1 --if-not-exists

echo "Topics created successfully!"

# Показываем созданные топики
echo "Created topics:"
kafka-topics --bootstrap-server kafka:29092 --list
