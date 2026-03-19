# Qryma PHP SDK

A PHP SDK for the Qryma Search API, providing a simple and intuitive interface for accessing Qryma's powerful search capabilities.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage Examples](#usage-examples)
- [API Reference](#api-reference)
- [Configuration](#configuration)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Installation

You can install the Qryma PHP SDK using Composer:

```bash
composer require qryma-ai/qryma-php
```

## Quick Start

```php
// To install: composer require qryma-ai/qryma-php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
$client = qryma(['apiKey' => 'ak-********************']);

$response = $client->search('artificial intelligence', ['lang' => 'en']);

print_r($response);
```

## Usage Examples

### Basic Search

```php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
$client = qryma(['apiKey' => 'ak-********************']);

$response = $client->search('python programming');

// Access the organic results
$results = $response['organic'] ?? [];
foreach ($results as $result) {
    echo $result['title'] . "\n";
    echo $result['link'] . "\n";
    echo $result['snippet'] . "\n";
    echo "\n";
}
```

### Search with All Parameters

```php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
$client = qryma(['apiKey' => 'ak-********************']);

$response = $client->search('machine learning tutorials', [
    'lang' => 'en',
    'start' => 0,
    'safe' => false,
    'detail' => false
]);

$results = $response['organic'] ?? [];
echo 'Found ' . count($results) . ' results' . "\n";
```

### Using SearchOptions Object

For more control, you can use the `SearchOptions` class directly:

```php
require __DIR__ . '/vendor/autoload.php';

use Qryma\SearchOptions;

$client = qryma(['apiKey' => 'ak-********************']);

$options = new SearchOptions([
    'lang' => 'en',
    'safe' => true
]);

$response = $client->search('python programming', $options);

print_r($response);
```

### Custom Configuration

You can specify additional configuration options:

```php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
$client = qryma([
    'apiKey' => 'ak-********************',
    'baseUrl' => 'https://custom.qryma.com',
    'timeout' => 60
]);
$response = $client->search('test query');
print_r($response);
```

### API Response Format

The `search()` method returns the raw API response in the following format:

```php
[
  "organic" => [
    [
      "title" => "Result Title",
      "date" => "",
      "link" => "https://example.com",
      "position" => 1,
      "site_name" => "Example.com",
      "snippet" => "Description text..."
    ]
  ]
]
```

**Field descriptions:**
- `title`: Search result title
- `date`: Publication date (if available)
- `link`: URL of the search result
- `position`: Position in the results list
- `site_name`: Name of the website
- `snippet`: Brief description or excerpt from the page

## API Reference

### qryma($config)

Factory function to create a Qryma client instance.

**Parameters:**
- `$config['apiKey']`: Your Qryma API key (required)
- `$config['baseUrl']`: Base URL for the API (optional, default: `https://search.qryma.com`)
- `$config['timeout']`: Request timeout in seconds (optional, default: 30)

**Returns:**
- `QrymaClient` instance

### QrymaClient::search($query, $options = [])

Perform a search with the given query and return the raw API response.

**Parameters:**
- `$query`: Search query string (required)
- `$options`: Search options (optional)
  - `lang`: Language code for search results (e.g., 'am' for Amharic, 'en' for English)
  - `start`: Starting position of results (default: 0)
  - `safe`: Safe search mode: true or false (default: false)
  - `detail`: Include detailed results (default: false)

**Returns:**
- Raw API response array containing the search results

### Alternative: Using QrymaClient class directly

If you prefer, you can still use the class directly:

```php
require __DIR__ . '/vendor/autoload.php';

use Qryma\QrymaClient;

$client = new QrymaClient('ak-********************');
$response = $client->search('artificial intelligence');
print_r($response);
```

## Configuration

### Environment Variables

You can configure the API key using environment variables:

```bash
export QRYMA_API_KEY="your-api-key"
```

Then in your code:

```php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
$client = qryma(['apiKey' => getenv('QRYMA_API_KEY')]);
```

## Error Handling

The SDK raises exceptions for API errors:

```php
require __DIR__ . '/vendor/autoload.php';
use function Qryma\qryma;
try {
    $client = qryma(['apiKey' => 'ak-********************']);
    $response = $client->search('test query');
    $results = $response['organic'] ?? [];
    // Process results...

} catch (RuntimeException $e) {
    if (strpos($e->getMessage(), 'timed out') !== false) {
        echo 'Network timeout error';
    } elseif (strpos($e->getMessage(), 'API request failed') !== false) {
        echo 'API error';
    } else {
        echo 'Error: ' . $e->getMessage();
    }
}
```

Common error conditions:
- Invalid API key
- Rate limiting
- Network timeouts
- Invalid parameters

## Testing

The SDK includes a simple test file. To run the test:

1. First, replace the placeholder API key in `tests/TestSearch.php` with your actual API key
2. Then run the test:

```bash
./vendor/bin/phpunit tests/TestSearch.php
```

## Contributing

Contributions are welcome! Please see our contributing guide for more information.

## License

MIT License - see the [LICENSE](LICENSE) file for details.

## Support

If you encounter any issues or have questions, please:

1. Check the [documentation](https://qryma.com/documentation.html)
2. Open an issue on GitHub
3. Contact support at support@qryma.com

## Changelog

### 0.1.0
- Basic search functionality
- Simple `qryma()` factory function for easy initialization
- Advanced search with SearchOptions
- Result extraction methods
- API status check
- Error handling
- Comprehensive data models
