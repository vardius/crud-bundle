Vardius - Crud Bundle
======================================

Crud Bundle provides crud actions

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b96120a7-5502-4dc4-9e90-f1ac88a7b6c9/big.png)](https://insight.sensiolabs.com/projects/b96120a7-5502-4dc4-9e90-f1ac88a7b6c9)

ABOUT
==================================================
Contributors:

* [Rafał Lorenz](http://rafallorenz.com)

Want to contribute ? Feel free to send pull requests!

Have problems, bugs, feature ideas?
We are using the github [issue tracker](https://github.com/vardius/crud-bundle/issues) to manage them.

HOW TO USE
==================================================

Installation
----------------
1. Download using composer
2. Enable the VardiusCrudBundle
3. Create your entity class
4. Create your form type
5. Set up your crud actions
6. Add custom logic
7. CSV export data
8. Include scripts


### 1. Download using composer

Install the package through composer:

``` bash
    php composer.phar require vardius/crud-bundle:*
```

REQUIRED: `vardius/list-bundle`

### 2. Enable the VardiusCrudBundle
Enable the bundle in the kernel:

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\ListBundle\VardiusListBundle(),
            new Vardius\Bundle\ListBundle\VardiusCrudBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
        );
    }
```

``` yml
    # app/config/config.yml
    knp_snappy:
        pdf:
            enabled:    true
            binary:     /usr/local/bin/wkhtmltopdf
            options:    []
```

### 3. Create your entity class

``` php
    /**
     * Product
     *
     * @ORM\Table(name="product")
     * @ORM\Entity
     */
    class Product
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         * @Assert\NotBlank()
         */
        private $name;

        // setters and getters...
    }
```

### 4. Create your form type

``` xml
    <service id="app.form.type.product" class="App\MainBundle\Form\Type\ProductType">
        <tag name="form.type" alias="product"/>
    </service>
```

### 5. Set up your crud actions
Default:

``` xml
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>

        <tag name="vardius_crud.controller" />
    </service>
```

or set only provided actions

``` xml
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppBundle:Category</argument>
        <argument>/category</argument>
        <argument>NULL</argument>
        <argument>NULL</argument>
        <argument>NULL</argument>
        <argument type="collection">
            <argument type="service" key="add" id="vardius_crud.action_show"/>
        </argument>

        <tag name="vardius_crud.controller" />
    </service>
```

add action to enabled by default

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

Action `vardius_crud.action_export` is not enabled by default. If you want to use it add it same way as in example before.

Default enabled actions:

``` xml
    <argument type="service" key="list" id="vardius_crud.action_list"/>
    <argument type="service" key="show" id="vardius_crud.action_show"/>
    <argument type="service" key="add" id="vardius_crud.action_add"/>
    <argument type="service" key="edit" id="vardius_crud.action_edit"/>
    <argument type="service" key="delete" id="vardius_crud.action_delete"/>
```

Default disabled actions:

``` xml
    <argument type="service" key="export" id="vardius_crud.action_export"/>
```

### 6. Add custom logic
You can add custom logic when add, remove or edit entity. In case to do that just create CrudManager class that implements `Vardius\Bundle\CrudBundle\Manager\CrudManagerInterface`

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
            $this->entityManager->persist($data);
        }
    
        /**
         * Updates entity custom logic
         *
         * @param $entity
         */
        public function update($entity)
        {
            //add your custom logic
            $this->entityManager->persist($data);
        }
    }
```

Declare is at a service and pass it to your controller. Example:

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

### 7. CSV export data
In case of export data to CSV file implement toArray() method in your entity class or override controller methods

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

### 8. Include scripts
Include styles in your view

``` html
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
```

and javascripts

``` html
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
```

or get latest from

        [Bootstrap](http://getbootstrap.com/getting-started/#download)
        [Font Awesome](http://fortawesome.github.io/Font-Awesome/get-started/)

RELEASE NOTES
==================================================
**0.1.0**

- First public release of crud-bundle

**0.2.0**

- Major bug fix and updates
