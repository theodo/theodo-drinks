# Theodo Drinks

Theodo drinks manage the team drinks.

## Installation

*Requirements*:
 * nodejs
 * mongodb and the php pecl extension

First download the project and the vendors.

    computer:~ user $ git clone https://github.com/theodo/theodo-drinks && cd theodo-drinks
    computer:theodo-drinks user $ curl -s http://getcomposer.org/installer | php
    computer:theodo-drinks user $ php composer.phar install

Theodo Drinks needs lesscss installed with nodejs. By default the node module can be found in node_modules folder, try this to be sure it works:

    computer:theodo-drinks user $ node_modules/.bin/lessc --help
    usage: lessc source [destination]

If that is not the case, make sure nodejs is installed: ```which node```

Then copy ```app/config/parameters.yml.sample``` into ```app/config/parameters.yml``` and change values according to your environment.

Build the database:

    computer:theodo-drinks user $ app/console doctrine:mongodb:schema:create

You can eventually load fixtures:

    computer:theodo-drinks user $ app/console doctrine:mongodb:fixtures:load

Finally, run symfttpd ```php symfttpd.phar spawn -t``` and go to http://127.0.0.1:4042/app_dev.php
