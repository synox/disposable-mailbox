import angular from 'angular';
import uiRouter from 'angular-ui-router';

import template from './navbar.html';
import controller from './navbar.controller';
import './navbar.css'

let navbarModule = angular.module('navbar', [uiRouter])
    .component('navbar', {
        template,
        controller
    });

export default navbarModule;