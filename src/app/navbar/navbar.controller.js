class NavbarController {
    /*@ngInject*/
    constructor(mailboxService, $stateParams, $rootScope) {
        this.$rootScope = $rootScope;
        this.mailboxService = mailboxService;
        this.$stateParams = $stateParams;

    }

    $onInit() {
        this.$rootScope.$watch(
            ()=> this.mailboxService.getCurrentAddress(),
            (newValue, oldValue)=> {
                this.address = newValue;
            }
        );
        this.address = this.mailboxService.getCurrentAddress();
    }

    openMailbox(username) {
        this.mailboxService.openMailbox(username);
    }

    createMailbox() {
        this.mailboxService.createMailbox();
    }
}

export default NavbarController;