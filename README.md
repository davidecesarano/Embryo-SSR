# Embryo SSR (Server Side Rendering)
Server Side Rendering Javascript in PHP application with V8Js.

## Requirements
* PHP >= 7.1
* V8Js PHP extension (See [V8 Javascript compiled files for PHP](https://github.com/davidecesarano/phpv8js-compiled))

## Installation
Using Composer:
```
$ composer require davidecesarano/embryo-ssr
```

## Usage
You can use SSR if you want rendering a "snapshot" of our app. The JavaScript frameworks (like Vuejs or React) build client-side applications that manipulate DOM in the browser as output.

With Embryo SSR is possible to render the components into HTML strings on the server, send them directly to the browser, and finally "hydrate" the static markup into a fully interactive app on the client.
```php
use Embryo\ServerSideRendering\SSR;

$v8js = new \V8Js;
$ssr = new SSR($v8js);

echo $ssr
    ->env([
        'NODE_ENV' => 'production',
        'VUE_ENV' => 'server'
    ])
    ->context([
        'user' => [
            'name' => 'Davide'
        ]
    ])
    ->entry('path/to/js/entry-server.js')
    ->render();
```

## Example
You may quickly test using the built-in PHP server going to http://localhost:8000.
```
$ cd example
$ php -S localhost:8000
```

## Options
### `enabled(bool $enabled = true): self`
Enables or disables server side rendering. When disabled, the client script and the fallback html will be rendered instead.

### `env(array $env): self`
ENV variables are placed in `process.env` when the server script is executed.

### `entry($entry): self`
The path to your server script. Must be an arrary for multiple files.

### `context(array $context): self`
Context is passed to the server script in the `context` variable.

### `script(string $script): self`
Writes a javascript script. For example: `var a = "My Var"`.

### `fallback(string $fallback): self`
Sets the fallback html for when server side rendering is disabled. For example: `<div id="app"></div>`.