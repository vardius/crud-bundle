Vardius - Crud Bundle
======================================

Serialization
----------------
1. [Configure](#configure)
1. [Set groups](#set-groups)

### Configure

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
                ->addAction('show', [
                    'defaults' => [
                        '_format' => 'json'
                    ],
                    'toArray' => false, //Default false, determine if use to Array method for data serialization (rest api)
                ])
            ;
            
            return $this->actions;
        }

    }
```

### Set Groups

You can configure groups for your entity serialization. Below you can see simple example. Property `title` will be serialized by default,
when provided groups `show` or `update` this properties will be serialized only when proper action is called.

Analogically: 
    `show` group for `show` action
    `update` group for `add` and `edit` action

``` php
    <?php
     
    namespace AppBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use JMS\Serializer\Annotation as Serializer;
    
    /**
     * @package AppBundle\Entity
     *
     * @ORM\Entity
     * @ORM\Table(name="overlay")
     */
    class Product
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         * @var int
         */
        protected $id;
    
        /**
         * @var string
         * @ORM\Column(type="string")
         */
        protected $title;
    
        /**
         * @var ArrayCollection|Video[]
         * @ORM\ManyToOne(targetEntity="Video", inversedBy="overlays")
         * @Serializer\MaxDepth(2)
         * @Serializer\Groups({"show", "update"})
         */
        protected $category;
```

FOr more information read how to configure this read: [JMS Serializer](http://jmsyst.com/libs/serializer/master/reference/annotations)