<?php
/**
 * Prueba de código para MarketGoo. ¡Lee el README.md!
 */
require __DIR__."/vendor/autoload.php";

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

// Datos estáticos que modelan los resultados de la consulta GraphQL
$users = [
    1 => ["id" => 1, "name" => "Sergio Palma", "ip" => "188.223.227.125"],
    2 => ["id" => 2, "name" => "Manolo Engracia", "ip" => "194.191.232.168"],
    3 => ["id" => 3, "name" => "Fito Cabrales", "ip" => "77.162.109.160"]
];

// Definimos el schema del tipo de dato "Usuario" para GraphQL
$graphql_user_type = new ObjectType([
    "name" => "User",
    "fields" => [
        "id" => Type::int(),
        "name" => Type::string(),
        "ip" => Type::string()
    ]
]);

// Instanciamos la aplicación Slim. Es tan sencilla que sólo vamos a usar aquí
// la ruta "/graphql" para este test. Todo lo demás es por defecto.
$app = new Slim\App();
$app->map(["GET", "POST"], "/graphql", function(Request $request, Response $response) {
    global $users, $graphql_user_type;
    $debug = \GraphQL\Error\Debug::INCLUDE_DEBUG_MESSAGE | \GraphQL\Error\Debug::INCLUDE_TRACE;
    try {
        $graphQLServer = new \GraphQL\Server\StandardServer([
            "schema" => new Schema([
                "query" => new ObjectType([
                    "name" => "Query",
                    "fields" => [
                        "user" => [
                            "type" => $graphql_user_type,
                            "args" => [
                                "id" => Type::nonNull(Type::int())
                            ],
                            "resolve" => function ($rootValue, $args) use ($users) {
                                return isset($users[intval($args["id"])])
                                    ? $users[intval($args["id"])]
                                    : null;
                            }
                        ],
                        "users" => [
                            "type" => Type::listOf($graphql_user_type),
                            "resolve" => function() use ($users) {
                                return $users;
                            }
                        ]
                    ]
                ])
            ]),
            "debug" => $debug
        ]);

        return $graphQLServer->processPsrRequest($request, $response, $response->getBody());
    } catch (\Exception $e) {
        return $response->withStatus($e->getCode() ?? 500)->withJson([
            "errors" => [\GraphQL\Error\FormattedError::createFromException($e, $debug)]
        ]);
    }
});

$app->run();
