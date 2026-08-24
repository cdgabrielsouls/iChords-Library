# Deploying iChords Library to Vercel

## 1. Push the project

Push this repository to GitHub, GitLab, or Bitbucket. Do not commit `.env`.

## 2. Import into Vercel

Create a new Vercel project and import the repository. The included `vercel.json` configures Laravel's PHP entry point.

## 3. Add environment variables

Add these in Vercel Project Settings > Environment Variables for Production:

```text
APP_NAME=iChords Library
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.vercel.app
APP_KEY=your-generated-laravel-key

DB_CONNECTION=pgsql
DB_HOST=your-neon-host
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=your-neon-password
DB_SSLMODE=require

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Generate `APP_KEY` locally with `php artisan key:generate --show`. Never commit the key or database password.

## 4. Deploy

Vercel will run the PHP runtime and serve the Vite build. Run migrations once from a trusted machine using the same production database:

```powershell
php artisan migrate --force
```

Do not run migrations automatically on every request.

## Important

Vercel's filesystem is temporary. This app uses database-backed sessions and cache, so it does not depend on local file persistence. If file uploads are added later, use durable object storage.
