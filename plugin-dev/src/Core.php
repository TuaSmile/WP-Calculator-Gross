<?php
namespace NetGrossCalc;
use Timber\Timber;

class Core
{
    /**
     * Initializes the Core class, setting up necessary configurations and registering components.
     *
     * This function is responsible for initializing the following:
     * - Timber: Sets the directory for Timber templates.
     * - AcfConfig: Initializes ACF (Advanced Custom Fields) configurations.
     * - TwigFilters: Registers custom Twig filters.
     * - Assets: Registers and enqueues frontend assets.
     * - CPT: Registers custom post types.
     * - RestApi: Registers REST API routes.
     * - Calculator: Registers the calculator shortcode.
     *
     * @return void
     */
    public static function init()
    {
        Timber::$dirname = "../views";
        AcfConfig::init();
        TwigFilters::init();
        Assets::init();
        CPT::init();
        RestApi::registerRoutes();
        Calculator::registerShortcode();
    }
}
