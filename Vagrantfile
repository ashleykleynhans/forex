# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "mukuru-test"
  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 3306
  config.vm.network "forwarded_port", guest: 8500, host: 8500
  config.vm.provision :shell, :path => "vagrant/setup.sh"
  config.vm.synced_folder ".", "/vagrant", group: "www-data", owner: "www-data", :mount_options => [ "dmode=777", "fmode=777" ]

  config.vm.provider "virtualbox" do |vb|
    vb.name = "Mukuru Test"
    vb.memory = "2048"
  end

  config.ssh.forward_agent = true
end
