# Theodo Drinks

Theodo drinks manage the team drinks.

## Installation

    computer:~ user $ git clone https://github.com/theodo/theodo-drinks
    computer:~ user $ curl -s http://getcomposer.org/installer | php
    computer:~ user $ php composer.phar install

    # Load users
    computer:~ user $ mongoimport -d theodo-drinks -c User data/user.json --jsonArray

    # Load drinks
    computer:~ user $ mongoimport -d theodo-drinks -c Drink data/drink.json --jsonArray

## Troubles

Why the added columns are not hydrated?
-> That's because the hydrator has to be refresh. Remove the hydrator file corresponding to the document and it should be fine then.
