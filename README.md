# RockPaperScissorsLizardSpock

Rock Paper Scissors Lizard Spock 1st player console game

## Project Description

This is a Laravel-based implementation of the Rock Paper Scissors Lizard Spock game, where a player can play against the computer through a command-line interface.

## Deployment Instructions

The project can be easily deployed using Docker and docker-compose:

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/RockPaperScissorsLizardSpock.git
   cd RockPaperScissorsLizardSpock
   ```

2. Create a `.env` file in the root directory with the following variables:
   ```
   APP_PORT=8000
   REDIS_PORT=6379
   ```
   You can adjust the ports as needed.

3. Build and start the containers:
   ```
   docker-compose up -d
   ```

4. Access the Laravel application container:
   ```
   docker exec -it app-server bash
   ```

5. Inside the container, navigate to the Laravel directory and install dependencies:
   ```
   cd /var/www/laravel
   composer install
   ```

6. Set up Laravel environment:
   ```
   cp .env.example .env
   php artisan key:generate
   ```

## Available Commands

### Game: Rock Paper Scissors Lizard Spock

To play the Rock Paper Scissors Lizard Spock game against the computer:

```
php artisan game:rpsls
```

This interactive command allows you to:
- Choose your fighter (Rock, Paper, Scissors, Lizard, or Spock)
- Play against a computer opponent
- View game statistics and results
- Exit the game when finished

### Code Quality Check

To run PHP Code Sniffer and check your code against PSR-12 standards:

```
php artisan code:check [path]
```

Parameters:
- `path` (optional): The path to check. Defaults to `./app` if not specified.

### Running Tests

To run the application tests:

```
php artisan test
```

This will execute all the unit and feature tests in the project, ensuring that all components work as expected.

## Project Structure

The project follows standard Laravel architecture with:
- Console commands for game interaction
- Services for game logic
- Repositories for data storage
- Unit and feature tests