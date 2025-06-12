# Statusphere Laravel Edition - Onboarding Guide

## Overview

**Statusphere** is a sample Laravel application that demonstrates integration with the AT Protocol (the decentralized social networking protocol that powers Bluesky). The project serves as a reference implementation for PHP developers who want to build applications that interact with decentralized social networks.

**Primary Users:**
- PHP developers learning AT Protocol integration
- Laravel developers exploring decentralized social networking
- Engineers building AT Protocol-compatible applications

**What it enables:**
- OAuth authentication with Bluesky accounts
- Publishing emoji status updates to the AT Protocol network
- Retrieving and displaying user profiles and status histories
- Real-time event processing from the AT Protocol (optional advanced feature)

The application prioritizes simplicity and follows Laravel best practices, making it accessible to PHP developers without requiring deep knowledge of decentralized protocols or complex JavaScript frameworks.

## Project Organization

### Core Systems and Services

**Authentication System**
- `routes/web.php` - OAuth flow with Bluesky using Laravel Socialite
- `app/Models/User.php` - User model with AT Protocol integration
- Session management for OAuth tokens

**Status Management**
- `app/Record/Status.php` - AT Protocol record definition for emoji statuses
- `resources/views/livewire/create-status.blade.php` - Livewire component for status creation
- `app/Console/Commands/CreateStatusCommand.php` - CLI command for status posting

**User Interface**
- `resources/views/livewire/home.blade.php` - Authenticated user home page
- `resources/views/welcome.blade.php` - Public landing page
- `resources/views/components/` - Reusable UI components

**Real-time Processing (Optional)**
- `app/Listeners/StatusListener.php` - Event listener for AT Protocol commits
- WebSocket integration via laravel-bluesky package

### Main Files and Directories

```
├── app/
│   ├── Console/Commands/CreateStatusCommand.php    # CLI status posting
│   ├── Listeners/StatusListener.php                # AT Protocol event processing
│   ├── Models/User.php                             # User model with Bluesky integration
│   └── Record/Status.php                           # AT Protocol status record definition
├── resources/views/
│   ├── livewire/
│   │   ├── create-status.blade.php                 # Status creation component
│   │   └── home.blade.php                          # User home page
│   ├── components/
│   │   ├── emoji.blade.php                         # Emoji display component
│   │   └── layouts/app.blade.php                   # Main layout template
│   └── welcome.blade.php                           # Public landing page
├── config/statusphere.php                          # Emoji configuration
├── routes/web.php                                  # OAuth and main routes
└── .github/workflows/                              # CI/CD pipelines
```

### Development Infrastructure

**Frontend Build System**
- `vite.config.js` - Asset bundling with Laravel Vite plugin
- `tailwind.config.js` - Tailwind CSS configuration
- `package.json` - Node.js dependencies (Vite, Tailwind, Axios)

**Testing and Quality**
- `phpunit.xml` - PHPUnit test configuration
- `tests/` - Feature and unit tests
- `.github/workflows/tests.yml` - Automated testing pipeline
- `.github/workflows/lint.yml` - Code style enforcement with Pint

**Development Environment**
- `composer.json` - PHP dependencies and scripts
- `.env.example` - Environment configuration template
- Laravel Sail for Docker-based development

### Key Classes and Functions

**Core Models**
- `App\Models\User` - User model with `status()` accessor for latest AT Protocol status
- `App\Record\Status` - Implements `Recordable` interface for AT Protocol records

**Livewire Components**
- `create-status.blade.php::submit(string $emoji)` - Validates and publishes status
- `home.blade.php::mount()` - Initializes user session and loads status data

**Console Commands**
- `CreateStatusCommand::handle()` - CLI status posting with app password auth

**Event Processing**
- `StatusListener::handle(JetstreamCommitMessage $event)` - Processes AT Protocol events

## Glossary of Codebase-Specific Terms

**AT Protocol Integration**
- **NSID** - Namespace Identifier: `com.puklipo.statusphere.status` for status records
- **TID** - Time-sortable ID generator for unique AT Protocol record keys (`Revolution\Bluesky\Core\TID`)
- **OAuthSession** - Bluesky authentication session wrapper (`Revolution\Bluesky\Session\OAuthSession`)
- **putRecord** - AT Protocol method for publishing records to user repositories
- **listRecords** - AT Protocol method for retrieving records from repositories
- **assertDid** - Method to get user's Decentralized Identifier from Bluesky session

**Application-Specific Classes**
- **Status** - Custom AT Protocol record class (`app/Record/Status.php`)
- **StatusListener** - Event handler for AT Protocol commit messages (`app/Listeners/StatusListener.php`)
- **CreateStatusCommand** - Artisan command for CLI status posting (`app/Console/Commands/CreateStatusCommand.php`)
- **statusphere.status** - Configuration array of allowed emoji statuses (`config/statusphere.php`)

**Livewire Components**
- **create-status** - Livewire component for emoji status creation (`resources/views/livewire/create-status.blade.php`)
- **home** - User dashboard Livewire component (`resources/views/livewire/home.blade.php`)
- **myStatus** - Livewire state property holding current user status
- **status-created** - Livewire event fired when new status is successfully posted

**Bluesky Integration**
- **bluesky_session** - Laravel session key storing OAuth session data
- **laravel-bluesky** - Third-party package providing AT Protocol integration (`revolution/laravel-bluesky`)
- **Bluesky** - Laravel facade for AT Protocol operations (`Revolution\Bluesky\Facades\Bluesky`)
- **bsky:create-status** - Artisan command signature for CLI status posting
- **JetstreamCommitMessage** - Event class for AT Protocol commit notifications

**Authentication & Users**
- **did** - Decentralized Identifier for users on AT Protocol network
- **handle** - User's human-readable identifier (e.g., `alice.bsky.social`)
- **issuer** - OAuth issuer URL, typically `https://bsky.social`
- **refresh_token** - OAuth token for session renewal

**Development Tools**
- **Volt** - Livewire syntax extension for single-file components
- **Pint** - Laravel's opinionated PHP code style fixer
- **Sail** - Laravel's Docker development environment
- **concurrently** - NPM package for running multiple development processes

**Configuration & Environment**
- **BLUESKY_IDENTIFIER** - Environment variable for Bluesky account identifier
- **BLUESKY_APP_PASSWORD** - App-specific password for Bluesky authentication
- **BLUESKY_OAUTH_PRIVATE_KEY** - Private key for OAuth authentication

**Testing Infrastructure**
- **RefreshDatabase** - Laravel testing trait for database state management
- **Event::fake()** - Laravel testing utility for mocking event dispatch
- **Mockery** - PHP mocking framework used in feature tests
