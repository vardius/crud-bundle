Vardius - Crud Bundle
======================================

Crud Bundle provides crud actions

This is currently a work in progress.

ABOUT
==================================================
Contributors:

* [Rafał Lorenz](https://rafallorenz.com)

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
5. Build your list view
6. Set up your crud actions
7. Include scripts


### 1. Download using composer

Install the package through composer::

    php composer.phar require vardius/crud-bundle:*

REQUIRED: `vardius/list-bundle`

### 2. Enable the VardiusCrudBundle
Enable the bundle in the kernel:

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\ListBundle\VardiusCrudBundle(),
        );
    }

### 3. Create your entity class

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

### 4. Create your form type

        <service id="app.form.type.product" class="App\MainBundle\Form\Type\ProductType">
            <tag name="form.type" alias="product"/>
        </service>

### 5. Build your list view
Follow the steps from documentation [VardiusLIstViewBundle](https://github.com/Vardius/list-bundle)

### 6. Set up your crud actions
Default:

        <service id="app.crud_controller" class="%vardius_crud.controller.class%" factory-service="vardius_crud.controller.factory" factory-method="get">
            <argument>AppMainBundle:Product</argument>
            <argument>/products</argument>
            <argument type="service" id="app_main.product.list_view"/>
            <argument type="service" id="app_main.form.type.product"/>

            <tag name="vardius_crud.controller" />
        </service>

or set only provided actions

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

### 7. Include scripts
Include styles in your view

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

and javascripts

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

or get latest from

        [Bootstrap](http://getbootstrap.com/getting-started/#download)
        [Font Awesome](http://fortawesome.github.io/Font-Awesome/get-started/)

RELEASE NOTES
==================================================
**0.1.0**

- First public release of list-bundle