<?php
/**
 * Prueba de código para MarketGoo. ¡Lee el README.md!
 */
require __DIR__ . "/vendor/autoload.php";

use App\Service\LocationService;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\FormattedError;
use GraphQL\Server\StandardServer;
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

// Definimos el schema del tipo de dato "Usuario" para GraphQL - Hacer el Usuario como schema
$graphql_user_type = new ObjectType([
    "name" => "User",
    "fields" => [
        "id" => Type::int(),
        "name" => Type::string(),
        "ip" => Type::string(),
        "ip_region" => Type::string()
    ]
]);

// Instanciamos la aplicación Slim. Es tan sencilla que sólo vamos a usar aquí
// la ruta "/graphql" para este test. Todo lo demás es por defecto.
$app = Slim\Factory\AppFactory::create();

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false, true, true);

$app->map(["GET", "POST"], "/graphql", function (Request $request, Response $response) {
    global $users, $graphql_user_type;
    $debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
    try {
        $graphQLServer = new StandardServer([
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
                                $users[intval($args["id"])]["ip_region"] =
                                    LocationService::getIpRegionByIpv2(
                                        $users[intval($args["id"])]["ip"] ?? ''
                                    );
                                return $users[intval($args["id"])] ?? null;
                            }
                        ],
                        "users" => [
                            "type" => Type::listOf($graphql_user_type),
                            "resolve" => function () use ($users) {
                                foreach ($users as &$user) {
                                    $user["ip_region"] = LocationService::getIpRegionByIpv2($user["ip"]);
                                }

                                return $users;
                            }
                        ]
                    ]
                ])
            ]),
            "debugFlag" => $debug
        ]);

        return $graphQLServer->processPsrRequest($request, $response, $response->getBody());
    } catch (Exception $e) {
        $status = (int) $e->getCode();
        $response->getBody()->write(json_encode([
            "errors" => [FormattedError::createFromException($e, $debug)]
        ], JSON_THROW_ON_ERROR));
        return $response->withStatus($status >= 400 && $status <= 599 ? $status : 500)
            ->withHeader('Content-Type', 'application/json');
    }
});

$app->run();
