<?php

class AutoLinkExtension {
    static public function auto_link_text(string $string) {

        $string = preg_replace_callback("/
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
            }, $string);

        return $string;
    }

}
