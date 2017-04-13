// config:
var reload_interval_ms = 10000;
var backend_url = './backend.php';

function generateRandomUsername() {
    var username = "";
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

app.controller('MailboxController', ["$interval", "$http", "$log", function ($interval, $http, $log) {
    var self = this;

    self.backend_url = backend_url;

    self.updateUsername = function (username) {
        username = username.replace(/[@].*$/, ''); // remove part after "@"
        if (self.username !== username) {
            // changed
            self.username = username;
            hasher.setHash(self.username);

            if (self.username.length > 0) {
                self.address = self.username; // use username until real address has been loaded
                self.updateMails();
            } else {
                self.randomize();
            }
        }
        self.inputFieldUsername = self.username;
    };


    self.randomize = function () {
        self.updateUsername(generateRandomUsername());
    };


    self.onHashChange = function (hash) {
        self.updateUsername(hash);
    };

    self.$onInit = function () {
        hasher.changed.add(self.onHashChange.bind(self));
        hasher.initialized.add(self.onHashChange.bind(self)); //add initialized listener (to grab initial value in case it is already set)
        hasher.init(); //initialize hasher (start listening for history changes)

        $interval(self.updateMails, reload_interval_ms);
    };

    self.updateMails = function () {
        if (self.username) {
            self.loadEmailsAsync(self.username);
        }
    };

    self.loadEmailsAsync = function (username) {
        $http.get(backend_url, {params: {username: username}})
            .then(function successCallback(response) {
                if (response.data.mails) {
                    self.error = null;
                    self.mails = response.data.mails;
                    self.address = response.data.address;
                    self.username = response.data.username;
                    if (self.inputFieldUsername === self.username) {
                        self.inputFieldUsername = self.address;
                    }
                } else {
                    self.error = {
                        title: "JSON_ERROR",
                        desc: "The JSON from the response can not be read.",
                        detail: response
                    };
                    $log.error(response);
                }
            }, function errorCallback(response) {
                $log.error(response, this);
                self.error = {
                    title: "HTTP_ERROR",
                    desc: "There is a problem with loading the data. (HTTP_ERROR).",
                    detail: response
                };
            });
    };

    self.deleteMail = function (mail, index) {
        // instantly remove from frontend.
        self.mails.splice(index, 1);

        // remove on backend.
        var firstTo = Object.keys(mail.to)[0];
        $http.get(backend_url, {params: {username: firstTo, delete_email_id: mail.id}})
            .then(
                function successCallback(response) {
                    self.updateMails();
                },
                function errorCallback(response) {
                    $log.error(response, this);
                    self.error = {
                        title: "HTTP_ERROR",
                        desc: "There is a problem with deleting the mail. (HTTP_ERROR).",
                        detail: response
                    };
                });
    };

    // Initial load
    self.updateMails()
}]);
