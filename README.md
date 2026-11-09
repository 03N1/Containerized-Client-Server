# Dockerized Client-Server Application

## 1. Project Overview

This project demonstrates the design and implementation of a containerized client-server application using Docker, PHP, Apache, MySQL and Mailpit.

The application provides user authentication, contact registration, contact searching and password recovery functionality.

It was developed as part of a Distributed Systems practical.

## 2. Technologies Used

- **PHP 8.2** – Server-side application development.
- **Apache** – Web server.
- **MySQL 8.0** – Database management.
- **Docker** – Containerization.
- **Docker Compose** – Management of multiple containers.
- **Mailpit** – Capturing and testing password recovery emails.
- **HTML** – Web forms and user interface.

## 3. System Architecture

The application consists of three Docker services:

| Service | Technology | Purpose |
|---|---|---|
| Web | PHP 8.2 + Apache | Handles application requests |
| Database | MySQL 8.0 | Stores users and contact records |
| Mail | Mailpit | Captures test emails |

## 4. Application Features

- User registration.
- Secure user login and logout.
- Password hashing and verification.
- Forgot-password functionality.
- Password reset using a generated token.
- Contact registration.
- Contact searching using registration numbers.
- Database storage using MySQL.
- Email testing using Mailpit.

## 5. Project Structure

- `Dockerfile` – Defines the PHP and Apache application container.
- `docker-compose.yml` – Defines and manages the application services.
- `init.sql` – Initializes the MySQL database and tables.
- `index.php` – User login page.
- `register.php` – User registration.
- `dashboard.php` – Contact registration interface.
- `search.php` – Contact searching.
- `forgot-password.php` – Password recovery request.
- `reset-password.php` – Password reset.
- `logout.php` – User logout.
- `.gitignore` – Specifies files Git should ignore.

## 6. Requirements

To run this project locally, you need:

- Windows, Linux or macOS.
- Docker Desktop with Docker Compose.
- A web browser.

## 7. Installation and Execution

### Step 1: Clone the repository

```bash
git clone https://github.com/03N1/Containerized-Client-Server.git
cd Containerized-Client-Server
```

### Step 2: Configure environment variables

Create a `.env` file in the project root directory.

Add the following configuration:

```env
DB_HOST=db
DB_NAME=contact_db
DB_USER=root
DB_PASSWORD=secret_pass
MYSQL_ROOT_PASSWORD=secret_pass
```

These are demonstration credentials for local testing. Use stronger credentials for any real deployment.

### Step 3: Start the containers

Run:

```bash
docker compose up -d
```

Docker Compose builds and starts the application services.

### Step 4: Access the application

Open the following addresses in your browser:

- Application: http://localhost:8080
- Mailpit: http://localhost:8025

Register an account through the registration page before logging in.

### Step 5: Stop the application

To stop the containers, run:

```bash
docker compose down
```

The named database volume is preserved when using this command.

## 8. Testing

The following functionality was tested:

- User registration and login.
- Contact registration and storage.
- Searching for registered contacts.
- User logout and session protection.
- Password recovery email capture through Mailpit.
- Password reset and subsequent login using the new password.

## 9. Conclusion

This practical demonstrates how Docker can be used to containerize a client-server application and manage multiple interconnected services.

It also demonstrates database integration, user authentication, email testing and communication between application containers.

---

**Project Repository:**  
https://github.com/03N1/Containerized-Client-Server
