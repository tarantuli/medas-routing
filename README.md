# medas-routing

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

Attribute-based HTTP routing. Routes are defined by annotating handler classes with `#[Route]` and handler methods with HTTP method attributes (`#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`, `#[Options]`). The router discovers all registered handlers, compiles regex patterns from their parameter definitions, and matches incoming requests at runtime.

**How it works:**

A `Route` attribute on a class defines the URL prefix for that handler (e.g. `/invoices`). An HTTP method attribute on a method defines the rest of the path. The full URL pattern is the concatenation of the global prefix (if any), the route parameters, and the method parameters. `HandlerFinder` scans all registered `#[Service]` classes for these attributes and builds `RouteHandler` instances. `HandlerManager` wraps them with a request-scoped cache.

**Parameters:**

| Class                 | Pattern           | Readable  | Denormalized type |
|-----------------------|-------------------|-----------|-------------------|
| `Constant('segment')` | Literal `segment` | `segment` | `string`          |
| `Integer('name')`     | `(?<name>\d+)`    | `:name`   | `int`             |
| `Uuid('id')`          | UUID regex        | `:uuid`   | `Uuid`            |
| `Anything('name')`    | `(?<name>.*)`     | `:name`   | `string`          |

**`EntityRoute`** is a convenience subclass of `Route` that derives the URL segment from the entity class's short name in kebab-case and marks the route as the canonical endpoint for that entity. E.g. `InvoiceLineItem` → `/invoice-line-item`.

**`EndpointFinder`** uses `endpointForEntity` metadata to reverse-generate URLs from entity instances or collection class names without hard-coding path strings.

## Configuration options

| Option                  | Default | Description                                     |
|-------------------------|---------|-------------------------------------------------|
| `routing.global-prefix` | `null`  | Prefix prepended to every route (e.g. `api/v1`) |

## Usage

### Package developer context

Register the package:

```php
use Medas\Routing\RoutingPackage;

RoutingPackage::instance();
```

**Defining a basic route:**

```php
use Medas\Core\Interfaces\HttpRequestHandler;
use Medas\HttpRequestHandler\ResponseTypes\{JsonResponse, Response};
use Medas\Routing\{Route, Methods\Get, Methods\Post};
use Medas\Core\Attributes\Service;

// Route prefix: /invoices
#[Service, Route('invoices')]
readonly class InvoiceHandlers
{
    public function __construct(
        private InvoiceService $invoiceService,
    ) {}

    // GET /invoices
    #[Get(isCollectionEndpoint: true)]
    public function list(): Response&JsonResponse { /* ... */ }

    // POST /invoices
    #[Post]
    public function create(): Response&JsonResponse { /* ... */ }
}
```

**Route with URL parameters:**

```php
use Medas\Routing\{Route, Methods\Get, Methods\Put, Methods\Delete, Parameters\Uuid};
use Medas\Core\Interfaces\{Uuid as UuidType};

#[Service, Route(['invoices', new Uuid('id')])]
readonly class InvoiceHandlers
{
    // GET /invoices/:uuid
    #[Get(isEntityEndpoint: true)]
    public function get(UuidType $id): Response&JsonResponse { /* ... */ }

    // PUT /invoices/:uuid
    #[Put]
    public function update(UuidType $id): Response&JsonResponse { /* ... */ }

    // DELETE /invoices/:uuid
    #[Delete]
    public function delete(UuidType $id): Response&JsonResponse { /* ... */ }
}
```

Route parameters are extracted from the URL and injected into the method via the DI parameter resolver. The `Uuid` parameter automatically converts the string to a `Uuid` object.

**Method-level parameters** — add path segments at the method level for nested routes:

```php
use Medas\Routing\{Route, Methods\Get, Parameters\Uuid};

#[Service, Route('invoices')]
readonly class InvoiceHandlers
{
    // GET /invoices/:uuid/lines
    #[Get(new Uuid('id'), 'lines', isCollectionEndpoint: true)]
    public function lines(UuidType $id): Response&JsonResponse { /* ... */ }
}
```

**`EntityRoute` shorthand:**

```php
use Medas\Routing\{EntityRoute, Methods\Get};

// Derives /invoice from Invoice::class; marks as canonical entity endpoint
#[Service, EntityRoute(Invoice::class)]
readonly class InvoiceHandlers
{
    // GET /invoice — collection endpoint
    #[Get(isCollectionEndpoint: true)]
    public function list(): Response&JsonResponse { /* ... */ }

    // GET /invoice/:uuid — entity endpoint
    #[Get(new Uuid('id'), isEntityEndpoint: true)]
    public function get(UuidType $id): Response&JsonResponse { /* ... */ }
}
```

**Reverse URL generation with `EndpointFinder`:**

```php
use Medas\Routing\EndpointFinder;
use Medas\Core\Attributes\Service;

#[Service]
readonly class LinkBuilder
{
    public function __construct(
        private EndpointFinder $endpointFinder,
    ) {}

    public function invoiceUrl(Invoice $invoice): string|null
    {
        // Walks the entity handler's parameters and fills in the values
        return $this->endpointFinder->forEntity($invoice);
        // → '/invoice/01963f4a-c3b2-7000-8d5e-1a2b3c4d5e6f'
    }

    public function invoiceCollectionUrl(): string|null
    {
        return $this->endpointFinder->forCollection(Invoice::class);
        // → '/invoice'
    }
}
```

**Named routes** — assign a name to a method handler for lookup by name:

```php
#[Get(name: 'invoice.list', isCollectionEndpoint: true)]
public function list(): Response&JsonResponse { /* ... */ }
```

```php
$handler = $handlerManager->findByName('invoice.list');
$endpoint = $handler?->endpointPattern(); // GET:/invoice
```

**Global prefix** — prepend a segment to every route:

```yaml
routing:
  global-prefix: api/v1
```

All routes are now under `/api/v1/…`. The prefix is injected as a `Constant` parameter at the start of every `RouteHandler`'s parameter list.

**Rebuilding the route cache:**

```bash
php bin/medas routing:rebuild
```

Clears and rebuilds the cached handler list. Run this after adding or removing route attributes.

**Listing all routes:**

```bash
php bin/medas routing:list
```

Outputs a table of all registered routes with their HTTP method, pattern, and handler class/method.

### Backend user context

Route discovery runs once per request (cached in memory). The `routing:rebuild` command also primes the persistent cache used across requests, so it should be run after each deployment that changes routes.

**Parameter injection** — URL parameters are extracted by name from the regex match and passed to the DI resolver. The handler method receives them as typed arguments alongside any other injected dependencies. For a `Uuid` parameter named `id`, the method simply declares `UuidType $id` and receives the resolved object.

**Route priority** — when multiple routes could match a URL, higher-priority routes win. Constant-only routes are registered at higher priority than routes with variable parameters to prevent a wildcard route from capturing a specific path.
