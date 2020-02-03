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
        ^/: ['json', 'xml']
    decoders:
        json: 'rest.decoder.json'
        xml: 'rest.decoder.xml'
    cors:
        ^/:
            origin: ['*']
            methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
            headers: ['Content-Type']
            credentials: TRUE
    security:
        ^/:
            X-Frame-Options: 'sameorigin'
            X-XSS-Protection: '1; mode=block'
            X-Content-Type-Options: 'nosniff'
            Content-Security-Policy: "default-src * data: blob: 'unsafe-inline' 'unsafe-eval'"
            Strict-Transport-Security: 'max-age=31536000; includeSubDomains; preload'
            Referrer-Policy: 'strict-origin-when-cross-origin'
            Feature-Policy: "accelerometer 'self'; ambient-light-sensor 'self'; autoplay 'self'; camera 'self'; cookie 'self'; docwrite 'self'; domain 'self'; encrypted-media 'self'; fullscreen 'self'; geolocation 'self'; gyroscope 'self'; magnetometer 'self'; microphone 'self'; midi 'self'; payment 'self'; picture-in-picture 'self'; speaker 'self'; sync-script 'self'; sync-xhr 'self'; unsized-media 'self'; usb 'self'; vertical-scroll 'self'; vibrate 'self'; vr 'self'"
            Expect-CT: 'max-age=3600'
```
