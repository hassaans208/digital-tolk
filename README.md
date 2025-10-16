## Translation Management Service - Code Test

### Objective
Build a scalable, secure, and high-performance Translation Management API to demonstrate proficiency in clean code, Laravel best practices, and robust backend design. This assignment evaluates your ability to design an efficient API with a focus on scalability, performance (<200ms response time), and extensibility.

### Test Overview
Develop a backend service to manage translations across multiple locales (e.g., en, fr, es), with flexible support for new languages. Each translation can be tagged by context (mobile, desktop, web) and must be easily searchable by tag, key, or content.

### Quick Setup

> **Prerequisite:**  
> Ensure [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/) are installed on your machine.  
> You will also need [Git](https://git-scm.com/) installed.

1. **Clone the project:**

   ```bash
   git clone https://github.com/hassaans208/digital-tolk.git
   cd digital-tolk
   ```

2. **Copy Environment File**

    Copy the example environment file:
     ```bash
     cp .env.example .env
     ```

3. **Build and start the Docker containers:**

   ```bash
   docker compose -f compose.prod.yaml up -d
   ```

4. **Wait Until PHP-FPM is ready:**

   Once you see following script in fpm logs, you will know it that the app is ready:

   ```
   Setting up storage directories...
   Waiting for database connection...
   Database is ready!
   Running database migrations...

   Dropping all tables ................................................ 1s DONE
   INFO  Preparing database.  
   Creating migration table ...................................... 71.90ms DONE
   INFO  Running migrations.  
   0001_01_01_000000_create_users_table ......................... 238.79ms DONE
   0001_01_01_000001_create_cache_table .......................... 89.77ms DONE
   0001_01_01_000002_create_jobs_table .......................... 205.25ms DONE
   2025_10_15_112724_create_tags_table ........................... 96.86ms DONE
   2025_10_15_112800_create_languages_table ..................... 142.78ms DONE
   2025_10_15_112907_create_translations_table ............. -167,765.56ms DONE
   2025_10_15_123352_create_personal_access_tokens_table .......... 2m 48s DONE
   2025_10_15_130100_create_translation_tag_table ............... 344.93ms DONE

   INFO  Seeding database.  

   Database\Seeders\TranslationSeeder ................................. RUNNING  
   Translation seeding done.
   Database\Seeders\TranslationSeeder .......................... 34,823 ms DONE  

   Migrations completed successfully
   Laravel application setup completed!
   [15-Oct-2025 23:25:01] NOTICE: fpm is running, pid 1
   [15-Oct-2025 23:25:01] NOTICE: ready to handle connections
   ```

5. **Discover Swagger Documentation:**

   [Swagger Documentation (Click here)](http://localhost/docs)


6. **Postman Testing:**

    To test API endpoints in Postman, import the Postman collection located at:

    ```
    storage/app/private/scribe/collection.json
    ```

    In Postman:  
    - Open Postman and choose `Import` > `File`
    - Select the file above to load all API requests.

6. **Run Tests:**

   Run tests using following command:

    ```
    docker exec digital-tolk-php-cli-1 php artisan test tests/Feature/TranslationExportTest.php
    ```

   Incase you encounter issue with running tests, please use following command:
    ```
    docker exec digital-tolk-php-fpm-1 composer install
    ```

   In Postman:  
    - Open Postman and choose `Import` > `File`
    - Select the file above to load all API requests.

7. **Shutting down the stack:**

    When done, you can stop the containers with:

    ```bash
    docker compose -f compose.prod.yaml down
    ```

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