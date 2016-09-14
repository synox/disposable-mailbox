import angular from "angular";
import "./header.scss";

class HeaderController {
    /*@ngInject*/
    constructor($log) {
        this.$log = $log;
        this.inputFieldUsername = "";
    }

    $onInit() {
        this.inputFieldUsername = this.address;
    }

    $onChanges(changes) {
        if (changes.address) {
            this.inputFieldUsername = this.username;
            this.address = this.address;
        }
    }

    gotoMailbox(username) {
        this.onChangeUsername({
            $event: {
                username: username
            }
        });
    }

    randomize() {
        this.onGotoRandom();
    }
}

const HeaderComponent = {
    bindings: {
        address: '<',
        username: '<',
        mailcount: '<',
        onChangeUsername: '&',
        onGotoRandom: '&'
    },
    controller: HeaderController,
    template: `
    <div class="nav-container">
    <div class="container">
        <nav class="navbar navbar-light">
            <a class="navbar-brand"><span class="octicon-inbox"></span>
                &nbsp;
                {{$ctrl.address}}
                &nbsp;
                <span ng-if="$ctrl.mailcount" class="tag tag-pill tag-default">{{$ctrl.mailcount}}</span>
            </a>


            <ul class="nav navbar-nav">

                <button type="button nav-link" class="btn btn-outline-primary pull-xs-right"
                        ng-click="$ctrl.randomize()">
                    <span class="glyphicon glyphicon-random"></span>&nbsp;
                    randomize
                </button>

                <form class="form-inline pull-xs-right" ng-submit="$ctrl.gotoMailbox($ctrl.inputFieldUsername)">
                    <input ng-model="$ctrl.inputFieldUsername"
                           placeholder="username"
                           type="text" class="form-control"/>
                    <button type="submit" class="btn btn-outline-success">login</button>
                </form>

            </ul>
        </nav>
    </div>
</div>
  `
};


export default angular
    .module('header', [])
    .component('header', HeaderComponent)
    .name;