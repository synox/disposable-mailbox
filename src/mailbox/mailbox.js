import angular from 'angular';
import uiRouter from 'angular-ui-router';
import Service from './service/mailbox.service'
import Inbox from './inbox/inbox';
import Mail from './mail/mail'

let module = angular.module('mailbox', [uiRouter, Inbox.name, Mail.name])
    .config(/*@ngInject*/($stateProvider, $urlRouterProvider) => {

        $urlRouterProvider.otherwise('/');

        $stateProvider.state('inbox', {
            url: '/:username',
            component: 'inbox'
        });
    })
    .service('mailboxService', Service);

export default module;