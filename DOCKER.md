# PulseBoard - Docker Setup Guide

PulseBoard is a Laravel-based API service for tracking deployment readiness of internal projects. This guide explains how to run the application using Docker.

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) (version 20.10 or later)
- [Docker Compose](https://docs.docker.com/compose/install/) (version 2.0 or later)
- At least 2GB of available RAM
- At least 5GB of available disk space

## Quick Start

1. **Clone or navigate to the project directory**
   ```bash
   cd /path/to/plusBoard
   ```

2. **Start the application**
   ```bash
   docker compose up --build
   ```

3. **Run database migrations** (in a new terminal)
   ```bash
   docker compose exec app php artisan migrate --force
   ```

4. **Access the application**
   - API: http://127.0.0.1:8000
   - MySQL: localhost:3306

## Detailed Setup

### 1. Environment Configuration

The Docker setup uses the following environment variables (defined in `docker-compose.yml`):

```yaml
APP_ENV: local
APP_DEBUG: 'true'
DB_CONNECTION: mysql
DB_HOST: db
DB_PORT: '3306'
DB_DATABASE: plusboard
DB_USERNAME: root
DB_PASSWORD: secret
```

### 2. Services

#### App Service (`app`)
- **Image**: Custom PHP 8.2 CLI with Laravel
- **Port**: 8000
- **Volume**: `./:/var/www/html` (source code)
- **Dependencies**: MySQL database

#### Database Service (`db`)
- **Image**: MySQL 8.0
- **Port**: 3306
- **Volume**: `db_data:/var/lib/mysql` (persistent data)
- **Database**: plusboard
- **Credentials**: root/secret

### 3. First-Time Setup

```bash
# Build and start containers
docker compose up --build -d

# Run database migrations
docker compose exec app php artisan migrate --force

# (Optional) Seed the database
docker compose exec app php artisan db:seed

# (Optional) Run tests
docker compose exec app php artisan test
```

## API Endpoints

Once running, you can test the API endpoints:

### Create a Project
```bash
curl -X POST http://127.0.0.1:8000/api/projects \
  -H "Content-Type: application/json" \
  -d '{"name":"My Project","owner_email":"owner@example.com"}'
```

### Add Deployment Check
```bash
curl -X POST http://127.0.0.1:8000/api/projects/1/checks \
  -H "Content-Type: application/json" \
  -d '{"title":"Database migration reviewed"}'
```

### Complete a Check
```bash
curl -X PATCH http://127.0.0.1:8000/api/checks/1/complete
```

### Get Project Readiness
```bash
curl http://127.0.0.1:8000/api/projects/1/readiness
```

### List All Projects
```bash
curl http://127.0.0.1:8000/api/projects
```

## Development Workflow

### Running Commands in Container

```bash
# Access the app container
docker compose exec app bash

# Run Laravel commands
docker compose exec app php artisan migrate:status
docker compose exec app php artisan route:list
docker compose exec app php artisan test

# Install PHP dependencies
docker compose exec app composer install

# Clear caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
```

### Working with Database

```bash
# Access MySQL directly
docker compose exec db mysql -u root -p plusboard
# Password: secret

# Create database backup
docker compose exec db mysqldump -u root -p plusboard > backup.sql

# Restore database
docker compose exec -T db mysql -u root -p plusboard < backup.sql
```

### Logs and Debugging

```bash
# View app logs
docker compose logs app

# View database logs
docker compose logs db

# Follow logs in real-time
docker compose logs -f app

# View Laravel logs
docker compose exec app tail -f storage/logs/laravel.log
```

## Testing

### Run Feature Tests
```bash
docker compose exec app php artisan test --testsuite=Feature
```

### Run Specific Test
```bash
docker compose exec app php artisan test --filter=ProjectReadinessTest
```

### Run All Tests
```bash
docker compose exec app php artisan test
```

## Troubleshooting

### Common Issues

#### 1. Port Already in Use
```bash
# Check what's using port 8000
netstat -tulpn | grep :8000

# Or use different ports in docker-compose.yml
ports:
  - '8001:8000'  # Host:Container
```

#### 2. Database Connection Issues
```bash
# Check if MySQL is running
docker compose ps db

# Restart database service
docker compose restart db

# Check MySQL logs
docker compose logs db
```

#### 3. Permission Issues
```bash
# Fix storage permissions
docker compose exec app chown -R www-data:www-data storage
docker compose exec app chmod -R 755 storage
```

#### 4. Out of Memory
```bash
# Increase Docker memory limit in Docker Desktop settings
# Or run with limited services
docker compose up app  # Only start app service
```

### Reset Everything

```bash
# Stop and remove containers
docker compose down

# Remove volumes (WARNING: deletes database data)
docker compose down -v

# Remove images
docker compose down --rmi all

# Clean rebuild
docker compose up --build --force-recreate
```

## Production Considerations

For production deployment:

1. **Use production Docker image** with optimizations
2. **Configure proper environment variables**
3. **Set up SSL/TLS certificates**
4. **Configure reverse proxy (nginx)**
5. **Set up database backups**
6. **Configure logging and monitoring**
7. **Use Docker secrets for sensitive data**

## File Structure

```
plusBoard/
├── Dockerfile              # PHP application container
├── docker-compose.yml      # Multi-service configuration
├── .dockerignore          # Files to exclude from build
├── app/                   # Laravel application code
├── database/              # Migrations and seeds
├── routes/                # API routes
├── tests/                 # Test files
└── README.md              # General documentation
```

## Support

If you encounter issues:

1. Check the logs: `docker compose logs`
2. Verify Docker and Docker Compose versions
3. Ensure ports 8000 and 3306 are available
4. Check that you have sufficient resources (RAM/disk)
5. Review the Laravel logs: `docker compose exec app tail storage/logs/laravel.log`