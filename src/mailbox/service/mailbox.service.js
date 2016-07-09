class MailboxService {
    /*@ngInject*/
    constructor($http, $log, $state, $stateParams, config) {
        this.$http = $http;
        this.config = config;
    }

    loadEmails(username) {
        return this.$http.get(this.config.backend_url, {params: {username: username, action: "get"}})
            .then(response=> {
                    return response.data;
                }
            );
    }

}

export default MailboxService;