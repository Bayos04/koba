<?php

namespace App\Security;

use App\Config\Config;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;

class AuthenticationService
{
	private static Validator $validator;

	private static Parser $parser;

	private static Config $config;

	public function __construct()
	{
		self::$validator = new Validator();
		self::$parser = new Parser(new JoseEncoder());
		self::$config = new Config();
	}

	public function generateAuthToken($data) : string
	{
		$tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
		$algorithm = new Sha256();
		$signingKey = InMemory::plainText("YOUR_ENCRYPTION_KEY");

		$token = $tokenBuilder->issuedBy(self::$config::SERVICE_HOST)
			//				  ->permittedFor("http://facturer.loc/")
							  ->withClaim('identifier', $data['identifier'])
							  ->withClaim('user', $data)
							  ->getToken($algorithm, $signingKey);

		return $token->toString();
	}

	public function getTokenInformation(string $token)
	{
		$token = self::$parser->parse($token);

		if ($token instanceof Plain) {
			return $token->claims()->get('user');
		} else {
			return null;
		}
	}


	public function validateToken(string $token)
	{
		$parsedToken = self::$parser->parse($token);

		$valid = self::$validator->validate($parsedToken, new IssuedBy(self::$config::SERVICE_HOST));

		if ($valid)
			return $this->getTokenInformation($token);
		else
			return false;
	}

}