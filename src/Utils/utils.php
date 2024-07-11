<?php


use Slim\App;
use Ramsey\Uuid\Uuid;
use App\Attribute\Route;
use Slim\Psr7\{
    Request,
    Response
};
use App\Security\AuthenticationService;


/**
 *
 * @param string $string
 * @return string
 *
 */
function getTextInput(string $string): string
{
    return htmlspecialchars($string);
}


function getFileExt(array $file)
{
    return strtolower(substr(strrchr($file['name'], '.'), 1));
}


/**
 * @throws Exception
 **/
function callGet($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    curl_close($ch);

    if ($response) {
        return json_decode($response, true);
    } else {
        throw new Exception("No response from the API : " . curl_error($ch));
    }
}

function callPost($url, $body)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);
    return json_decode($response, true);
}

function getBody(): array
{
    $json = json_encode(file_get_contents("php://input"));
    $array = json_decode($json);
    return (array) json_decode($array);
}

function throwError(int $code, $title, $message = "", array $option = []): void
{
    header("HTTP/1.1 $code $title : $message");
    //http_response_code($code);
    $result = ["code" => $code, "message" => $title, "debug" => $option != [] ? $message . $option[0] : $message];
    //echo writeBody(null,$code,$title,$option != [] ? $message . $option[0] : $message);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}

function writeBody($data): string|null
{
    $res = json_encode($data, JSON_UNESCAPED_UNICODE, 500);
    return $res ? $res : null;
}

function generateUniqueIdentifier($length = 10): string
{
    $uuid = Uuid::uuid4()->toString();
    return substr(str_replace('-', '', $uuid), 0, $length);
}

function generateRandomSerialNumber(int $val)
{
    $letter = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
    ];
    $matri = [];
    for ($i = 0; $i < $val; $i++) {
        $il = array_rand($letter);
        array_unshift($matri, $letter[$il]);
    }

	return implode("", $matri);
}

function generateNumber(int $val) : string
{
	$numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
	$number = [];
	for ($i = 0; $i < $val; $i++) {
		$il = array_rand($numbers);
		array_unshift($number, $numbers[$il]);
	}

	return implode("", $number);
}

function generateRandomPassword() : string
{
    $chars = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        '&', '#', '{', '(', '[', '-', '_', 'ç', '@', ')', ']', '=', '}', '$', '£', 'µ', '%', '!', '§', ':', '/',
        '?'
    ];
    $rand = rand(8, 14);

    $pass = [];

    for ($i = 0; $i < $rand; $i++) {
        $char = array_rand($chars);
        array_unshift($pass, $chars[$char]);
    }

    return implode("", $pass);
}

function password_encrypt(string $string): string
{
    //$k1 = sha1($string);
    $k2 = md5(sha1($string));
    $k3 = sha1(md5($k2));

    for ($i = 0; $i < 8; $i++) {
        $k3 = sha1(md5($k3));
    }

    return sha1($k3);
}

function getIpAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getUrlParams(string $url): array|null
{
    $urlParams = explode("?", $url);
    $queryParams = [];
    $nQP = [];
    if (isset($urlParams[1])) {
        $queryParams = explode("&", $urlParams[1]);
        foreach ($queryParams as $queryParam) {
            $qPArray = explode("=", $queryParam);
            $nQP[$qPArray[0]] = $qPArray[1];
        }
    }
    return  $nQP;
}

function getPagingOffset($page, $size): int
{
    return ceil((($page + 1) - 1) * $size);
}

function isGuardAuthorized(array $methodGuards, string $requestGuard) : bool
{
	if (in_array($requestGuard, $methodGuards))
		return true;
	return false;
}


/**
 * @throws ReflectionException
 * @throws Exception
 */
function registerController(App $app, string $controller): void
{
    $class = new ReflectionClass($controller);

    $attributes = $class->getAttributes(Route::class);
    $prefix = "";
    if (!empty($attributes)) {
        $prefix = $attributes[0]->newInstance()->getPath();
    }

    foreach ($class->getMethods() as $method) {
        $routesAttribute = $method->getAttributes(Route::class);
        if (empty($routesAttribute)) {
            continue;
        }

        foreach ($routesAttribute as $route) {
            /** @var Route $r */
            $r = $route->newInstance();

            $httpMethod = $r->getMethod();
            $app->$httpMethod($prefix . $r->getPath(), function (Request $request, Response $response, array $args) use ($method, $controller, $r) {

				/*
				 * Authentication section start
				 * */
				$guard = $r->getGuard();
				if ($guard != ["NONE"]){
					$authorization = isset($request->getHeaders()['Authorization']) ? $request->getHeaders()['Authorization'] : throw new Exception("Accès refusé. Veullez vous authentifier");
					$token = explode("Bearer ",$authorization[0])[1];
					$valid = (new AuthenticationService())->validateToken($token);

					if ($valid)
						!isGuardAuthorized($guard, $valid['role']) ?? throw new Exception("Accès refusé");
					else
						throw new Exception("Token invalide, veullez vous authentifier");
				}

				/*
				 * Authentication section end
				 * */

                $body = (array) json_decode($request->getBody()->getContents());
                $queryParams = getUrlParams($request->getServerParams()['REQUEST_URI']);

                $params = [];
                foreach ($method->getParameters() as $param) {
                    $params[] = match ($param->getName()) {
                        "request" => $request,
                        "response" => $response,
                        default => $body[$param->getName()] ?? $args[$param->getName()] ?? $queryParams[$param->getName()] ?? $param->getDefaultValue() ?? null,
                    };
                }

                $responseController = call_user_func_array([new $controller(), $method->getName()], $params);

                $response->getBody()->write(writeBody($responseController));
				return $response;
            });
        }
    }
}
