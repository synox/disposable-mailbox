import angular from "angular";
import template from "./list.html";
import "./list.scss";


class ListController {
    /*@ngInject*/
    constructor($log) {
        this.$log = $log;

        // @Input:
        // this.mails = [];
        // this.username = null;
        // this.address = null;
    }
}


export default angular.module('mailbox.inbox', [])
    .component('inbox', {
        template, controller: ListController,
        bindings: {
            address: '<',
            username: '<',
            mails: '<',
            state: '<'
        }
    })
    .name;