# Infernum Backend Documentation

Infernum Backend is a robust RESTful API built on the Laravel framework, serving as the backend engine for a digital game store. It includes customer-facing operations, an administrative back-office console, payment gateway integrations, containerized environments, and AWS automated deployment configurations.

---

## Technical Stack

The system is built using the following core technologies:

*   **Language:** PHP 8.4
*   **Framework:** Laravel 11/12
*   **Database:** MySQL 8.0
*   **Administration Panel:** Filament v3
*   **API Documentation:** L5 Swagger (OpenAPI 3.0 annotations)
*   **Payments:** Laravel Cashier (Stripe SDK integration with local Mock fallback)
*   **Authentication:** Laravel Sanctum (Token-based API authentication)
*   **Containerization:** Docker & Docker Compose
*   **Deployment:** AWS CodeDeploy & AWS Systems Manager (SSM) Parameter Store

---

## Directory Structure

The repository is organized as follows:

*   `API/`
    *   `Infernum-API/`: Core Laravel application code.
        *   `app/`: Business logic, models, controllers, and providers.
            *   `Filament/`: Resources and configuration for the admin dashboard.
            *   `Filters/`: Request query filtering logic.
            *   `Http/`: Controllers, custom middleware, and form request validations.
            *   `Models/`: Eloquent database entities.
            *   `Observers/`: Listeners responding to database events.
            *   `Providers/`: Service providers wiring up services and Filament configurations.
        *   `config/`: System configuration files (e.g., Sanctum, Cashier, Swagger).
        *   `database/`: Database migration scripts and database seeders.
        *   `routes/`: Routing tables (`api.php` for API endpoints, `web.php` for web redirects).
    *   `setup/`: Local container and server environment setups.
        *   `Dockerfile.base`: Base Docker image setup (PHP, Apache extensions, Composer, Node).
        *   `Dockerfile.production`: Production Docker build extending the base image.
        *   `docker-compose.dev.yml`: Local Docker environment configuration.
        *   `docker-compose.prod.yml`: Production Docker environment orchestration.
        *   `utils/backend.conf`: Apache virtual host configuration.
*   `scripts/`: Server shell hooks executed by AWS CodeDeploy during deployment.
    *   `before_install.sh`: Setup steps before the deployment bundle is installed.
    *   `after_install.sh`: Post-installation bootstrap script (composer, migrations, seeding, caching, permissions).
*   `appspec.yml`: AWS CodeDeploy deployment instructions.

---

## Database Schema and Data Models

The relational database is designed with standard cascading rules and indexes. The database entities and their properties are detailed below:

### Tables and Columns

1.  **`users`**
    *   `id` (BigInt, Primary Key)
    *   `email` (String, Unique): User email address.
    *   `nickname` (String, Unique): Unique screen name.
    *   `password` (String): Hashed password.
    *   `role` (Enum: `admin`, `client`): Determines access permissions (admin allows dashboard login).
    *   `created_at` (Timestamp): Creation timestamp.
    *   *Note:* The `updated_at` column is disabled in this model (`const UPDATED_AT = null;`).

2.  **`profiles`**
    *   `id` (BigInt, Primary Key)
    *   `display_name` (String, Nullable): User-friendly custom display name.
    *   `bio` (Text, Nullable): Biographical description.
    *   `profile_picture` (String, Nullable): Local path or URL to the profile image.
    *   `user_id` (BigInt, Foreign Key -> `users.id` with cascade onDelete/onUpdate).

3.  **`games`**
    *   `id` (BigInt, Primary Key)
    *   `name` (String, 255): Title of the game.
    *   `short_description` (String, 300): Brief summary.
    *   `long_description` (LongText): Full promotional description.
    *   `price` (Float): Retail base price.
    *   `count_boughts` (Integer): Counter tracking total purchases.
    *   `discounts_id` (BigInt, Foreign Key -> `discounts.id`, Nullable, with cascade onDelete/onUpdate).

4.  **`genres`**
    *   `id` (BigInt, Primary Key)
    *   `type` (String): Genre category name (e.g., Action, RPG).

5.  **`games_genres`** (Pivot Table)
    *   `game_id` (BigInt, Foreign Key -> `games.id`)
    *   `genre_id` (BigInt, Foreign Key -> `genres.id`)

6.  **`discounts`**
    *   `id` (BigInt, Primary Key)
    *   `name` (String): Discount campaign name.
    *   `percentage` (Decimal, 5, 2): Discount rate.
    *   `valid_at` (Timestamp): Active start date.
    *   `expires_at` (Timestamp): Expiration date.

7.  **`requirements`**
    *   `id` (BigInt, Primary Key)
    *   `type` (String): Platform spec type (e.g., minimum or recommended).
    *   `os` (String): Operating system.
    *   `cpu` (String): Processor specifications.
    *   `ram` (String): Memory specifications.
    *   `gpu` (String): Graphics card specifications.
    *   `storage` (String): Required disk space.
    *   `game_id` (BigInt, Foreign Key -> `games.id` with cascade onDelete).

