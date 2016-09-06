// Vendor-Imports
import angular from "angular";
import uiRouter from "angular-ui-router";
import uiBootstrap from "angular-ui-bootstrap";
import "bootstrap/dist/css/bootstrap.css";
import "babel-polyfill";
import angularStickyfill from "angular-stickyfill";
import "angular-stickyfill/dist/angular-stickyfill.css";
import Mailbox from "./mailbox/mailbox";

// Interne Modul-Imports

angular.module('app', [
    uiRouter, uiBootstrap, Mailbox.name, angularStickyfill
])

    .constant('config', {
        'backend_url': 'http://dubgo.com/m2/backend.php',
        'reload_interval_ms': 10000
    })
