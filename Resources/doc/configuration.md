Vardius - Crud Bundle
======================================

Configuration
----------------
1. Create your entity class
2. Create your form type
3. Set up your crud actions
4. Provide custom config for actions
5. Add custom logic
6. CSV export data
7. Include scripts

### 1. Create your entity class

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

### 2. Create your form type

``` xml
    <service id="app.form.type.product" class="App\MainBundle\Form\Type\ProductType">
        <tag name="form.type" alias="product"/>
    </service>
```

### 3. Set up your crud actions
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

### 4. Provide custom config for actions
In case you want to use rest routes or provide some config for actions you can create ActionsProvider class

Here is a simple example explaining how to add actions and provide custom config for it.

``` php
 <?php
    namespace App\DemoBundle\Actions;

    use Vardius\Bundle\CrudBundle\Actions\Provider\ActionsProvider as BaseProvider;

    class ProductActionsProvider extends BaseProvider
    {
        /**
         * Provides actions for controller
         */
        public function getActions()
        {
            //actions: list,show,edit,add,delete,export
            $this
                ->addAction('list', [
                    'route_suffix' => 'somesuffix' //default action name
                    'rest_route' => false,
                    'response_type' => 'html', 
                    'template' => '',
                    'pattern' => '',
                    'defaults' => [],
                    'requirements' => [],
                    'options' => [],
                    'host' => '',
                    'schemes' => [],
                    'methods' => [],
                    'condition' => '',               
                ])
                ->addAction('edit', [])
            ;
            
            return $this->actions;
        }

    }
```

You can enable rest routs by providing `rest_route` parameter as `true`. If you don't know what RESTful is you can read more about it [HERE](http://routes.readthedocs.org/en/latest/restful.html)
`response_type` tell the action what type of response to return `xml`, `html`, or `json`.
By `template` parameter you can provide custom location for action view.
Other options are just routing symfony options.

**COUTION: When using ActionProvider only actions provided by provider class are enabled in controller!**

Remember to register you `ActionProvider` as a `service`

``` xml
    <service id="app.product.action_provider" class="App\DemoBundle\Actions\ProductActionsProvider" parent="vardius_crud.action.provider"/>
```

Anf use it in your controller definition:

``` xml
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppBundle:Product</argument>
        <argument>/product</argument>
        <argument>NULL</argument>
        <argument>NULL</argument>
        <argument>NULL</argument>
        <argument type="service" id="app.product.action_provider"/>

        <tag name="vardius_crud.controller" />
    </service>
```

Providing route_suffix as in example above will result with route name as: `app.crud_controller.somesuffix` according to this example
Route names are builded as `$controllerKey . '.' . $actionKey` where `$controllerKey` is controller service id and `$actionKey` is `route_suffix` option or action name if route_suffix is not provided

### 5. Add custom logic
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

### 6. CSV export data
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

### 7. Include scripts
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
