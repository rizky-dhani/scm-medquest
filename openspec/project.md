# Project Context

## Purpose
This project, scm-medquest, is likely a system for managing medical supplies or related operations, given the name "medquest" and the presence of models like `Location`, `Room`, and `RoomTemperature`. The Filament admin panel suggests it has a comprehensive backend for data management and reporting. It tracks temperature and humidity, generates notifications for deviations, and handles user access based on locations.

## Tech Stack
- Laravel (PHP 12.43.1)
- PHP (8.4.15)
- Filament (4.3.1)
- Livewire (3.7.3)
- MySQL/MariaDB
- TailwindCSS (4.0.14)
- Pest (3.8.4) for testing
- Rector (2.2.14) for code refactoring
- Laravel Pint (1.26.0) for code style
- Laravel Echo (2.0.2)


## Project Conventions

### Code Style
- Adherence to PSR-2 and PSR-12 standards.
- Utilizes Laravel Pint for automatic code formatting and linting.
- Follows FilamentPHP's code style guidelines for Filament-specific components.
- Avoids `any` type, `as` keyword for type casting, `!` operator for type assertion, and `ts-ignore`/`ts-nocheck`/`ts-expect-error` comments in TypeScript (as per .gemini/extensions/chrome-devtools-mcp/GEMINI.md).
- Prefers `for..of` over `forEach` in TypeScript.

### Architecture Patterns
- Follows Laravel's MVC (Model-View-Controller) architectural pattern.
- Implements Service Layer for complex business logic (e.g., `TemperatureDeviationNotificationService`).
- Uses FilamentPHP for administrative interfaces, adhering to its resource and page structure.
- Event-driven architecture for certain actions (e.g., `TemperatureDeviationCreated` event).

### Testing Strategy
- Unit tests for individual components and services (e.g., `tests/Unit/Services/`).
- Feature tests for application functionalities and HTTP endpoints (e.g., `tests/Feature/`).
- Utilizes PestPHP (`tests/Pest.php`) and PHPUnit (`phpunit.xml`) for testing.

### Git Workflow
- Uses a feature-branch workflow.
- Pull Requests are used for code reviews and merging into `main` or `develop` branches.
- Commit messages should be clear and descriptive, following a conventional commit format (e.g., `feat:`, `fix:`, `chore:`).

## Domain Context
The project operates within the domain of medical supply chain management, specifically focusing on environmental monitoring (temperature and humidity) for sensitive items. Key entities include:
- **Locations**: Physical sites where monitoring occurs.
- **Rooms**: Subdivisions within locations where specific items are stored and monitored.
- **Temperature/Humidity Readings**: Data collected from sensors in rooms.
- **Temperature Deviations**: Instances where temperature or humidity falls outside acceptable ranges, triggering notifications.
- **Users**: Personnel with varying access levels, potentially restricted by location.
- **Serial Numbers**: Tracking individual items or sensors.
- **Notification Logs**: Records of alerts sent for deviations.

## Important Constraints
- **Data Accuracy**: High importance on accurate and timely temperature/humidity data.
- **Real-time Monitoring & Notification**: Critical to notify users promptly of deviations to prevent damage to sensitive medical supplies.
- **Access Control**: Strict role-based access control, potentially location-based, to ensure data security and operational integrity.
- **Audit Trails**: Logging of actions and notifications for compliance and accountability.
- **Scalability**: Ability to handle a growing number of locations, rooms, sensors, and data points.

## External Dependencies
- **Redis**: Likely used for caching, queues (for notifications), and potentially session management.
- **Database (MariaDB)**: Primary data storage.
- **Email Service**: For sending temperature deviation and bulk notifications.
- **Sensor Hardware/APIs**: Integration with physical sensors or their APIs for data collection (implicit).
