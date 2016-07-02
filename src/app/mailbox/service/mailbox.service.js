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

    openMailbox(username) {
        username = MailboxService.cleanUsername(username);
        this.setCurrentAddress(username);
        this.$state.go('inbox', {username: username});
    }

    deleteMail(id) {
        this.$log.info('deleting mails with id ' + id);
        return this.$http.post(this.config.backend_url, {
            params: {
                id: id,
                username: this.getCurrentUsername(),
                action: "delete"
            }
        });
    }

    loadEmails(username) {
        return this.$http.get(this.config.backend_url, {params: {username: username, action: "get"}})
            .then(response=> {
                    this.setCurrentAddress(response.data.address);
                    return response.data;
                }
            );
    }

    static cleanUsername(username) {
        return username.replace(/[@].*$/, '');
    }

    createMailbox() {
        let username = this.generateRandomUsername();
        this.openMailbox(username);
    }

    generateRandomUsername() {
        let username = null;
        if (this.chance.bool()) {
            username = this.chance.word({syllables: 3});
        } else {
            username = this.chance.first();
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

    setCurrentAddress(address) {
        this.address = address;
    }

    getCurrentAddress() {
        return this.address
    }

}

export default MailboxService;