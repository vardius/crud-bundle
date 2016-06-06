Vardius - Crud Bundle
======================================

Configuration
----------------
1. [Create your entity class](#create-your-entity-class)
2. [Create your form type](#create-your-form-type)
3. [Create controller](#create-controller)
4. [Include scripts](#include-scripts)

### Create your entity class

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

### Create your form type

##### YML
``` yml
services:
    app.form.type.product:
        class: App\MainBundle\Form\Type\ProductType
        tags:
            - { name: form.type }
```
##### XML
``` xml
    <service id="app.form.type.product" class="App\MainBundle\Form\Type\ProductType">
        <tag name="form.type"/>
    </service>
```

### Create controller

Finally create your crud controller
Remember to inject `list view` service for `list` action.
You can read more about how to create `list view provider` [HERE](https://github.com/Vardius/list-bundle/blob/master/Resources/doc/configuration.md)        
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
```
##### XML
``` xml
    <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
        <argument>AppMainBundle:Product</argument>
        <argument>/products</argument>
        <argument type="service" id="app_main.product.list_view"/>
        <argument type="service" id="app_main.form.type.product"/>

        <tag name="vardius_crud.controller" />
    </service>
```

You can provide bundle path like `AppMainBundle:Product` when using **ORM** and namespace `AppMainBundle\Product` form **Propel** or **ORM**

### Include scripts

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


Advanced config
----------------
1. [Manage your controller's actions](actions.md)
2. [Use VardiusListBundle for list action](vardiuslist.md)
3. [Add custom logic to your controller](custom_logic.md)
4. [Export action](export.md)
5. [Serialization](serialization.md)
6. [Commands Tool](commands.md)
