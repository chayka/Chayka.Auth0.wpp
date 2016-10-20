<?php
/**
 * Class CoreTest
 *
 * @package
 */

/**
 * Core test case.
 */
class CoreTest extends WP_UnitTestCase {

    /**
     * A single example test.
     */
    function test_application_enabled() {
        // Replace this with some actual testing code.
        $this->assertTrue( class_exists('Chayka\Auth0\Plugin'), 'Plugin class loaded' );
        $this->assertInstanceOf( 'Chayka\Auth0\Plugin', Chayka\Auth0\Plugin::getInstance(), 'Plugin instance created' );
    }
}