# MaplePHP with Docker

MaplePHP Docker provides a fully functional environment including a web server, MySQL database, and Composer integration.

## Installation Steps

### 1. Prepare Docker Files
Move the `Dockerfile` and `docker-compose.yml` files into the MaplePHP root directory for integration.

### 2. Build and Launch
In your terminal, navigate to the MaplePHP root directory where the Docker files were placed. Execute the command below to build and launch your Docker containers:

```sh
docker-compose up -d
```

This command installs and starts Docker, setting up your environment.

### 3. Accessing Your Application
Open your web browser and go to **http://localhost/public/**. You should now be able to see your application running.

## MySQL Configuration
Below are the default settings for the MySQL service provided in the Docker setup:

- **Host:** `host.docker.internal`
- **Database Name:** `sandbox`
- **Username:** `root`
- **Password:** `root`
- **Port:** `3306`

## Managing Docker Services
Docker-Compose facilitates easy management of your containers. You can utilize the following commands as needed:

- To stop your containers:
  
  ```sh
  docker-compose down
  ```
  
- To start your containers again:
  
  ```sh
  docker-compose up -d
  ```
  
- To rebuild your services after making any changes:
  
  ```sh
  docker-compose up -d --build
  ```

This setup streamlines the development and deployment process for MaplePHP applications using Docker.