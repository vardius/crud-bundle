Vardius - Crud Bundle
======================================

Use VardiusListBundle for your list action
------------------------------------------
1. [Install dependencies](#install-dependencies)
2. [Override action](#override-action)
3. [Use list bundle for CSV export](#use-list-bundle-for-csv-export)

### Install dependencies

#### 1. Download using composer

Install the package through composer:

``` bash
    php composer.phar require vardius/list-bundle:^0.6.2
```

#### 2. Enable the VardiusListBundle

Enable the bundle in the kernel:

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Vardius\Bundle\ListBundle\VardiusListBundle(),
        );
    }
```

Add to config.yml:

``` yml
    vardius_list:
        title: List //default 'List'
        limit: 10   //default 10
        paginator; true //turn on/off paginator
```

You can also provide your custom value for list by setting them in provider class.
For more information check the [documentation](https://github.com/Vardius/list-bundle/blob/master/Resources/doc/configuration.md)

### Override action

Create and register `CrudBundle` inside your application project

``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new CrudBundle\CrudBundle(),
        );
    }
```

Set parent bundle for your `CrudBundle` this way you can override `VardiusCrudBundle` classes and views

```php
<?php

namespace CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CrudBundle extends Bundle
{
    /**
     * @return string
     */
    public function getParent()
    {
        return 'VardiusCrudBundle';
    }
}
```

Override `ListAction` class as follow:

```php

namespace CrudBundle\Actions\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\CrudBundle\Event\ResponseEvent;
use Vardius\Bundle\ListBundle\Column\ColumnInterface;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProviderInterface;

class ListAction extends Action\ListAction
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event, string $format)
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        $request = $event->getRequest();
        $source = $event->getDataProvider()->getSource();
        $listDataEvent = new ListDataEvent($source, $request);

        /** @var ListViewProviderInterface $listViewProvider */
        $listViewProvider = $controller->get(trim($controller->getRoutePrefix(), '/') . '.list_view');
        $listView = $listViewProvider->buildListView();

        if ($format === 'html') {
            $params = [
                'list' => $listView->render($listDataEvent),
                'title' => $listView->getTitle(),
            ];
        } else {
            $columns = $listView->getColumns();
            $results = $listView->getData($listDataEvent, true);
            $results = $this->parseResults($results, $columns, $format);

            $params = [
                'data' => $results,
            ];
        }

        $routeName = $request->get('_route');
        if (strpos($routeName, 'export') !== false) {
            $params['ui'] = false;
        }

        $paramsEvent = new ResponseEvent($params);
        $crudEvent = new CrudEvent($source, $controller, $paramsEvent);

        $dispatcher = $controller->get('event_dispatcher');
        $dispatcher->dispatch(CrudEvents::CRUD_LIST, $crudEvent);

        $responseHandler = $controller->get('vardius_crud.response.handler');

        return $responseHandler->getResponse($format, $event->getView(), $this->getTemplate(), $paramsEvent->getParams(), 200, [], ['list']);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('defaults', [
            'page' => 1,
            'limit' => null,
            'column' => null,
            'sort' => null,
        ]);

        $resolver->setDefault('requirements', [
            'page' => '\d+',
            'limit' => '\d+',
        ]);

        $resolver->setDefault('pattern', function (Options $options) {
            if ($options['rest_route']) {
                return '.{_format}';
            }

            return '/list/{page}/{limit}/{column}/{sort}.{_format}';
        });
    }

    /**
     * @param array $results
     * @param ArrayCollection|ColumnInterface[] $columns
     * @param string $format
     * @return array
     */
    protected function parseResults(array $results, $columns, $format)
    {
        foreach ($results as $key => $result) {
            if (is_array($result)) {

                $results[$key] = $this->parseResults($result, $columns, $format);
            } elseif (method_exists($result, 'getId')) {
                $rowData = [];

                /** @var ColumnInterface $column */
                foreach ($columns as $column) {
                    $columnData = $column->getData($result, $format);
                    if ($columnData) {
                        $rowData[$column->getLabel()] = $columnData;
                    }
                }
                $results[$key] = $rowData;
            }
        }

        return $results;
    }

}
```

You have to also override class parameter. This way your `ListAction` class will be used for action service declaration.
 
```xml
    //CrudBundle/Resources/config/services.xml
    <parameters>
        <parameter key="vardius_crud.action_list.class">CrudBundle\Actions\Action\ListActionn</parameter>
    </parameters>
```

Override `list` action view as follow:

```twig
<!--//CrudBundle/Resources/views/Actions/list.html.twig-->
{% extends 'VardiusCrudBundle::layout.html.twig' %}

{% block title %}{{ title }}{% endblock %}

{% block content %}
    {{ list|raw }}
{% endblock %}

{% block javascripts %}
    {{ parent() }}
{% endblock %}
```

You are done. Now `VardiusListBundle` will be used for **list action**. You are also able to use it all futures.

### Use list bundle for CSV export

If you want to use `VardiusListBundle` futures like ****filters*** and many others when exporting to **CSV**
Simple add event listener for `CrudEvents::CRUD_EXPORT` and override `QueryBuilder`

```php
<?php

namespace AppBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Vardius\Bundle\ListBundle\ListView\ListView;
use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProvider;

class CrudSubscriber implements EventSubscriberInterface
{
    /** @var  ContainerInterface */
    protected $container;
    /** @var  RequestStack */
    protected $request;

    /**
     * CrudSubscriber constructor.
     * @param ContainerInterface $container
     * @param RequestStack $request
     */
    public function __construct(ContainerInterface $container, RequestStack $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            CrudEvents::CRUD_EXPORT => 'onCSV'
        );
    }

    public function onCSV(CrudEvent $event)
    {
        $request = $this->request->getMasterRequest();
        $path = trim($request->getBasePath(), '/');

        /** @var ListViewProvider $listView */
        $listViewProvider = $this->container->get($path . '.list_view'); //lets assume your list view privider services names are build like this
        /** @var ListView $listView */
        $listView = $listViewProvider->buildListView();
        $listView->setPagination(false);

        $repository = $event->getSource();
        $listDataEvent = new ListDataEvent($repository, $request);

        $event->setData($listView->getData($listDataEvent, true, true));
    }
}
```

Register your CrudSubscriber class

```xml
    app.crud_subscriber:
      class: AppBundle\EventListener\CrudSubscriber
      arguments: ["@service_container", "@request_stack"]
      tags:
          - { name: kernel.event_subscriber }
```

That is it, we managed to replace `QueryBuilder` object used to generate **CSV** file.
We are able to use full futures like filters and so on.
