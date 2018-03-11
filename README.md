# Magento 2 Have I Been Pwned Validator
This module adds a validator which checks if the submitted password is found in public databases using the `Have I Been Pwned?` service.

## Security
There are no security drawbacks, because there are no actual passwords being submitted over the internet. This is possible by hashing the password using the `SHA-1` algorithm and request all hashes in the `Have I been Pwned?` databases starting with the first 5 characters of the password hash. This resultset contains a list of hashes and the amount of occurrences.

This way the password stays inside the Magento process.

## Installation
```
composer require timpack/magento2-module-pwned-validator
bin/magento setup:upgrade
``` 

## Configuration
You can configure the threshold of the validator, at which count of occurrences in the resultset the password should be considered insecure/invalid.
This configuration can be found at:

`Stores -> Configuration -> Customer -> Customer Configuration -> Pwned Validator -> Minimum amount of matches`

## Credits
This module was heavily inspired by Valorin's Pwned validator written for Laravel: [valorin/pwned-validator](https://github.com/valorin/pwned-validator)