8.  **`image_games`**
    *   `id` (BigInt, Primary Key)
    *   `url` (String): Path or URL of the image asset.
    *   `type` (String): Image context (e.g., portrait, screenshot).
    *   `game_id` (BigInt, Foreign Key -> `games.id` with cascade onDelete).

9.  **`shopping_carts`**
    *   `id` (BigInt, Primary Key)
    *   `user_id` (BigInt, Foreign Key -> `users.id`, Unique). Each user is restricted to a single shopping cart.

10. **`cart_items`**
    *   `id` (BigInt, Primary Key)
    *   `price` (Float): Historical base price at addition time.
    *   `game_id` (BigInt, Foreign Key -> `games.id`).
    *   `shopping_cart_id` (BigInt, Foreign Key -> `shopping_carts.id`).
    *   *Constraint:* Unique index on `['game_id', 'shopping_cart_id']` prevents duplicate game references in a single cart.

11. **`libraries`** (Pivot Table representing owned items)
    *   `id` (BigInt, Primary Key)
    *   `user_id` (BigInt, Foreign Key -> `users.id` with cascade onDelete).
    *   `game_id` (BigInt, Foreign Key -> `games.id` with cascade onDelete).
    *   `created_at` / `updated_at` (Timestamps).

### Model Observers

*   **`UserObserver`:** Hooked to the `User` model. When a user is successfully registered (`created` event), it automatically instantiates an associated profile entry in the database.

---

## API Endpoints and Webhooks

All API endpoints are prefixed with `/api/v1/` and return JSON payloads.

### Authentication Endpoints

*   **`POST /api/v1/register`**
    *   *Description:* Creates a new client user.
    *   *Validation:* Requires a unique `email`, unique `nickname`, and a `password`.
    *   *Response:* Returns the created user object and a Sanctum Bearer token valid for 3 hours.

*   **`POST /api/v1/login`**
    *   *Description:* Authenticates client or admin credentials.
    *   *Validation:* Requires `email` and `password`.
    *   *Response:* Returns the user object, token, and token duration. If the user is an administrator, it generates a temporary signed URL under the property `admin_redirect_url` to log in into the administration dashboard.

*   **`GET /api/v1/user`**
    *   *Description:* Retrieves the current authenticated user profile.
    *   *Security:* Requires a valid Sanctum Bearer token.

### Game Catalog Endpoints

*   **`GET /api/v1/games/all/{pagination?}`**
    *   *Description:* Retrieves a paginated list of games along with their genres, discounts, and images.
    *   *Parameters:* Optional integer for items per page (default is 10).

*   **`GET /api/v1/games/details/{id}`**
    *   *Description:* Retrieves complete metadata for a single game (genres, requirements, active discounts, images).

*   **`GET /api/v1/games/filter`**
    *   *Description:* Query-based search and filter tool.
    *   *Query Parameters:*
        *   `name[like]`: Searches games matching or containing this text string.
        *   `price[gt]`: Filters games with a price greater than this value.
        *   `price[lt]`: Filters games with a price less than this value.
        *   `genre`: Filters games belonging to a specific genre category name.

*   **`GET /api/v1/games/most-bought`**
    *   *Description:* Retrieves the game with the highest transaction rate (where `count_boughts` > 0).

### Profile Endpoints

*   **`GET /api/v1/profile/show`**
    *   *Description:* Retrieves user profile details.
    *   *Security:* Requires Sanctum Bearer token.

*   **`PUT /api/v1/profile/update`**
    *   *Description:* Updates user profile text metadata and profile pictures.
    *   *Validation:* Supports `display_name`, `bio`, and `profile_picture` (valid image format, max 2MB). Profile pictures are uploaded and stored in the `public/profiles_images` storage disk path.
    *   *Security:* Requires Sanctum Bearer token.

### Shopping Cart Endpoints

*   **`GET /api/v1/cart/show`**
    *   *Description:* Retrieves items within the user's cart along with the calculated total.
    *   *Security:* Requires Sanctum Bearer token.

*   **`POST /api/v1/cart/create`**
    *   *Description:* Creates a shopping cart if missing, and adds a game.
    *   *Validation:* Requires `game_id` (must exist). Checks if the game is already in the cart or owned in the library before adding.
    *   *Security:* Requires Sanctum Bearer token.

### Library Endpoints

*   **`GET /api/v1/library/games`**
    *   *Description:* Fetches all games owned by the authenticated user (paginated).
    *   *Security:* Requires Sanctum Bearer token.

### Purchase and Payment Gateway Integration

