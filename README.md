# API Magic

A PHP library that provides automatic and magical API client generation for REST APIs using dynamic method calls.

## Features

- 🪄 **Magic Methods**: Automatically converts method calls to HTTP requests
- 🔧 **Flexible Configuration**: Easy setup for different API endpoints
- 🔐 **Authentication Support**: Built-in support for Basic Authentication
- 📡 **HTTP Client**: Uses Guzzle HTTP client for reliable requests
- 🎯 **Route Verification**: Optional route validation before making requests
- 📋 **Multiple Formats**: Support for JSON and form-encoded data
- 🏷️ **Named Returns**: Option to wrap responses with element names

## Installation

Install via Composer:

```bash
composer require santosalan/api-magic
```

## Requirements

- PHP >= 5.5.0
- Guzzle HTTP >= 6.3.0

## Basic Usage

```php
<?php

use SantosAlan\ApiMagic\ApiMagic;

// Create an instance
$api = new ApiMagic();

// Configure the API base URL
$api->host = 'https://api.example.com';
$api->prefix = '/v1';

// Make a GET request to /users
$response = $api->users(['GET']);

// Make a POST request to /users with data
$response = $api->users(['POST'], null, [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Make a request to the /users.php?access=hash1234 - explicit extension endpoint, URL parameters and data
$response = $api->{'users.php'}(['POST'], ['?access=hash1234'], [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Make a GET request to /users/123
$response = $api->users(['GET'], [123]);
```

## Configuration

### Basic Configuration

```php
$api = new ApiMagic();
$api->host = 'https://api.example.com';  // Base URL
$api->port = ':8080';                     // Port (optional)
$api->prefix = '/api/v1';                 // URL prefix (optional)
```

### Authentication

```php
// Basic Authentication
$api->auth('username', 'password');

// Basic Authentication (explicit)
$api->auth('username', 'password', 'basic');

// Other authentication types
$api->auth('token', 'secret', 'digest');
```

### Token-based Authentication

```php
// Set token field name
$api->tokenField = 'access_token';

// Override token method
class MyApiMagic extends ApiMagic {
    protected function token() {
        return 'your-api-token-here';
    }
}
```

## Advanced Usage

### JSON Requests

```php
// Send data as JSON
$response = $api->toJson()->users(['POST'], null, [
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
]);
```

### Custom Headers

```php
// Send custom headers
$response = $api->users(['GET'], null, [], [
    'Content-Type' => 'application/json',
    'X-Custom-Header' => 'value'
]);
```

### Named Element Returns

```php
// Wrap response with element name
$response = $api->element('user_data')->users(['GET']);
// Returns: {"user_data": {...actual response...}}

// Disable named returns
$api->namedReturn = false;
```

### Route Verification

```php
// Enable route verification
$api->actionRoutes = 'routes'; // Endpoint that returns available routes

// Now API Magic will verify if the route exists before making requests
$response = $api->users(['GET']); // Will check if 'users' route exists first
```

## Method Parameters

When calling dynamic methods, you can pass up to 4 parameters:

```php
$api->methodName([
    $httpMethod,    // HTTP method (GET, POST, PUT, DELETE, etc.)
    $urlParams,     // URL parameters (array)
    $data,          // Request data (array)
    $headers        // Custom headers (array)
]);
```

### Examples

```php
// GET /users
$api->users(['GET']);

// GET /users/123
$api->users(['GET'], [123]);

// GET /users/123/posts
$api->users(['GET'], [123, 'posts']);

// POST /users with data
$api->users(['POST'], null, [
    'name' => 'John',
    'email' => 'john@example.com'
]);

// PUT /users/123 with data and headers
$api->users(['PUT'], [123], [
    'name' => 'John Updated'
], [
    'Content-Type' => 'application/json'
]);
```

## Error Handling

