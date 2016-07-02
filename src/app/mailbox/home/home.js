import angular from 'angular';
import uiRouter from 'angular-ui-router';

import template from './home.html';
import controller from './home.controller';
import './home.css';

export default angular.module('mailbox.home', [uiRouter])
    .component('home', {template, controller})