<?php
/**
 * Prueba de código para MarketGoo. ¡Lee el README.md!
 */
require __DIR__ . "/vendor/autoload.php";

use GraphQL\Error\Debug;
use GraphQL\Error\FormattedError;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;

function getIpRegionByIp(string $ip): string
{
    $baseUrl = "http://api.ipstack.com/"; // free enroll. free use. https://ipwhois.io/documentation can be use instead
    //https://rapidapi.com/whoisapi/api/ip-geolocation/ => free and low latence
    $fullUrl = sprintf($baseUrl . "%s?access_key=23dc9996807fa1f7d61a2b5acf791a23", $ip);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($result, true);
    return "{$data['city']} - {$data['region_name']} ({$data['country_name']}) ";
}

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
$app = new Slim\App();

$app->map(["GET", "POST"], "/graphql", function (Request $request, Response $response) {
    global $users, $graphql_user_type;
    $debug = Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
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
                                    getIpRegionByIp(
                                        $users[intval($args["id"])]["ip"]
                                    );
                                return $users[intval($args["id"])] ?? null;
                            }
                        ],
                        "users" => [
                            "type" => Type::listOf($graphql_user_type),
                            "resolve" => function () use ($users) {
                                foreach ($users as &$user) {
                                    $user["ip_region"] = getIpRegionByIp($user["ip"]);
                                }

                                return $users;
                            }
                        ]
                    ]
                ])
            ]),
            "debug" => $debug
        ]);

        return $graphQLServer->processPsrRequest($request, $response, $response->getBody());
    } catch (Exception $e) {
        return $response->withStatus($e->getCode() ?? 500)->withJson([
            "errors" => [FormattedError::createFromException($e, $debug)]
        ]);
    }
});

$app->run();
