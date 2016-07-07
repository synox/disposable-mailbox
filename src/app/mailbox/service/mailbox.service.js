import phonetic from 'phonetic';

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
        let username = '';

        phonetic.generate({syllables: 3});

        if (Math.random() >= 0.5) {
            username += phonetic.generate({syllables: 3});
        } else {
            username += phonetic.generate({syllables: 2});
            username += phonetic.generate({syllables: 2});
        }
        if (Math.random() >= 0.5) {
            username += this.getRandomInt(30, 99);
        }
        return username.toLowerCase();
    }

    getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min)) + min;
    }

    getCurrentUsername() {
        return this.$stateParams.username;
    }


    getCurrentAddress() {
        return this.address
    }

}

export default MailboxService;