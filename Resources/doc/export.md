Vardius - Crud Bundle
======================================

Export action
----------------
1. [Enable export action](#enable-export-action)
2. [Install dependencies](#install-dependencies)
3. [CSV export data](#csv-export-data)
4. [Update views](#update-views)

### Enable export action

Simply tell controller to call method `addAction`

##### YML
``` yml
services:
    app.crud_controller:
        class: %vardius_crud.controller.class%
        tags:
            - { name: vardius_crud.controller }
        factory_method: get
        factory_service: vardius_crud.controller.factory
        arguments: ['AppMainBundle:Product', /products, '@app_main.product.list_view', '@app_main.form.type.product']
        calls:
            - [addAction, [export, '@vardius_crud.action_export']]
```

##### XML
``` xml
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>

        <call method="addAction">
            <argument>export</argument>
            <argument type="service" id="vardius_crud.action_export"/>
        </call>
        
        <tag name="vardius_crud.controller" />
    </service>
```

You can add more actions to this enabled by default or provided by you.

### 1. Install dependencies

#### 1. Download using composer

Install the package through composer:

``` bash
    php composer.phar require knplabs/knp-snappy-bundle:1.2.*@dev
```

#### 2. Enable the KnpSnappyBundle

Enable the bundle in the kernel:

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
        );
    }
```

Add to config.yml path to wkhtmltopdf lib
Example:

``` yaml
    # app/config/config.yml
    knp_snappy:
        pdf:
            enabled:    true
            binary:     /usr/local/bin/wkhtmltopdf
            options:    []
```

### CSV export data

In case of export data to CSV file implement toArray() method in your entity class or override controller methods
You can also configure actions to return `json`, or `xml` then instead of `toArray` method `serialzier` will serialize your data.

Remember to add as a last argument your **Controller Class** when defining controller service

##### YML
``` xml    
services:
    app.product_controller:
        class: App\DemoBundle\Controller\ProductController
        arguments: ['AppMainBundle:Product', /products, '@app_main.product.list_view', '@app_main.form.type.product', null, null, 'App\DemoBundle\Controller\ProductController']
        <!-- ... -->
```
##### XML
``` xml    
    <service id="app.product_controller" class="App\DemoBundle\Controller\ProductController" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>
        <argument>NULL</argument>
        <argument>NULL</argument>
        <argument>App\DemoBundle\Controller\ProductController</argument>
    <!-- ... -->
    </service>
```

``` php
    class ProductController extends CrudController
    {
        /**
         * Returns array from entity object
         * Used in export action
         *
         * @param $entity
         * @return array
         */
        public function getRow($entity)
        {
            return [
                $entity->getId(),
                $entity->getName(),
            ];
        }
    
        /**
         * Returns headers for export action (CSV file case)
         *
         * @return array
         */
        public function getHeaders()
        {
            return [
                'ID',
                'Name',
            ];
        }
    }
```

### Update views

Export action can export single object or whole list, when exporting to `pdf` it use `show` and `list` actions.
To hide the user interface export action return to views parameter `ui` set to `false`.
If you want to hide **button** or any other element make sure it will not display when `ui` parameter is `false`
Example:

```twig
{% if ui %}
    <a href="{{ path(action.path, action.parameters) }}" class="btn btn-default" role="button">
        {{ action.name|trans }}
    </a>
{% endif %}
```
