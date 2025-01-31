# Weather Web Application

A simple weather application using PHP and OpenWeatherMap API.

## Features
- Real-time weather data
- City search functionality
- Temperature in Celsius
- Humidity and wind speed display
- Weather condition icons
- Caching system to reduce API calls

## Setup Instructions

1. **Get API Key**
   - Register at [OpenWeatherMap](http://api.weatherapi.com/v1)
   - Get your free API key

2. **Configuration**
   - Create `config.php` from `config.php.example`
   - Insert your API key in `config.php`

3. **Server Requirements**
   - PHP 7.4 or higher
   - `allow_url_fopen` enabled in php.ini
   - PHP sessions enabled

4. **Run Application**
   - Place files in web server directory
   - Access via browser

## Dependencies
- Bootstrap 5.3
- weatherapi API