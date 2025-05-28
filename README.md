# Statusphere Laravel edition

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

## License

MIT
