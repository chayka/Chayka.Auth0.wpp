<?php

namespace Chayka\Auth0;

use Auth0\SDK\JWTVerifier;

/**
 * Class JwtHelper enables JWT validation and provides access to data stored in JWT.
 * JWT is passed in HTTP request header:
 *      authorization: Bearer eyJ0eXAiOiJKV....
 */
class JwtHelper{

    /**
     * Valid Auth0 audience (registered API) identifier
     */
    const AUTH0_AUDIENCE = 'https://chayka.io/';

    /**
     * Auth0 audience (registered API) client secret that is used for symmetrical HS256 encryption/decryption.
     */
    const AUTH0_CLIENT_SECRET = 'WPvLVZ07nNd7s1CmfZZFnD5aOEGsVOAQ';

    /**
     * Decoded valid JWT
     *
     * @var array|null
     */
    protected static $jwt = null;

    /**
     * Error message in case of invalid token
     *
     * @var string
     */
    protected static $message = '';

    /**
     * Extract encoded token from HTTP request header
     *
     * @return string
     */
    protected static function getTokenFromHeaders(){
        $token = '';
        if(isset($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/^\s*Bearer\s+(.*)$/i', $_SERVER['HTTP_AUTHORIZATION'], $m)){
            $token = $m[1];
        }
        return $token;
    }

    /**
     * Check if request passed a valid token, decode it and store for future use.
     *
     * @return bool
     */
    public static function validate(){
        if(!self::$jwt){
            $token = self::getTokenFromHeaders();
            if($token){
                try{
                    $verifier = new JWTVerifier([
                        'valid_audiences' => [self::AUTH0_AUDIENCE],
                        'client_secret' => base64_encode(self::AUTH0_CLIENT_SECRET)
                    ]);

                    self::$jwt = $verifier->verifyAndDecode($token);

                }catch(\Exception $e){
                    self::$message = $e->getMessage();
                }
            }else{
                self::$message = 'No OAuth token detected';
            }
        }

        return !!self::$jwt;
    }

    /**
     * Get validation error
     *
     * @return string
     */
    public static function getMessage(){
        return self::$message;
    }

    /**
     * Get available scopes passed in JWT
     *
     * @return string[]
     */
    public static function getScopes(){
        return self::$jwt && isset(self::$jwt->scope) ? explode(' ', self::$jwt->scope) : [];
    }

    /**
     * Strip client id from token
     *
     * @return string
     */
    public static function getClientId(){
        return self::$jwt && isset(self::$jwt->sub) ? preg_replace('/@clients$/', '', self::$jwt->sub) : '';
    }
}