# Use Maven + JDK 17 image to build and run in one container
FROM maven:3.9.6-eclipse-temurin-17

WORKDIR /app

# Copy all files to container
COPY . .

# Build the app (skip tests to speed up)
RUN mvn clean package -DskipTests

# Expose port
EXPOSE 9090

# Run the built JAR
CMD ["java", "-jar", "target/spring-kafka-consumer-0.0.1-SNAPSHOT.jar"]