```php
try {
    $response = $api->users(['GET']);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## Route Verification Response Format

If you enable route verification, your routes endpoint should return JSON in this format:

```json
[
    {"action": "users"},
    {"action": "posts"},
    {"action": "comments"}
]
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Alan Santos**
- Email: alanssantos@outlook.com
- GitHub: [@santosalan](https://github.com/santosalan)

## Keywords

PHP, API, Magic, Service, Automatic, Utilities, Guzzle, REST, HTTP Client

## Practical Examples

### Creating Custom API Clients

You can create specific API clients by extending the `ApiMagic` class:

```php
<?php

use SantosAlan\ApiMagic\ApiMagic;

// GitHub API Client
class GitHubApi extends ApiMagic
{
    public function __construct($token = null)
    {
        $this->host = 'https://api.github.com';
        $this->namedReturn = false;
        
        if ($token) {
            $this->auth($token, '', 'token');
        }
    }
    
    protected function token()
    {
        return 'your-github-token-here';
    }
}

// JSONPlaceholder API Client
class JsonPlaceholderApi extends ApiMagic
{
    public function __construct()
    {
        $this->host = 'https://jsonplaceholder.typicode.com';
        $this->namedReturn = false;
    }
}

// REST Countries API Client
class CountriesApi extends ApiMagic
{
    public function __construct()
    {
        $this->host = 'https://restcountries.com';
        $this->prefix = '/v3.1';
        $this->namedReturn = false;
    }
}
```

### Using Multiple API Clients

```php
<?php

// Initialize different API clients
$github = new GitHubApi();
$jsonPlaceholder = new JsonPlaceholderApi();
$countries = new CountriesApi();

// Get GitHub user information
$githubUser = $github->users(['GET'], ['octocat']);
echo "GitHub User: " . $githubUser;

// Get posts from JSONPlaceholder
$posts = $jsonPlaceholder->posts(['GET']);
echo "Posts: " . $posts;

// Get specific post
$post = $jsonPlaceholder->posts(['GET'], [1]);
echo "Post #1: " . $post;

// Create new post
$newPost = $jsonPlaceholder->posts(['POST'], null, [
    'title' => 'My New Post',
    'body' => 'This is the content of my new post',
    'userId' => 1
]);
echo "New Post: " . $newPost;

// Get all countries
$allCountries = $countries->all(['GET']);
echo "All Countries: " . $allCountries;

// Get country by name
$brazil = $countries->name(['GET'], ['brazil']);
echo "Brazil Info: " . $brazil;

// Get countries by region
$europeCountries = $countries->region(['GET'], ['europe']);
echo "Europe Countries: " . $europeCountries;
```

### Advanced GitHub API Example

```php
<?php

class GitHubApi extends ApiMagic
{
    private $username;
    private $token;
    
    public function __construct($username = null, $token = null)
    {
        $this->host = 'https://api.github.com';
        $this->namedReturn = false;
        $this->username = $username;
        $this->token = $token;
        
        if ($token) {
            $this->auth($token, '', 'token');
        }
    }
    
    // Get user repositories
    public function getUserRepos($username = null)
    {
        $user = $username ?: $this->username;
        return $this->users(['GET'], [$user, 'repos']);
    }
    
    // Get repository issues
    public function getRepoIssues($owner, $repo)
    {
        return $this->repos(['GET'], [$owner, $repo, 'issues']);
    }
    
    // Create a new issue
    public function createIssue($owner, $repo, $title, $body = '')
    {
        return $this->repos(['POST'], [$owner, $repo, 'issues'], [
            'title' => $title,
            'body' => $body
        ]);
    }
}

// Usage
$github = new GitHubApi('your-username', 'your-token');

// Get user repositories
$repos = $github->getUserRepos('octocat');

// Get specific repository issues
$issues = $github->getRepoIssues('octocat', 'Hello-World');

// Create new issue (requires authentication)
$newIssue = $github->createIssue('your-username', 'your-repo', 'Bug Report', 'Found a bug...');
```

### E-commerce API Example

```php
<?php

class EcommerceApi extends ApiMagic
{
    public function __construct($apiKey)
    {
        $this->host = 'https://api.yourstore.com';
        $this->prefix = '/v1';
        $this->tokenField = 'api_key';
        $this->namedReturn = true;
    }
    
    protected function token()
    {
        return 'your-api-key-here';
    }
    
    // Get all products
    public function getAllProducts($page = 1, $limit = 10)
    {
        return $this->products(['GET'], null, [
            'page' => $page,
            'limit' => $limit
        ]);
    }
    
    // Create new product
    public function createProduct($productData)
    {
        return $this->toJson()->products(['POST'], null, $productData);
    }
    
    // Update product
    public function updateProduct($productId, $productData)
    {
        return $this->toJson()->products(['PUT'], [$productId], $productData);
    }
    
    // Get orders
    public function getOrders($status = null)
    {
        $params = $status ? ['status' => $status] : [];
        return $this->orders(['GET'], null, $params);
    }
}

// Usage
$ecommerce = new EcommerceApi('your-api-key');

// Get products
$products = $ecommerce->getAllProducts(1, 20);

// Create new product
$newProduct = $ecommerce->createProduct([
    'name' => 'Amazing Product',
    'price' => 29.99,
    'description' => 'This is an amazing product',
    'category' => 'electronics'
]);

// Get pending orders
$pendingOrders = $ecommerce->getOrders('pending');
```

### Weather API Example

```php
<?php

class WeatherApi extends ApiMagic
{
    private $apiKey;
    
    public function __construct($apiKey)
    {
        $this->host = 'https://api.openweathermap.org';
        $this->prefix = '/data/2.5';
        $this->namedReturn = false;
        $this->apiKey = $apiKey;
    }
    
    // Get current weather
    public function getCurrentWeather($city)
    {
        return $this->weather(['GET'], null, [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric'
        ]);
    }
    
    // Get weather forecast
    public function getForecast($city, $days = 5)
    {
        return $this->forecast(['GET'], null, [
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric',
            'cnt' => $days * 8 // 8 forecasts per day (every 3 hours)
        ]);
    }
}

// Usage
$weather = new WeatherApi('your-openweather-api-key');

// Get current weather for New York
$currentWeather = $weather->getCurrentWeather('New York');

// Get 5-day forecast for London
$forecast = $weather->getForecast('London', 5);
```
