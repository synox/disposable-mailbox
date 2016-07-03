class NavbarController {
    /*@ngInject*/
    constructor(mailboxService, $stateParams, $rootScope) {
        this.$rootScope = $rootScope;
        this.mailboxService = mailboxService;
        this.$stateParams = $stateParams;

    }

    $onInit() {
        // the address is updated after loading the page. the value must be watched and upated later.
        this.$rootScope.$watch(
            ()=> this.mailboxService.getCurrentAddress(),
            (newValue, oldValue)=> {
                this.address = newValue;
            }
        );
        // load the temporary address (which is the username)
        this.address = this.mailboxService.getCurrentAddress();
    }

    gotoMailbox(username) {
        this.mailboxService.gotoMailbox(username);
    }

    gotoRandomAddress() {
        this.mailboxService.gotoRandomAddress();
    }
}

export default NavbarController;