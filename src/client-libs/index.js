// config:
var reload_interval_ms = 10000;
var backend_url = './backend.php';

function generateRandomUsername() {
    let username = "";
    if (chance.bool()) {
        username += chance.first();
        if (chance.bool()) {
            username += chance.last();
        }
    } else {
        username += chance.word({syllables: 3})
    }
    if (chance.bool()) {
        username += chance.integer({min: 30, max: 99});
    }
    return username.toLowerCase();
}

var app = angular.module('app', ["ngSanitize"]);

// http://stackoverflow.com/a/20033625/79461
app.filter("nl2br", function () {
        return function (data) {
            if (!data) return data;
            return data.replace(/\r?\n/g, '<br/>');
        }
    }
);

// http://stackoverflow.com/a/20033625/79461
app.filter("autolink", function () {
    return function (data) {
        return Autolinker.link(data, {truncate: {length: 50, location: 'middle', newWindow: true}});
    }
});

app.controller('MailboxController', ["$scope", "$interval", "$http", "$log", function ($scope, $interval, $http, $log) {
    var self = this;

    self.updateUsername = function (username) {
        username = username.replace(/[@].*$/, ''); // remove part after "@"
        if (self.username != username) {
            // changed
            self.username = username;
            hasher.setHash(self.username);

            if (self.username.length > 0) {
                self.address = self.username; // use username until real address has been loaded
                self.updateMails();
            } else {
                self.address = null;
                self.mails = [];
            }
        }
        self.inputFieldUsername = self.address;
    };


    self.randomize = function () {
        let username = generateRandomUsername();
        self.updateUsername(username);
    };


    self.onHashChange = function (hash) {
        self.updateUsername(hash);
    };

    self.$onInit = function () {
        hasher.changed.add(self.onHashChange.bind(self));
        hasher.initialized.add(self.onHashChange.bind(self)); //add initialized listener (to grab initial value in case it is already set)
        hasher.init(); //initialize hasher (start listening for history changes)

        self.intervalPromise = $interval(function () {
            self.updateMails()
        }, reload_interval_ms);
    };

    self.updateMails = function () {
        if (self.username) {
            self.loadEmailsAsync(self.username);
        }
    };

    self.loadEmailsAsync = function (username) {
        $log.debug("updating mails for ", username);
        $http.get(backend_url, {params: {username: username, action: "get"}})
            .then(function successCallback(response) {
                $log.debug("received mails for ", username);
                if (response.data.mails) {
                    self.error = null;
                    self.mails = response.data.mails;
                    self.address = response.data.address;
                    self.username = response.data.username;
                } else {
                    self.error = "There is a problem with fetching the JSON. (JSON_ERROR). Reponse: " + response.data;
                }
            }, function errorCallback(response) {
                $log.error(response);
                self.error = "There is a problem with fetching the JSON. (HTTP_ERROR). Status: " + response.status;
            });
    };

    // Initial load
    self.updateMails()
}]);