*   **`POST /api/v1/buy`**
    *   *Description:* Initiates a purchase transaction.
    *   *Validation:* Requires a valid `shoppingCartId` owned by the authenticated user.
    *   *Workflow:*
        1. Validates that the cart is not empty and belongs to the user.
        2. Applies any active discounts to the games dynamically.
        3. If `STRIPE_SECRET` is not set or contains dummy characters, it triggers a **Mock Checkout**. This mock mode immediately adds the games to the user's library, empties and deletes the cart, and redirects to the success page.
        4. If `STRIPE_SECRET` is valid, it uses Laravel Cashier to create a Stripe Checkout Session with product metadata and redirects the user to the Stripe Checkout page.

*   **`POST /api/v1/stripe/webhook`**
    *   *Description:* Webhook callback endpoint used by Stripe.
    *   *Workflow:* Listens for `checkout.session.completed` events. It parses the session metadata, retrieves the `user_id`, adds the corresponding cart games into the user's library, and clears/deletes the user's shopping cart.

---

## Custom Middleware

*   **`RefreshTokenMiddleware`**
    *   *Purpose:* Handles rolling Sanctum session tokens.
    *   *Mechanism:* For every authenticated request passing through this middleware, it checks if the token has expired.
        *   If the token has expired, it automatically deletes it and returns a `401 Token expired` response.
        *   If the token is still active, it automatically updates the token's expiration date (`expires_at`) to exactly **3 hours** from the current timestamp. This implements sliding session expiration.

---

## Filament Admin Panel

The administration backend dashboard is located at `/administratorPanel`.

*   **Access Control:** Managed via the `canAccessPanel()` method in the `User` model, restricting access only to users with the `role === 'admin'`.
*   **Decoupled Architecture:** To keep resources maintainable, form configurations and table columns are organized into dedicated files (e.g. `GameForm` under the `Schemas/` subdirectory, and `GamesTable` under the `Tables/` subdirectory) rather than nesting form/table schemas directly inside the resource definition class.
*   **Auto-Login Link:** Administrators logging in through the API receive a temporary, cryptographically signed URL (`/administratorPanel/autologin`) valid for 2 minutes. Browsing this link logs the user into the admin panel session guard directly and redirects them to the admin dashboard.
*   **Custom Logout Redirect:** The logout action is customized using a custom `LogoutResponse` class. Logging out of the panel redirects users back to the client application homepage (either `http://localhost:4220` or the production client URL specified by `FRONTEND_URL` in the environment variables).

---

## Setup and Development

### Running Locally with Docker

Local development requirements are packaged using Docker Compose:

1.  Create your local `.env` configuration inside `API/Infernum-API/` by copying `.env.example`.
2.  Navigate to `API/setup/`.
3.  Boot the services:
    ```bash
    docker compose -f docker-compose.dev.yml up -d --build
    ```
4.  Once running, execute migrations and seeds inside the application container:
    ```bash
    docker compose -f docker-compose.dev.yml exec app composer install
    docker compose -f docker-compose.dev.yml exec app npm install
    docker compose -f docker-compose.dev.yml exec app php artisan migrate --seed
    ```

---

## Production Deployment with AWS CodeDeploy

The backend is configured for automated deployments to Linux servers via AWS CodeDeploy using the `appspec.yml` file.

### Hooks Lifecycle

1.  **`BeforeInstall` (runs `scripts/before_install.sh`)**
    *   Fetches MySQL credentials from the AWS SSM Parameter Store (`/infernum/MYSQL_ROOT_PASSWORD`, `/infernum/MYSQL_USER`, `/infernum/MYSQL_PASSWORD`, `/infernum/MYSQL_DATABASE`) using the AWS CLI.
    *   Cleans out any files in the deployment target folder (`/var/www/html/public/Infernum-API`).
    *   Generates a local `.env` setup helper inside the deployment staging workspace.

2.  **`AfterInstall` (runs `scripts/after_install.sh`)**
    *   Fetches the production Laravel Application Key from AWS SSM (`/backend/app-key`).
    *   Builds the base Docker image `base_image` from `setup/Dockerfile.base`.
    *   Launches production containers in detached mode using `setup/docker-compose.prod.yml`.
    *   Runs production commands inside the container:
        *   Runs production optimized composer installation (`composer install --no-dev --optimize-autoloader`).
        *   Installs node modules.
        *   Decrypts the production configuration (`php artisan env:decrypt --env=production --key="{key}"`).
        *   Clears configs and cache.
        *   Waits for the MySQL database container to report healthy.
        *   Runs database migrations forcing changes (`php artisan migrate --force`).
        *   Seeds data (`php artisan db:seed --force`) only if the `users` table is completely empty.
        *   Caches configurations and routing maps (`config:cache`, `route:cache`).
        *   Corrects filesystem permissions for storage and bootstrap directories under the `www-data` user.
        *   Enables Docker on system boot and reloads the Apache web server inside the container.
