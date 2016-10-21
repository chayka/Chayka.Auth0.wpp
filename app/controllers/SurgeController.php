<?php

namespace Chayka\Auth0;

use Chayka\Helpers\LogHelper;
use Chayka\Helpers\Util;
use Chayka\WP\MVC\Controller;
use Chayka\Helpers\InputHelper;
use Chayka\WP\Helpers\JsonHelper;

class SurgeController extends Controller{

    public function init(){
        /**
         * Enable JSON params capture
         */
        InputHelper::captureInput();
    }

    public function tempLinkAction(){
        /**
         * Check JWT authentication
         */
        if(!JwtHelper::validate()){
            JsonHelper::respondError(JwtHelper::getMessage());
        }

        if(in_array('temp-access-link', JwtHelper::getScopes())){
            $tempLink = sprintf('%s://%s/api/surge/temp-access/pass/%s',
                Util::serverProtocol(), Util::serverName(), strrev(JwtHelper::getClientId()));
            JsonHelper::respond($tempLink);
        }

        JsonHelper::respondError('Need "temp-access-link" permission to acquire temporary access link');
    }

    public function tempAccessAction(){
        $pass = InputHelper::checkParam('pass')->required()->getValue();
        InputHelper::validateInput(true);
        $client = strrev($pass);

        echo "Access granted for client $client\n";
    }

}