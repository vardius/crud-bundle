Vardius - Crud Bundle
======================================

Installation
----------------
1. Download using composer
2. Enable the VardiusCrudBundle

### 1. Download using composer

Install the package through composer:

``` bash
    php composer.phar require vardius/crud-bundle:*
```

### 2. Enable the VardiusCrudBundle

Enable the bundle in the kernel:

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\CrudBundle\VardiusCrudBundle(),
        );
    }
```

Add to config.yml:

``` yml
    vardius_crud:
        db_driver: orm //available orm and propel
```
