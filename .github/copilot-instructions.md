# Statusphere Laravel Edition - Onboarding Guide

## Overview

**Statusphere** is a sample Laravel application that demonstrates integration with the AT Protocol (the decentralized social networking protocol that powers Bluesky). This project serves as a reference implementation specifically designed for PHP developers who want to build applications that interact with decentralized social networks using Laravel and the [laravel-bluesky package](https://github.com/invokable/laravel-bluesky).

**Live Demo:** [https://statusphere.puklipo.com/](https://statusphere.puklipo.com/)

**Primary Users:**
- PHP developers learning AT Protocol integration
- Laravel developers exploring decentralized social networking
- Engineers building AT Protocol-compatible applications

**What it enables:**
- OAuth authentication with Bluesky accounts using Laravel Socialite
- Publishing emoji status updates to the AT Protocol network
- Retrieving and displaying user profiles and status histories
- Reference patterns for event processing from the AT Protocol (optional advanced feature)

The application prioritizes simplicity and follows Laravel best practices, making it accessible to PHP developers without requiring deep knowledge of decentralized protocols or complex JavaScript frameworks. It intentionally avoids WebSocket infrastructure to maintain accessibility for developers unfamiliar with process supervision.

**Technology Stack:** Laravel 12.x, Livewire 3.x/Volt 1.6.x, Tailwind CSS 4.x, Revolution Laravel-Bluesky 1.x

## Quick Start for Contributors

To get started with development:

```bash
git clone https://github.com/invokable/statusphere.git
cd statusphere
cp .env.example .env
composer install
composer run post-create-project-cmd
npm install
npm run build
```

**For local testing:** Configure Bluesky app password in `.env`:
```
BLUESKY_IDENTIFIER=your.handle.bsky.social
BLUESKY_APP_PASSWORD=your-app-password
```

**Development commands:**
- `composer run dev` - Start all development services (server, queue, logs, vite)
- `php artisan bsky:create-status` - Test CLI status posting
- `vendor/bin/phpunit` - Run tests
- `vendor/bin/pint --test` - Check code style

## Project Organization

### Core Systems and Services

**Authentication System**
- `routes/web.php` - OAuth flow with Bluesky using Laravel Socialite
- `app/Models/User.php` - User model with AT Protocol integration
- Session management for OAuth tokens

**Status Management**
- `app/Record/Status.php` - AT Protocol record definition for emoji statuses
- `resources/views/livewire/create-status.blade.php` - Livewire component for status creation
- `app/Console/Commands/CreateStatusCommand.php` - CLI command for status posting with app password authentication

**User Interface**
- `resources/views/livewire/home.blade.php` - Authenticated user home page
- `resources/views/welcome.blade.php` - Public landing page with user timeline
- `resources/views/components/emoji.blade.php` - Emoji display component
- `resources/views/components/status-desc.blade.php` - Status description component
- `resources/views/components/layouts/app.blade.php` - Main layout template

**Event Processing (Reference Implementation)**
- `app/Listeners/StatusListener.php` - Event listener for AT Protocol commits (WebSocket integration reference)
- Optional Bluesky Firehose/Jetstream integration via laravel-bluesky package

### Main Files and Directories

```
├── app/
│   ├── Console/Commands/CreateStatusCommand.php    # CLI status posting
│   ├── Listeners/StatusListener.php                # AT Protocol event processing (reference)
│   ├── Models/User.php                             # User model with Bluesky integration
│   └── Record/Status.php                           # AT Protocol status record definition
├── resources/views/
│   ├── livewire/
│   │   ├── create-status.blade.php                 # Status creation component
│   │   └── home.blade.php                          # User home page
│   ├── components/
│   │   ├── emoji.blade.php                         # Emoji display component
│   │   ├── status-desc.blade.php                   # Status description component
│   │   └── layouts/app.blade.php                   # Main layout template
│   └── welcome.blade.php                           # Public landing page
├── config/statusphere.php                          # Emoji configuration
├── routes/web.php                                  # OAuth and main routes
├── .github/workflows/                              # CI/CD pipelines
│   ├── tests.yml                                   # Automated testing
│   ├── lint.yml                                    # Code style enforcement
│   ├── update.yml                                  # Dependency updates
│   └── copilot-setup-steps.yml                    # GitHub Copilot setup
└── .env.example                                    # Environment configuration template
```

### Development Infrastructure

**Frontend Build System**
- `vite.config.js` - Asset bundling with Laravel Vite plugin
- `package.json` - Node.js dependencies (Vite 6.x, Tailwind CSS 4.x, Axios, Concurrently)
- Tailwind CSS 4.x configured via Vite plugin (@tailwindcss/vite)

**Testing and Quality**
- `phpunit.xml` - PHPUnit test configuration
- `tests/` - Feature and unit tests (Command, Event, Example tests)
- `.github/workflows/tests.yml` - Automated testing pipeline
- `.github/workflows/lint.yml` - Code style enforcement with Laravel Pint
- `pint.json` - Laravel Pint configuration for code styling

**Development Environment**
- `composer.json` - PHP dependencies and development scripts
- `.env.example` - Environment configuration template with Bluesky settings
- Laravel Sail for Docker-based development (via `laravel/sail` package)
- Development command: `composer run dev` for concurrent processes (server, queue, logs, vite)

**Additional CI/CD**
- `.github/workflows/update.yml` - Automated dependency updates
- `.github/workflows/copilot-setup-steps.yml` - GitHub Copilot setup automation

### Key Classes and Functions

**Core Models**
- `App\Models\User` - User model with `status()` accessor that fetches latest AT Protocol status (cached)
- `App\Record\Status` - Implements `Recordable` interface for AT Protocol records with NSID `com.puklipo.statusphere.status`

**Livewire Components (using Volt syntax)**
- `create-status.blade.php::submit(string $emoji)` - Validates emoji against config, publishes status via OAuth session
- `home.blade.php::mount()` - Initializes OAuth session, loads user profile and status history (last 20 records)
- `home.blade.php::logout()` - Clears session and redirects to welcome page

**Console Commands**
- `CreateStatusCommand::handle()` - CLI status posting using app password authentication (for local development)
- Command signature: `bsky:create-status` - Posts random emoji from config

**Event Processing (Reference Implementation)**
- `StatusListener::handle(JetstreamCommitMessage $event)` - Processes AT Protocol commit events for WebSocket integration
- Filters for 'create' operations on Status NSID collection

## Glossary of Codebase-Specific Terms

**AT Protocol Integration**
- **NSID** - Namespace Identifier: `com.puklipo.statusphere.status` for status records
- **TID** - Time-sortable ID generator for unique AT Protocol record keys (`Revolution\Bluesky\Core\TID`)
- **OAuthSession** - Bluesky authentication session wrapper (`Revolution\Bluesky\Session\OAuthSession`)
- **putRecord** - AT Protocol method for publishing records to user repositories
- **listRecords** - AT Protocol method for retrieving records from repositories (with limit and pagination)
- **assertDid** - Method to get user's Decentralized Identifier from Bluesky session
- **AT Protocol Guidelines** - [Official documentation](https://atproto.com/guides/applications) followed by this implementation

**Application-Specific Classes**
- **Status** - Custom AT Protocol record class (`app/Record/Status.php`) with required fields: status, createdAt
- **StatusListener** - Event handler for AT Protocol commit messages (`app/Listeners/StatusListener.php`) for WebSocket reference
- **CreateStatusCommand** - Artisan command for CLI status posting (`app/Console/Commands/CreateStatusCommand.php`)
- **statusphere.status** - Configuration array of allowed emoji statuses (`config/statusphere.php`) - 28 emoji options

**Livewire Components**
- **create-status** - Livewire component for emoji status creation (`resources/views/livewire/create-status.blade.php`)
- **home** - User dashboard Livewire component (`resources/views/livewire/home.blade.php`)
- **myStatus** - Livewire state property holding current user status
- **status-created** - Livewire event fired when new status is successfully posted

**Bluesky Integration**
- **bluesky_session** - Laravel session key storing OAuth session data
- **laravel-bluesky** - Third-party package providing AT Protocol integration ([revolution/laravel-bluesky](https://github.com/invokable/laravel-bluesky))
- **Bluesky** - Laravel facade for AT Protocol operations (`Revolution\Bluesky\Facades\Bluesky`)
- **bsky:create-status** - Artisan command signature for CLI status posting
- **JetstreamCommitMessage** - Event class for AT Protocol commit notifications
- **OAuth flow** - Handled via routes: `/login` (initiate) and `/callback` (process) in `routes/web.php`

**Authentication & Users**
- **did** - Decentralized Identifier for users on AT Protocol network
- **handle** - User's human-readable identifier (e.g., `alice.bsky.social`)
- **issuer** - OAuth issuer URL, typically `https://bsky.social`
- **refresh_token** - OAuth token for session renewal

**Development Tools**
- **Volt** - Livewire syntax extension for single-file components (version 1.6.x)
- **Pint** - Laravel's opinionated PHP code style fixer (version 1.13+)
- **Sail** - Laravel's Docker development environment (version 1.26+)
- **Concurrently** - NPM package for running multiple development processes (version 9.0+)
- **Mockery** - PHP mocking framework used in feature tests (version 1.6+)

**Configuration & Environment**
- **BLUESKY_IDENTIFIER** - Environment variable for Bluesky account identifier (handle or DID)
- **BLUESKY_APP_PASSWORD** - App-specific password for Bluesky authentication (required for local CLI commands)
- **BLUESKY_OAUTH_PRIVATE_KEY** - Private key for OAuth authentication (production use)
- **APP_URL** - Application URL, defaults to `http://localhost:8000` for local development
- **SESSION_DRIVER** - Set to `database` for OAuth session persistence
- **QUEUE_CONNECTION** - Set to `database` for background job processing

**Testing Infrastructure**
- **RefreshDatabase** - Laravel testing trait for database state management (not needed - uses SQLite)
- **Event::fake()** - Laravel testing utility for mocking event dispatch in tests
- **Mockery** - PHP mocking framework used in feature tests for HTTP responses
- **PHPUnit** - Testing framework (version 11.x) with testdox output for readable test descriptions
- **Clover XML** - Code coverage report format generated during test runs
