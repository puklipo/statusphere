# Statusphere Laravel edition

<kbd>![statusphere](https://github.com/user-attachments/assets/d3eeee73-9e2b-453b-b58d-d115a841031f)</kbd>

## About

Statusphere is a sample application demonstrating how to use the [Laravel Bluesky package](https://github.com/invokable/laravel-bluesky) to integrate with the Bluesky social network. It follows the official [AT Protocol application guidelines](https://atproto.com/guides/applications).

## Live Demo

A live version of this application is available at [https://statusphere.puklipo.com/](https://statusphere.puklipo.com/). Users can log in with their Bluesky account to try the application.

### Server Infrastructure

- AWS EC2
- RDS(MySQL)
- Laravel Forge

## Technology Stack

- Laravel 12.x
- Tailwind 4.x
- Livewire 3.x / Volt 1.6.x
- laravel-bluesky 1.x
- No WebSocket/Firehose (Many PHP developers are not familiar with using supervisor, so WebSockets are not used)

## AT Protocol Integration

Statusphere demonstrates key AT Protocol application patterns by implementing a decentralized emoji status system. The application follows the official [AT Protocol application guidelines](https://atproto.com/guides/applications) using Laravel and the `laravel-bluesky` package.

### OAuth Authentication

Users authenticate using their Bluesky credentials via OAuth flow:

- **Login Route**: Users enter their Bluesky handle (e.g., `alice.bsky.social`)
- **OAuth Flow**: Laravel Socialite with Bluesky driver handles the authentication
- **Session Management**: OAuth tokens are stored in Laravel sessions for API access
- **User Creation**: User records are created/updated with DID, handle, and profile information

The authentication is handled in the `/login` and `/callback` routes, which create an `OAuthSession` that provides authenticated access to the user's AT Protocol repository.

### Custom Data Schema

Statusphere publishes emoji status records using a custom AT Protocol schema:

- **Collection**: `com.puklipo.statusphere.status` 
- **Schema**: Records contain `status` (emoji) and `createdAt` (timestamp)
- **Record Keys**: Uses TID (Timestamp Identifier) for unique, time-ordered keys
- **Validation**: Laravel validation ensures only valid emojis from the configured list

The status records are defined in the `Status` class using the `laravel-bluesky` package's `Recordable` interface.

### Publishing Data

When users select an emoji status:

1. **Validation**: Emoji is validated against the configured list in `config/statusphere.php`
2. **OAuth Check**: Session tokens are verified and refreshed if needed  
3. **Record Creation**: A new status record is created with the selected emoji
4. **AT Protocol Write**: Record is published to the user's repository using `Bluesky::putRecord()`
5. **UI Update**: Livewire components update reactively to show the new status

The publishing flow is implemented in the `create-status` Livewire component, which provides real-time feedback and error handling.

### Fetching User Data

The application retrieves and displays user information:

- **Profile Data**: Fetches user profiles from their repositories for display names and avatars
- **Status History**: Retrieves the user's previous status records using `Bluesky::listRecords()`
- **Real-time Updates**: Livewire provides reactive updates when new statuses are created
- **Timeline Display**: Shows chronological status updates with user attribution

User data is accessed through the authenticated OAuth session, allowing read access to the user's AT Protocol repository.

### Event Processing

The application architecture is designed with consideration for AT Protocol event processing:

- **OAuth-Focused**: The primary implementation uses OAuth-based user interactions
- **No WebSockets**: As noted in the Technology Stack section, WebSockets/Firehose are intentionally not used
- **Reference Implementation**: The codebase includes reference patterns for those who wish to implement event processing
- **Future Extensibility**: The architecture allows for adding Bluesky Firehose integration if needed

Note: The current implementation prioritizes simplicity and accessibility for PHP developers who may not be familiar with WebSocket infrastructure.

### Development Features

- **Local Development**: OAuth posting is disabled locally; use `php artisan bsky:create-status` command instead
- **Error Handling**: Comprehensive error display for OAuth and API failures  
- **Validation**: Both client-side and server-side validation of emoji selections
- **Session Management**: Automatic token refresh and re-authentication when needed

## Getting Started

```bash
git clone https://github.com/invokable/statusphere.git
cd statusphere
cp .env.example .env
composer install
composer run post-create-project-cmd
npm install
npm run build
```

### OAuth Configuration

For Bluesky OAuth authentication, you need to create a private key. This is required before using any OAuth functionality and is the only configuration needed (no client_id or client_secret registration with Bluesky is required).

Generate a new private key with:

```bash
php artisan bluesky:new-private-key
```

The command will output a URL-safe base64 encoded key like this:
```
Please set this private key in .env

BLUESKY_OAUTH_PRIVATE_KEY="...url-safe base64 encoded key..."
```

Copy and paste this key into your `.env` file:

```
// .env

BLUESKY_OAUTH_PRIVATE_KEY="..."
```

### App Password Configuration

Due to OAuth limitations, posting from local environments is not supported with OAuth authentication. Instead, you need to use App Password authentication for local development and testing. Configure your Bluesky account credentials:

```
// .env

BLUESKY_IDENTIFIER=***.bsky.social
BLUESKY_APP_PASSWORD=****-****-****-****
```

For local development, use the following command to create status updates:

```bash
php artisan bsky:create-status
```

This command uses App Password authentication to post status updates to your Bluesky account.

Finally, start with the Laravel local server command. You can view it at http://localhost:8000.

```bash
php artisan serve
```

## Advanced Usage

If you want to create a more official-like implementation, you can also use WebSockets to receive and store data.
The laravel-bluesky package supports both Firehose and Jetstream, but Jetstream is easier to use when you only need data from specific collections like in this case.
Note that this Jetstream is Bluesky's Jetstream, not Laravel's Jetstream with the same name, which can be confusing.
You can check what kind of data is received on GitHub:
https://github.com/bluesky-social/jetstream


Run the command specifying the collection continuously using Supervisor:
```shell
php artisan bluesky:ws start -C com.puklipo.statusphere.status
```

When data is received, a `JetstreamCommitMessage` event is fired, which can be captured by a Listener and saved to the database:

```shell
php artisan make:listener StatusListener
```

```php:app/Listeners/StatusListener.php
namespace App\Listeners;

use App\Record\Status;
use Revolution\Bluesky\Events\Jetstream\JetstreamCommitMessage;

class StatusListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JetstreamCommitMessage $event): void
    {
        if ($event->operation !== 'create') {
            return;
        }

        $collection = data_get($event->message, 'commit.collection');
        if ($collection !== Status::NSID) {
            return;
        }

        $did = data_get($event->message, 'did');
        $status = data_get($event->message, 'commit.record.status');
        $createdAt = data_get($event->message, 'commit.record.createdAt');

        // Save status to database
    }
}
```

If you actually use this approach, other parts of the application would need to be modified as well.
This is just for explanation purposes in the advanced section, so no further implementation is provided.

When deploying to production, terminate with `stop` and configure Supervisor to automatically restart:
```shell
php artisan bluesky:ws stop
```

## License

MIT
