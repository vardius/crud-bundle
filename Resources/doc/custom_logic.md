Vardius - Crud Bundle
======================================

Add custom logic to your controller
----------------
1. [Add custom logic](#add-custom-logic)

### Add custom logic

You can add custom logic when add, remove or edit entity.
In case to do that just create CrudManager class that implements `Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface`

``` php
    namespace App\DemoBundle\Manager;

    use Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface;

    class CrudManager implements CrudManagerInterface
    {
        /** @var EntityManager */
        protected $entityManager;
        
        /**
         * @param EntityManager $entityManager
         */
        function __construct(EntityManager $entityManager)
        {
            $this->entityManager = $entityManager;
        }
            
        /**
         * Remove entity custom logic
         *
         * @param $entity
         */
        public function remove($entity)
        {
            //add your custom logic
            $this->entityManager->remove($entity);
        }
    
        /**
         * Add entity custom logic
         * @param $entity
         */
        public function add($entity)
        {
            //add your custom logic
            $this->entityManager->persist($entity);
        }
    
        /**
         * Updates entity custom logic
         *
         * @param $entity
         */
        public function update($entity)
        {
            //add your custom logic
            $this->entityManager->persist($entity);
        }
    }
```

Declare is as a service and pass it to your controller.
##### YML
``` yml
services:
    app.crud_manager:
        class: App\DemoBundle\Manager\CrudManager
        arguments: ['@doctrine.orm.entity_manager']
    app.crud_controller:
        class: %vardius_crud.controller.class%
        tags:
            - { name: vardius_crud.controller }
        factory_method: get
        factory_service: vardius_crud.controller.factory
        arguments: ['AppMainBundle:Product', /products, '@app_main.product.list_view', '@app_main.form.type.product', '@app.crud_manager']
```
##### XML
``` xml
    <service id="app.crud_manager" class="App\DemoBundle\Manager\CrudManager">
        <argument type="service" id="doctrine.orm.entity_manager"/>
    </service>
    
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>
        <argument type="service" id="app.crud_manager"/>

        <tag name="vardius_crud.controller" />
    </service>
```
