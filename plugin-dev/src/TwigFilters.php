<?php
namespace NetGrossCalc;

class TwigFilters
{
    public static function init() {
        add_filter('timber/twig', [__CLASS__, 'addTranslateFilter']);
    }

    /**
    * Adds a 'translate' filter to the Twig environment.
    *
    * This filter is used to translate text using the WordPress __() function.
    * The NGC_TDOMAIN constant is used as the text domain for the translations.
    *
    * @param \Twig\Environment $twig The Twig environment to add the filter to.
    *
    * @return \Twig\Environment The modified Twig environment with the new filter added.
    */
    public static function addTranslateFilter($twig) {
        $twig->addFilter(new \Twig\TwigFilter('translate', function ($text) {
            return __($text, NGC_TDOMAIN);
        }));

        return $twig;
    }
}
