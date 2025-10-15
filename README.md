## Translation Management Service - Code Test

### Objective
Build a scalable, secure, and high-performance Translation Management API to demonstrate proficiency in clean code, Laravel best practices, and robust backend design. This assignment evaluates your ability to design an efficient API with a focus on scalability, performance (<200ms response time), and extensibility.

### Test Overview
Develop a backend service to manage translations across multiple locales (e.g., en, fr, es), with flexible support for new languages. Each translation can be tagged by context (mobile, desktop, web) and must be easily searchable by tag, key, or content.

### Setup

> **Prerequisite:**  
> Ensure [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/) are installed on your machine.  
> You will also need [Git](https://git-scm.com/) installed.

1. **Clone the project:**

   ```bash
   git clone https://github.com/hassaans208/digital-tolk.git
   cd digital-tolk
   ```

2. **Configure environment variables:**

   - Copy the example environment file:
     ```bash
     cp .env.example .env
     ```
   - (Optional) Edit `.env` and set your required configuration values if needed.

3. **Build and start the Docker containers:**

   ```bash
   docker compose -f compose.prod.yaml up --build -d
   ```

4. **Install Composer dependencies:**

   ```bash
   docker compose exec app composer install
   ```

5. **Generate application key:**

   ```bash
   docker compose exec app php artisan key:generate
   ```

6. **Run migrations and seeders:**

   ```bash
   docker compose exec app php artisan migrate --seed
   ```

   This will prepare your database with required tables and optional sample data.

7. **(Optional) Generate test records for scalability testing:**

   ```bash
   docker compose exec app php artisan db:seed --class=TranslationSeeder
   ```

   *(Only if you wish to generate 100k+ records; see documentation for custom seeders or factories.)*

8. **Build the Swagger (Scribe) API documentation:**

   Build the API docs using the knuckleswtf/scribe library:

   ```bash
   docker compose exec app php artisan scribe:generate
   ```

9. **Access the application:**

   - **API base URL:**  
     [http://localhost](http://localhost:8000)
   - **API Documentation:**  
     [http://localhost/docs](http://localhost:8000/docs)

10. **Postman Testing:**

    To test API endpoints in Postman, import the Postman collection located at:

    ```
    storage/app/private/scribe/collection.json
    ```

    In Postman:  
    - Open Postman and choose `Import` > `File`
    - Select the file above to load all API requests.

11. **Shutting down the stack:**

    When done, you can stop the containers with:

    ```bash
    docker compose -f compose.prod.yaml down
    ```

**If you encounter permission issues (Linux/Mac), you may need to fix file or storage permissions:**

```bash
docker compose exec app chmod -R ug+w storage bootstrap/cache
```


- Documentation URL

Access documentation [http://localhost/docs]

- 

### Requirements Summary

#### Core Features
- **Store translations** for any number of locales, with ability to add new ones.
- **Support contextual tags** (e.g., mobile, desktop, web) for translations.
- **API Endpoints** for creating, updating, viewing, and searching translations.
- **JSON Export Endpoint** that always returns the latest translations for frontend integration (e.g., Vue.js).
- **Performance:** All endpoints must respond within 200ms; JSON export must handle 100k+ records in <500ms.
- **Bulk Data Support:** Provide a command/factory to generate 100k+ test records for scalability testing.

#### Technical Guidelines
- **Code Standards:** Follow PSR-12 and SOLID principles.
- **Database:** Use a scalable schema suited for large datasets.
- **Testing:** Ensure >95% test coverage across unit, feature, and performance tests.
- **Security:** Implement token-based authentication.
- **No CRUD Libraries:** All CRUD and translation logic must be implemented from scratch—no external abstractions.
- **Documentation:** Provide OpenAPI/Swagger docs and setup instructions.
- **Dockerized Environment** for development and deployment.
- **CDN Support:** Design endpoints with CDN compatibility in mind.

### Evaluation Criteria
- **Code Quality & PSR-12 Compliance (20%)**
- **Scalability & Performance (25%)**
- **API Design & Functionality (20%)**
- **Security Best Practices (20%)**
- **Testing & Coverage (15%)**

### Deliverables
- Complete API source code and database schema.
- Docker configurations for easy setup.
- Swagger/OpenAPI documentation.
- Test suite with >95% coverage.
- README with setup instructions and a brief explanation of design decisions.
- GitHub repository link.

*This project is a test assignment. Target completion is 2 hours, but feel free to use additional time to deliver a robust solution.*