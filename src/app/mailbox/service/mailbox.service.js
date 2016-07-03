import Chance from 'chance';

class MailboxService {
    /*@ngInject*/
    constructor($http, $log, $state, $stateParams, config) {
        this.name = 'mailboxService';
        this.$http = $http;
        this.$log = $log;
        this.$state = $state;
        this.$stateParams = $stateParams;
        this.config = config;
        this.address = null;
        this.chance = new Chance();
    }

    gotoMailbox(username) {
        username = MailboxService.cleanUsername(username);
        this.address = username; // use username until real address has been loaded
        this.$state.go('inbox', {username: username});
    }

    loadEmails(username) {
        return this.$http.get(this.config.backend_url, {params: {username: username, action: "get"}})
            .then(response=> {
                    this.address = response.data.address;
                    return response.data;
                }
            );
    }

    static cleanUsername(username) {
        return username.replace(/[@].*$/, '');
    }

    gotoRandomAddress() {
        let username = this.generateRandomUsername();
        this.gotoMailbox(username);
    }

    generateRandomUsername() {
        let username = null;
        if (this.chance.bool()) {
            username = this.chance.word({syllables: 3});
        } else {
            username = this.chance.first(); // first name
        }
        if (this.chance.bool()) {
            username += this.chance.integer({min: 50, max: 99});
        }
        if (this.chance.bool()) {
            username += this.chance.tld();
        }
        username = username.toLowerCase();
        return username;
    }

    getCurrentUsername() {
        return this.$stateParams.username;
    }


    getCurrentAddress() {
        return this.address
    }

}

export default MailboxService;