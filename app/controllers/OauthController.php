<?php

namespace Chayka\Auth0;

use Auth0\SDK\Helpers\Cache\FileSystemCacheHandler;
use Auth0\SDK\JWTVerifier;
use Chayka\WP\MVC\Controller;
use Chayka\Helpers\InputHelper;
use Chayka\WP\Helpers\JsonHelper;

class OauthController extends Controller{

    public function init(){
        // NlsHelper::load('main');
        // InputHelper::captureInput();
    }

    public function tokenAction(){
        //	AclHelper::apiAuthRequired();
        $token = InputHelper::checkParam('token')->required()->getValue();

        InputHelper::validateInput(true);


		try{

            $verifier = new JWTVerifier([
                'valid_audiences' => ['https://chayka.io/api/oauth'],
                'authorized_iss' => ['https://chayka.eu.auth0.com'],
                'cache' => new FileSystemCacheHandler() // This parameter is optional. By default no cache is used to fetch the Json Web Keys.
            ]);

            $decoded = $verifier->verifyAndDecode($token);
			$payload = ['decoded' => $decoded];


			JsonHelper::respond($payload);

		}catch(\Exception $e){
			JsonHelper::respondException($e);
		}
    }
}