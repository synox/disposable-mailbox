import angular from "angular";
import ngSanitize from "angular-sanitize";
import Autolinker from "autolinker";
import template from "./mail.html";
import "./mail.scss";


class MailController {
    /*@ngInject*/
    constructor() {
    }

}

export default angular.module('mailbox.inbox.mail', [ngSanitize])
    .component('mail', {
        template,
        controller: MailController,
        bindings: {
            mail: '<'
        }
    })
    // http://stackoverflow.com/a/20033625/79461
    .filter("nl2br", function () {
            return function (data) {
                if (!data) return data;
                return data.replace(/\r?\n/g, '<br/>');
            }
        }
    )
    // http://stackoverflow.com/a/20033625/79461
    .filter("autolink", function () {
            return function (data) {
                return Autolinker.link(data, {truncate: {length: 50, location: 'middle', newWindow: true}});
            }
        }
    ).name;