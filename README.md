<!-- Configure N8N -->

## N8N Installation and Configuration

### Step 1: Install N8N

#### Using npm (Global Installation)

```bash
npm install n8n -g
```

#### Using Docker

```bash
docker run -it --rm --name n8n -p 5678:5678 n8nio/n8n
```

#### Using Docker Compose

Create a `docker-compose.yml` file:

```yaml
version: "3.8"
services:
    n8n:
        image: n8nio/n8n
        ports:
            - "5678:5678"
        environment:
            - N8N_BASIC_AUTH_ACTIVE=true
            - N8N_BASIC_AUTH_USER=admin
            - N8N_BASIC_AUTH_PASSWORD=password
        volumes:
            - n8n_data:/home/node/.n8n
volumes:
    n8n_data:
```

### Step 2: Start N8N

```bash
# If installed via npm
n8n start

# If using Docker Compose
docker-compose up -d
```

### Step 3: Access N8N Web Interface

Open your browser and navigate to: `http://localhost:5678`

### Step 4: Copy N8N Workflow Code

1. **Export from Source N8N Instance:**

    - Open your existing N8N workflow
    - Click on the workflow settings (three dots menu)
    - Select "Download" or "Export"
    - Save the `.json` file

2. **Import to New N8N Instance:**
    - In your new N8N instance, click "Import from File"
    - Select your downloaded `.json` workflow file
    - Click "Import"

### Step 5: Configure N8N Account and Credentials

#### Set Up User Account

1. On first access, create your admin account
2. Set username and password
3. Configure email settings (optional)

#### Configure Node Credentials

For each node requiring authentication:

1. **API Credentials:**

    - Click on the node requiring credentials
    - Click "Create New Credential"
    - Enter API keys, tokens, or connection details
    - Test the connection
    - Save credentials

2. **Database Connections:**

    - Configure host, port, username, password
    - Test connection before saving

3. **Webhook URLs:**
    - Update webhook URLs to match your new N8N instance
    - Format: `http://your-domain:5678/webhook/your-webhook-name`

### Step 6: Environment Variables (Optional)

Create a `.env` file for configuration:

```bash
N8N_BASIC_AUTH_ACTIVE=true
N8N_BASIC_AUTH_USER=your-username
N8N_BASIC_AUTH_PASSWORD=your-password
N8N_HOST=0.0.0.0
N8N_PORT=5678
N8N_PROTOCOL=http
WEBHOOK_URL=http://your-domain:5678
```

### Step 7: Test Your Workflows

1. Activate imported workflows
2. Test each workflow manually
3. Verify all credentials are working

4. Check webhook endpoints are accessible
 <p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
