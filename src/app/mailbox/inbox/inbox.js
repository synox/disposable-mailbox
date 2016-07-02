import angular from 'angular';
import uiRouter from 'angular-ui-router';

import template from './inbox.html';
import controller from './inbox.controller';
import './inbox.css';
import Mail from './mail/mail'

export default angular.module('mailbox.inbox', [uiRouter, Mail.name])
    .component('inbox', {
        template,
        controller,
        bindings: {
            data: '<'
        }
    })