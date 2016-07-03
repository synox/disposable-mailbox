class MailboxController {
    /*@ngInject*/
    constructor($log, $interval, config, mailboxService) {
        this.$log = $log;
        this.$interval = $interval;
        this.config = config;
        this.mailboxService = mailboxService;
        this.loadingData = true;
        this.mails = [];
        this.address = null; // is set when mails are loaded
    }

    $onInit() {
        this.intervalPromise = this.$interval(() => this.loadMails(), this.config.reload_interval_ms);
        this.loadMails();

    }

    $onDestroy() {
        this.$log.debug("destroying controller");
        this.$interval.cancel(this.intervalPromise);
    }


    loadMails() {
        this.mailboxService.loadEmails(this.mailboxService.getCurrentUsername())
            .then(data => {
                this.mails = data.mails;
                this.address = this.mailboxService.getCurrentAddress();
                this.loadingData = false;
            });
    }

}

export default MailboxController;