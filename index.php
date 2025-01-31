<?php
session_start();
require_once 'config.php';

$weather = null;
$error = null;

// Check for cached data
if (isset($_SESSION['weather']) && (time() - $_SESSION['weather']['time'] < CACHE_TIME)) {
    $weather = $_SESSION['weather']['data'];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = trim($_POST['city']);
    
    if (!empty($city)) {
        // Sanitize input
        $city = htmlspecialchars($city);
        
        // Build API URL
        $apiUrl = BASE_URL . "?q=" . urlencode($city) . "&appid=" . API_KEY . "&units=metric";
        
        try {
            // Fetch weather data
            $response = @file_get_contents($apiUrl);
            
            if ($response === FALSE) {
                throw new Exception("City not found. Please try again.");
            }
            
            $data = json_decode($response, true);
            
            if ($data['cod'] != 200) {
                throw new Exception($data['message'] ?? "Unknown error occurred");
            }
            
            // Process successful response
            $weather = [
                'city' => $data['name'],
                'country' => $data['sys']['country'],
                'temp' => round($data['main']['temp']),
                'humidity' => $data['main']['humidity'],
                'wind' => $data['wind']['speed'],
                'description' => ucfirst($data['weather'][0]['description']),
                'icon' => $data['weather'][0]['icon']
            ];
            
            // Store in session cache
            $_SESSION['weather'] = [
                'time' => time(),
                'data' => $weather
            ];
            
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "Please enter a city name";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Weather Finder</h2>
                        
                        <form method="post">
                            <div class="input-group mb-3">
                                <input type="text" 
                                       class="form-control" 
                                       name="city" 
                                       placeholder="Enter city name" 
                                       required
                                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </form>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <?php if ($weather): ?>
                            <div class="weather-info text-center">
                                <h3><?= $weather['city'] ?>, <?= $weather['country'] ?></h3>
                                <img src="http://api.weatherapi.com/v1<?= $weather['icon'] ?>@2x.png" 
                                     alt="Weather icon" 
                                     class="weather-icon">
                                <div class="display-4 mb-3"><?= $weather['temp'] ?>°C</div>
                                <div class="details">
                                    <p class="mb-1"><?= $weather['description'] ?></p>
                                    <p class="mb-1">Humidity: <?= $weather['humidity'] ?>%</p>
                                    <p class="mb-0">Wind: <?= $weather['wind'] ?> m/s</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>