<?php

namespace Chayka\Auth0;

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

    /**
     * Generate temp access link.
     * Since in real world implementation we'll have clientId <-> tempAccessLink relation,
     * our temp link consists of reversed client id for simplicity.
     */
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

    /**
     * Endpoint that is being accessed using generated temp link.
     * In this demo restoring client id by reversing pass.
     */
    public function tempAccessAction(){
        $pass = InputHelper::checkParam('pass')->required()->getValue();
        InputHelper::validateInput(true);
        $client = strrev($pass);

        echo "Access granted for client $client\n";
    }

}