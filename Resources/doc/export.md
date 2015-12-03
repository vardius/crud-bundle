Vardius - Crud Bundle
======================================

Export action
----------------
1. CSV export data

### 1. CSV export data

In case of export data to CSV file implement toArray() method in your entity class or override controller methods
You can also configure actions to return `json`, or `xml` then instead of `toArray` method `JMSSerialzier` will serialize your data.

``` xml    
    <service id="app.product_controller" class="App\DemoBundle\Controller\ProductController" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>

        <tag name="vardius_crud.controller" />
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
