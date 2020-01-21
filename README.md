# [Symfony](https://symfony.com) REST Bundle

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
```