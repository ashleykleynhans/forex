# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.hostname = "forex"
  config.vm.network "forwarded_port", guest: 8080, host: 8080
  config.vm.network "forwarded_port", guest: 9090, host: 9090
  config.vm.network "forwarded_port", guest: 3306, host: 3306
  config.vm.provision :shell, :path => "provision/setup.sh"
  config.vm.synced_folder ".", "/vagrant", group: "www-data", owner: "www-data", :mount_options => [ "dmode=777", "fmode=777" ]

  config.vm.provider "virtualbox" do |vb|
    vb.name = "Forex Simulator"
    vb.memory = "2048"
  end

  config.ssh.forward_agent = true
end
