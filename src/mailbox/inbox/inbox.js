import angular from 'angular';
import uiRouter from 'angular-ui-router';

import template from './inbox.html';
import controller from './inbox.controller';
import './inbox.scss';

export default angular.module('mailbox.inbox', [uiRouter])
    .component('inbox', {
        template, controller,
        bindings: {
            data: '<'
        }
    })