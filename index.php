<?php
session_start();

require 'vendor/autoload.php';
require_once 'src/Utils/utils.php';

use Slim\Factory\AppFactory;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpMethodNotAllowedException;
use App\Controller\UserController;

header("Access-Control-Allow-Origin: *");
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
   exit(0);
}

$app = AppFactory::create();

try {
    registerController($app, UserController::class);

	$app->run();

} catch (HttpNotFoundException $e) {
    throwError(404, "Not Found", $e->getMessage());
}catch (\PDOException $e) {
    throwError(500, "Database server error", $e->getMessage());
} catch (TypeError | Exception $e) {
    throwError(406, "Internal Error", $e->getMessage());
}
