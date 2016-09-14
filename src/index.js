import angular from "angular";
import "bootstrap/scss/bootstrap.scss";
import "babel-polyfill";
import angularStickyfill from "angular-stickyfill";
import "angular-stickyfill/dist/angular-stickyfill.css";
import {cleanUsername, generateRandomUsername} from "./mailbox/util";
import hasher from "hasher";
import Header from "./mailbox/header/header";
import List from "./mailbox/list/list";
import Mail from "./mailbox/mail/mail";

// config:
const reload_interval_ms = 10000;
const backend_url = './backend.php';

class AppController {
    /*@ngInject*/
    constructor($http, $log, $interval) {
        this.$interval = $interval;
        this.$http = $http;
        this.$log = $log;
        this.address = null;
        this.username = null;
        this.mails = [];
    }

    $onInit() {
        hasher.changed.add(this.onHashChange.bind(this));
        hasher.initialized.add(this.onHashChange.bind(this)); //add initialized listener (to grab initial value in case it is already set)
        hasher.init(); //initialize hasher (start listening for history changes)

        this.intervalPromise = this.$interval(() => this.updateMails(), reload_interval_ms);
    }

    $onDestroy() {
        this.$interval.cancel(this.intervalPromise);
    }

    onHashChange(hash) {
        this.updateUsername(hash);
    }

    onChangeUsername({username}) {
        this.updateUsername(username);
    }

    onGotoRandom() {
        let username = generateRandomUsername();
        this.updateUsername(username);
    }

    loadEmails(username) {
        return this.$http.get(backend_url, {params: {username: username, action: "get"}})
            .then(response=> {
                    return response.data;
                }
            );
    }

    loadEmailsAsync(username) {
        this.$log.debug("updating mails for ", username);
        this.loadEmails(this.username).then(data=> {
            this.mails = data.mails;
            this.address = data.address;
            this.username = data.username;
            this.$log.debug("received mails for ", username);
        });
    }

    updateMails() {
        if (this.username) {
            this.loadEmailsAsync(this.username);
        }
    }

    updateUsername(username) {
        this.username = cleanUsername(username);
        if (this.username.length > 0) {
            hasher.setHash(this.username);
            this.address = this.username; // use username until real address has been loaded
            this.updateMails();
        } else {
            this.address = null;
            this.mails = [];
        }
    }

}


angular.module('app', [
    List, Mail, angularStickyfill, Header
])

    .component('app', {
        template: `
            <header
                username="$ctrl.username"
                address="$ctrl.address"
                mailcount="$ctrl.mails.length"
                on-change-username="$ctrl.onChangeUsername($event)" 
                on-goto-random="$ctrl.onGotoRandom($event)">
            </header>
            
            <inbox
                mails="$ctrl.mails"
                username="$ctrl.username"
                address="$ctrl.address"
                state="$ctrl.state">
            </inbox>
        `,
        controller: AppController
    });

