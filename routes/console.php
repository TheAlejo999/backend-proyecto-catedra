<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('postman:export
    {--path=storage/app/postman/backend-proyecto-catedra.postman_collection.json : Relative or absolute output path}
    {--name=Backend Proyecto Catedra API : Postman collection display name}
', function () {
    $outputPath = (string) $this->option('path');

    if (! str_starts_with($outputPath, DIRECTORY_SEPARATOR) && ! preg_match('/^[A-Za-z]:\\\\/', $outputPath)) {
        $outputPath = base_path($outputPath);
    }

    $collectionName = (string) $this->option('name');

    $items = collect(app('router')->getRoutes())
        ->flatMap(function ($route) {
            $methods = collect($route->methods())
                ->reject(fn (string $method) => in_array($method, ['HEAD', 'OPTIONS'], true))
                ->values();

            if ($methods->isEmpty()) {
                return [];
            }

            $rawUri = trim($route->uri(), '/');
            $rawUri = $rawUri === '' ? '/' : '/'.$rawUri;

            $postmanUri = preg_replace('/\{([^}]+)\??\}/', ':$1', $rawUri) ?? $rawUri;
            $pathSegments = $postmanUri === '/'
                ? []
                : array_values(array_filter(explode('/', trim($postmanUri, '/'))));

            $routeParameters = collect($route->parameterNames())
                ->map(fn (string $name) => ['key' => $name, 'value' => ''])
                ->values()
                ->all();

            $usesSanctum = collect($route->gatherMiddleware())
                ->contains(fn (string $middleware) => str_contains($middleware, 'auth:sanctum'));

            $routeName = $route->getName();

            return $methods->map(function (string $method) use (
                $rawUri,
                $postmanUri,
                $pathSegments,
                $routeParameters,
                $usesSanctum,
                $routeName
            ) {
                $item = [
                    'name' => sprintf('%s %s', $method, $rawUri),
                    'request' => [
                        'method' => $method,
                        'header' => [
                            ['key' => 'Accept', 'value' => 'application/json'],
                        ],
                        'url' => [
                            'raw' => '{{base_url}}'.$postmanUri,
                            'host' => ['{{base_url}}'],
                            'path' => $pathSegments,
                            'variable' => $routeParameters,
                        ],
                    ],
                    'response' => [],
                ];

                if ($routeName) {
                    $item['request']['description'] = 'Route name: '.$routeName;
                }

                if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
                    $item['request']['header'][] = ['key' => 'Content-Type', 'value' => 'application/json'];
                    $item['request']['body'] = [
                        'mode' => 'raw',
                        'raw' => "{\n  \"example\": \"value\"\n}",
                        'options' => [
                            'raw' => [
                                'language' => 'json',
                            ],
                        ],
                    ];
                }

                if ($usesSanctum) {
                    $item['request']['auth'] = [
                        'type' => 'bearer',
                        'bearer' => [
                            ['key' => 'token', 'value' => '{{auth_token}}', 'type' => 'string'],
                        ],
                    ];
                }

                return $item;
            });
        })
        ->values()
        ->all();

    $collection = [
        'info' => [
            '_postman_id' => (string) Str::uuid(),
            'name' => $collectionName,
            'description' => 'Generated from Laravel routes with postman:export.',
            'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
        ],
        'item' => $items,
        'variable' => [
            ['key' => 'base_url', 'value' => 'http://localhost:8000'],
            ['key' => 'auth_token', 'value' => ''],
        ],
    ];

    File::ensureDirectoryExists(dirname($outputPath));
    File::put(
        $outputPath,
        json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
    );

    $this->info('Postman collection exported successfully.');
    $this->line('Path: '.$outputPath);
    $this->line('Endpoints exported: '.count($items));
})->purpose('Export all Laravel routes to a Postman collection JSON file');
