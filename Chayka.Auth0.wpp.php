<?php
/**
 * Plugin Name: Chayka.Auth0
 * Plugin URI: git@github.com:chayka/Chayka.Auth0.wpp.git
 * Description: WP plugin that will implement API endpoint to enable Auth0 OAuth API authorization
 * Version: 0.0.1
 * Author: Boris Mossounov <borix@tut.by>
 * Author URI: https://anotherguru.me
 * License: Proprietary
 */

/**
 * Requiring autoload
 */
if(!class_exists('Chayka\Auth0\Plugin')){
    require_once __DIR__.'/vendor/autoload.php';
}
if(!class_exists('Chayka\WP\Plugin')){
    add_action( 'admin_notices', function () {
?>
    <div class="error">
        <p>Chayka.Core plugin is required in order for Chayka.Auth0 to work properly</p>
    </div>
<?php
	});
}else{

    /**
     * Initializing App
     */
    add_action('init', ['Chayka\Auth0\Plugin', 'init']);

    /**
     * Initializing Sidebar Widgets if present
     */
    class_exists('Chayka\Auth0\SidebarWidget');
}