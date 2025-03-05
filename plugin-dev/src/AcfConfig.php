<?php
namespace NetGrossCalc;

class AcfConfig
{
    public static $acfPath = "";

    public static function init()
    {
        static::$acfPath = \plugin_dir_path( __FILE__ ) . "acf-json";

        add_filter('acf/settings/save_json', [__CLASS__, 'jsonSavePoint']);
        add_filter('acf/settings/load_json', [__CLASS__, 'jsonLoadPoint']);
    }

    public static function jsonSavePoint($path) {
        return static::$acfPath;
    }

    public static function jsonLoadPoint($paths) {
        return [
            static::$acfPath
        ];
    }
}
