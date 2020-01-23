# Hanaboso REST Bundle

[![Build Status](https://travis-ci.org/hanaboso/rest-bundle.svg?branch=master)](https://travis-ci.org/hanaboso/rest-bundle)
[![Coverage Status](https://coveralls.io/repos/github/hanaboso/rest-bundle/badge.svg?branch=master)](https://coveralls.io/github/hanaboso/rest-bundle?branch=master)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen)](https://img.shields.io/badge/PHPStan-level%208-brightgreen)
[![Downloads](https://img.shields.io/packagist/dt/hanaboso/rest-bundle)](https://packagist.org/packages/hanaboso/rest-bundle)


**Installation**
```
composer require hanaboso/rest-bundle
```

**Configuration**
```
rest:
    routes:
        '^/': ['json', 'xml']
    decoders:
        json: 'rest.decoder.json'
        xml: 'rest.decoder.xml'
    cors:
        '^/':
            origin: ['*']
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
            headers: ['Content-Type']
            credentials: TRUE
```
