# Forex Simulator

## Setup Instructions

This project requires Vagrant to provision your development environment.

+ [Download VirtualBox](https://www.virtualbox.org/wiki/Downloads)
+ [Download Vagrant](https://www.vagrantup.com/downloads.html)
+ Clone the repository from Github
```
git clone https://github.com/ashleykleynhans/forex.git
```
+ Start the Vagrant Server (This process takes a while, it has to download a few things, so please wait for it to complete before proceeding to the next step)
```
vagrant up
```
+ [Access the application in your web browser](http://127.0.0.1:8080)

All of the VM configuration resides in **Vagrantfile** and the provisioning resides in **provision/setup.sh**
