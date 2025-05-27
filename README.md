# Statusphere

This project is the Laravel edition of Statusphere, the official sample of Bluesky/AT Protocol.

## About

Statusphere is a sample application demonstrating how to use the [Laravel Bluesky package](https://github.com/invokable/laravel-bluesky) to integrate with the Bluesky social network. It follows the official [AT Protocol application guidelines](https://atproto.com/guides/applications).

## Live Demo

A live version of this application is available at [https://statusphere.puklipo.com/](https://statusphere.puklipo.com/). Users can log in with their Bluesky account to try the application.

## Technology Stack

- Laravel 12.x
- Tailwind 4.x
- Livewire 3.x / Volt 1.6.x
- laravel-bluesky 1.x
- No WebSocket/Firehose (Many PHP developers are not familiar with using supervisor, so WebSockets are not used)

## Server Infrastructure

- AWS EC2
- RDS
- Laravel Forge

## Comparison with Node.js Version

This Laravel implementation differs from the [official Node.js version](https://atproto.com/guides/applications) in several ways:

### Authentication
- Uses Laravel Socialite for Bluesky OAuth integration
- Stores authentication tokens in Laravel session
- Implements user model with Bluesky profile data

### Data Handling
- Uses Laravel Bluesky package for AT Protocol API calls
- Implements custom Status record model with NSID
- Uses Laravel's Eloquent ORM for data persistence

### User Interface
- Built with Livewire for reactive components
- Uses Blade templating with Tailwind CSS
- Implements Volt for single-file components

### Event Handling
- Uses Laravel event listeners instead of WebSockets
- Processes Bluesky commit events through StatusListener
- Implements Laravel queue system for background processing

### Development Experience
- Provides Laravel-specific tooling (Sail, Pint, Pail)
- Uses Laravel's configuration system for emoji definitions
- Supports Laravel's deployment workflows

## License

MIT
