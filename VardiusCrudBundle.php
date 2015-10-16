<?php

namespace Vardius\Bundle\CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Vardius\Bundle\CrudBundle\DependencyInjection\Compiler\ActionPass;
use Vardius\Bundle\CrudBundle\DependencyInjection\Compiler\CrudPass;

class VardiusCrudBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CrudPass());
        $container->addCompilerPass(new ActionPass());
    }
}
