<?php

namespace Chayka\Auth0;

use Chayka\WP;

class Plugin extends WP\Plugin{

    /* chayka: constants */
    
    /**
     * Application singleton instance
     *
     * @var null
     */
    public static $instance = null;

    /**
     * An array of dependencies required to run this application
     *
     * @var array
     */
    protected static $requiredClasses = [
    //    "Chayka\\WP\\Plugin" => 'Chayka.Core plugin is required in order for Chayka.Auth0 to work properly',
    ];

    /**
     * Application init function
     */
    public static function init(){
        if(!static::$instance && self::areRequiredClassesAvailable()){
            static::$instance = $app = new self(__FILE__, [
                'oauth',
                /* chayka: init-controllers */
            ]);
	        $app->dbUpdate([
                /* chayka: db-update */
            ]);
	        $app->addSupport_UriProcessing();
	        $app->addSupport_ConsolePages();


            /* chayka: init-addSupport */
        }
    }


    /**
     * Register your action hooks here using $this->addAction();
     */
    public function registerActions() {
    	/* chayka: registerActions */
    }

    /**
     * Register your action hooks here using $this->addFilter();
     */
    public function registerFilters() {
		/* chayka: registerFilters */
    }

    /**
     * Register scripts and styles here using $this->registerScript() and $this->registerStyle()
     *
     * @param bool $minimize
     */
    public function registerResources($minimize = false) {
        $this->registerBowerResources(true);

        $this->populateResUrl('Chayka.Auth0');

        $this->setResSrcDir('src/');
        $this->setResDistDir('dist/');

		/* chayka: registerResources */
    }

    /**
     * Routes are to be added here via $this->addRoute();
     */
    public function registerRoutes() {
        $this->addRoute('default');
    }

    /**
     * Registering console pages
     */
    public function registerConsolePages(){
        /* chayka: registerConsolePages */
    }
}