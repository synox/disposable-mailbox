// Vendor-Imports
import angular from 'angular';
import uiRouter from 'angular-ui-router';
import uiBootstrap from 'angular-ui-bootstrap';
import 'bootstrap/dist/css/bootstrap.css';
import 'babel-polyfill';

// Interne Modul-Imports
import Mailbox from './mailbox/mailbox';

angular.module('app', [
    uiRouter, uiBootstrap, Mailbox.name
])

    .constant('config', {
        'backend_url': './backend.php',
        'reload_interval_ms': 10000
    })
