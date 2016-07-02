import angular from 'angular';
import Service from './mailbox.service'

export default angular.module('mailbox.service', [])
    .service('mailboxService', Service)

