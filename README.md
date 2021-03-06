# Forex Simulator

## Assumptions

### Technical Assumptions

This project makes the following assumptions in order for Vagrant to be able to function correctly.

+ Port 8080 on your localhost is available for the application to listen on
+ Port 9090 on your localhost is available for the web service to listen on

If the ports are not available, the application will not function as expected.

### Design Assumptions

+ This project doesn't actually charge for the forex purchase, or award the buyer with any forex currency, it is just a simulator.

## Notes

+ The web service does not implement HTTPS, which should never happen in production.
+ The web service does not implement any kind of authentication, which is a bad idea in production.
+ The web service does not implement any kind of rate limiting, which could pose a problem depending on the particular use case.
+ The web service and web application would typically each have their own unique hostname rather than different ports in production.

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
cd forex
vagrant up
```
+ [Access the application in your web browser](http://127.0.0.1:8080)

All of the VM configuration resides in **Vagrantfile** and the provisioning resides in **provision/setup.sh**

## Receiving Emails

Emails are configured to go to `email@example.com` by default.

To test that you are able to receive emails

```
vagrant ssh
mysql -u root -p forex
```

You will be prompted to enter the root password for MySQL which is *rootpass*
I am aware that this is insecure, but its just a development environment, and not for production purposes.

Once you're at the MySQL prompt

```mysql
UPDATE order_emails SET email_address = 'your@email.com';
```

Obviously change `your@email.com` to something more suitable.

## Updating the exchange rates
```
vagrant ssh
cd /vagrant/service/app
sudo php -c /vagrant/provision/config/cli/cli.ini cli.php import rates
```
