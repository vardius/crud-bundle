Vardius - Crud Bundle
======================================

Manage your controller's actions
----------------
1. [Default actions](#default-actions)
2. [Enable only few](#enable-only-few)
3. [Add additional action](#add-additional-action)
4. [Configure actions](#configure-actions)

### Default actions

By default there is 5 basic crud actions enabled:

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

If it is enough for you you don't have to tell your controller nothing

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

You can enable rest routs by providing `rest_route` parameter as `true`. If you don't know what RESTful is you can read more about it [HERE](http://routes.readthedocs.org/en/latest/restful.html)
`response_type` tell the action what type of response to return `xml`, `html`, or `json`.
By `template` parameter you can provide custom location for action view.
Other options are just routing symfony options.

`checkAccess` option allow you to check user role, or use your custom voters as well, this bundle goes with `vardius-security` bundle which allows you to use `isOwner` voter.
For more information how to configure `isOwner` read [Configuration](https://github.com/Vardius/security-bundle/blob/master/Resources/doc/configuration.md)

**COUTION: When using ActionProvider only actions provided by provider class are enabled in controller!**

Remember to register your `ActionProvider` as a `service`

``` xml
    <service id="app.product.action_provider" class="App\DemoBundle\Actions\ProductActionsProvider" parent="vardius_crud.action.provider"/>
```

And use it in your controller definition:

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
