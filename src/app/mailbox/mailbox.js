import angular from 'angular';
import uiRouter from 'angular-ui-router';

import Service from './service/service';
import Home from './home/home';
import Inbox from './inbox/inbox';

let module = angular.module('mailbox', [uiRouter, Inbox.name, Service.name, Home.name])
    .config(/*@ngInject*/($stateProvider, $urlRouterProvider) => {

        $urlRouterProvider.otherwise('/');

        $stateProvider.state('home', {
            url: "/",
            component: 'home'
        });

        $stateProvider.state('inbox', {
            url: '/:username',
            component: 'inbox'
        });
    });

export default module;