# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "avenuefactory/lamp"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 993, host: 9993

  config.vm.synced_folder "./", "/var/www/html", id: "vagrant-root",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]

    config.vm.provision "shell", inline: <<-SHELL
      echo updating...    && sudo apt-get -qq update
      echo installing...  &&  sudo apt-get -qq  -y install php5-imap
      sudo service apache2 restart

      # Install and Configure Dovecot (http://blog.tedivm.com/open-source/2014/03/building-an-email-library-testing-with-vagrant-dovecot-and-travis-ci/)
      #https://github.com/tedious/DovecotTesting/blob/master/resources/Scripts/Provision.sh
      if which dovecot > /dev/null; then
          echo 'Dovecot is already installed'
      else

        sudo mkdir /home/vagrant/Maildir
        sudo chown -R vagrant:vagrant /home/vagrant/Maildir
        sudo chmod a+rw /home/vagrant/Maildir

        echo 'Installing Dovecot'
        sudo apt-get -qq -y install dovecot-imapd
        sudo touch /etc/dovecot/local.conf
        sudo chmod go+rw /etc/dovecot/local.conf
        echo 'mail_location = maildir:/home/vagrant/Maildir' >> /etc/dovecot/local.conf
        echo 'disable_plaintext_auth = no' >> /etc/dovecot/local.conf
        echo 'mail_max_userip_connections = 10000' >> /etc/dovecot/local.conf
        sudo restart dovecot
      fi

      # Create user "test"
      if getent passwd test > /dev/null; then
          echo 'test already exists'
      else
          sudo useradd "test" -m -s /bin/bash
          echo "test:test"|sudo chpasswd
          echo 'User "test" created'
       fi

    SHELL


end
