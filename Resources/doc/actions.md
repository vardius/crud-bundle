Vardius - Crud Bundle
======================================

Manage your controller's actions
----------------
1. [Default actions](#default-actions)
2. [Enable only few](#enable-only-few)
3. [Add additional action](#add-additional-action)
4. [Configure actions](#configure-actions)
5. [Response/Request formats](#responserequest-formats)
6. [Update action](#update-action)

### Default actions

By default there is 5 basic crud actions enabled:
##### YML
``` yml
    arguments: { list: '@vardius_crud.action_list', show: '@vardius_crud.action_show', add: '@vardius_crud.action_add', edit: '@vardius_crud.action_edit', delete: '@vardius_crud.action_delete' }
```
##### XML
``` xml
    <argument type="service" key="list" id="vardius_crud.action_list"/>
    <argument type="service" key="show" id="vardius_crud.action_show"/>
    <argument type="service" key="add" id="vardius_crud.action_add"/>
    <argument type="service" key="edit" id="vardius_crud.action_edit"/>
    <argument type="service" key="delete" id="vardius_crud.action_delete"/>
```

Default disabled actions:
##### YML
``` yml
    export: '@vardius_crud.action_export'
    update: '@vardius_crud.action_update'
```
##### XML
``` xml
    <argument type="service" key="export" id="vardius_crud.action_export"/>
    <argument type="service" key="update" id="vardius_crud.action_update"/>
```

If it is enough for you you don't have to tell your controller nothing

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

### Enable only few

You can tell controller which actions should be enabled. 
In that case pass the collection of actions you want to be available
##### YML
``` yml
    
services:
    app.crud_controller:
        class: %vardius_crud.controller.class%
        tags:
            - { name: vardius_crud.controller }
        factory_method: get
        factory_service: vardius_crud.controller.factory
        arguments: ['AppBundle:Category', /category, null, null, null, { add: '@vardius_crud.action_show' }]
```

##### XML
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

### Add additional action

You can add more actions to this enabled by default or this provided by you.
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

### Configure actions

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
                    'defaults' => [
                        '_format' => 'json'
                    ],
                    'requirements' => [
                        '_format' => 'json'
                    ],
                    'template' => '', //template path available for all actions expect export (export uses show and list action templates)
                    'pattern' => '',
                    'defaults' => [],
                    'requirements' => [],
                    'options' => [],
                    'host' => '',
                    'schemes' => [],
                    'methods' => [],
                    'condition' => '',
                    'checkAccess' => [ //default empty array - no access is checked then
                        'attributes' => ['ROLE_USER', 'isOwner'], //attributes array
                        'message' => 'Access Denied.', //optional message, default: Access Denied.
                    ],
                    'toArray' => false, //Default false, available only for show action, determine if use to Array method for data serialization (rest api)
                ])
                ->addAction('edit', [])
            ;
            
            return $this->actions;
        }

    }
```

By `template` parameter you can provide custom location for action view.
Other options are just routing symfony options.

`checkAccess` option allow you to check user role, or use your custom voters as well, this bundle goes with `vardius-security` bundle which allows you to use `isOwner` voter.
For more information how to configure `isOwner` read [Configuration](https://github.com/Vardius/security-bundle/blob/master/Resources/doc/configuration.md)

### Response/Request formats

You can enable rest routs by providing `rest_route` parameter as `true`. If you don't know what RESTful is you can read more about it [HERE](http://routes.readthedocs.org/en/latest/restful.html)
Default `_format` is `html` and you can request other response by providing allowed formats in the route. For example `/list.json`. allowed types by default are `html|json|xml`.
When entering rout without `{_format}` parameter the default one will trigger. For example `/list` will trigger default `html` format.

You can override this options as follows:

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
                    'defaults' => [
                        '_format' => 'json' //default format
                    ],
                    'requirements' => [
                        '_format' => 'json|xml' //allowed formats
                    ],
                ])
            ;
            
            return $this->actions;
        }
    }
```

This example will works as follow:

`/list` will trigger `json` format
`/list.json` will trigger `json` format
`/list.xml` will trigger `xml` format
`/list.*` any other formats will not match any route

**COUTION: When using ActionProvider only actions provided by provider class are enabled in controller!**

Remember to register your `ActionProvider` as a `service`
##### YML
``` yml
services:
    app.product.action_provider:
        class: App\DemoBundle\Actions\ProductActionsProvider
```
##### XML
``` xml
    <service id="app.product.action_provider" class="App\DemoBundle\Actions\ProductActionsProvider" parent="vardius_crud.action.provider"/>
```

And use it in your controller definition:

##### YML
``` yml
services:
    app.crud_controller:
        class: %vardius_crud.controller.class%
        tags:
            - { name: vardius_crud.controller }
        factory_method: get
        factory_service: vardius_crud.controller.factory
        arguments: ['AppBundle:Product', /product, null, null, null, '@app.product.action_provider']
```
##### XML
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
Route names are builded as `$controllerKey . '.' . $actionKey` where `$controllerKey` is controller service id and `$actionKey` is `route_suffix` option or action name if route_suffix is not provided.

### Update action

There is `PATCH` action available allowing you to update part of your object defined in action config.
Example of `Update action` usage:

``` php
 <?php
    namespace App\DemoBundle\Actions;

    use Vardius\Bundle\CrudBundle\Actions\Provider\ActionsProvider as BaseProvider;

    class ProductActionsProvider extends BaseProvider
    {
        public function getActions()
        {
            $this
                ->addAction('update', [
                    'allow' => ["name"], //define fields allowed to be update by PATCH request
                ])
            ;
            
            return $this->actions;
        }
    }
```
