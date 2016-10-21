<?php

namespace Chayka\Auth0;

use Chayka\Helpers\LogHelper;
use Chayka\WP\MVC\Controller;
use Chayka\Helpers\InputHelper;
use Chayka\WP\Helpers\JsonHelper;

class SurgeController extends Controller{

    public function init(){
        /**
         * Check JWT authentication
         */
        if(!JwtHelper::validate()){
            JsonHelper::respondError(JwtHelper::getMessage());
        }

        /**
         * Enable JSON params capture
         */
        InputHelper::captureInput();
    }

    public function tempLinkAction(){
        if(in_array('temp-access-link', JwtHelper::getScopes())){
            $tempLink = strrev(JwtHelper::getClientId());
            JsonHelper::respond($tempLink);
        }
        JsonHelper::respondError('Need "temp-access-link" permission to acquire temporary access link');
    }

}