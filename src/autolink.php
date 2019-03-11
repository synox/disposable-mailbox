<?php

/**
 * Adapted from https://plugins.trac.wordpress.org/browser/sem-external-links/trunk/sem-autolink-uri.php
 * which is MIT/GPL licenced.
 *    Author: Denis de Bernardy & Mike Koepke
 *    Author URI: https://www.semiologic.com
 */
class AutoLinkExtension {
    public static function auto_link_text(string $string) {
        $string = preg_replace_callback(
            "/
            ((?<![\"'])                                     # don't look inside quotes
            (\b
            (                           # protocol or www.
                [a-z]{3,}:\/\/
            |
                www\.
            )
            (?:                         # domain
                [a-zA-Z0-9_\-]+
                (?:\.[a-zA-Z0-9_\-]+)*
            |
                localhost
            )
            (?:                         # port
                 \:[0-9]+
            )?
            (?:                         # path
                \/[a-z0-9:%_|~.-]*
                (?:\/[a-z0-9:%_|~.-]*)*
            )?
            (?:                         # attributes
                \?[a-z0-9:%_|~.=&#;-]*
            )?
            (?:                         # anchor
                \#[a-z0-9:%_|~.=&#;-]*
            )?
            )
            (?![\"']))
            /ix",
            function ($match) {
                $url = $match[0];
                $href = $url;

                if (false === strpos($href, 'http')) {
                    $href = 'http://' . $href;
                }
                return '<a href="' . $href . '" rel="noreferrer">' . $url . '</a>';
            },
            $string
        );

        return $string;
    }
}
