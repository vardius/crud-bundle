<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;
use Vardius\Bundle\CrudBundle\Generator\CrudGenerator;

/**
 * Class GenerateCommand
 * @package AppBundle\Command
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class GenerateCommand extends AbstractCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate CRUD stubs based on the schema.xml (propel) or from your mapping information (orm)')
            ->addArgument('bundle', InputArgument::REQUIRED, 'The bundle to use to generate')
            ->addArgument('classes', InputArgument::IS_ARRAY, 'Model/Entities classes to generate for')
            ->addOption('propel', 'p', InputOption::VALUE_NONE, 'Propel database driver')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command allows you to quickly generate CRUD stubs for a given bundle.
Default database driver: ORM the <info>--propel</info> parameter changes database driver to Propel.

  <info>php app/console %command.full_name%</info>
  The <info>%command.name%</info> command generates CRUD stubs from your mapping information:

You have to limit generation:
* To a bundle:

  <info>php %command.full_name% MyCustomBundle</info>
  
* To a entities/models:

  <info>php %command.full_name% MyCustomBundle Book Author</info>
EOT
            )
            ->setName('vardius:crud:generate');
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument('bundle'));
        $output->writeln(sprintf('Vardius Crud generating for bundle "<info>%s</info>"', $bundle->getName()));

        $generator = new CrudGenerator($this->getContainer()->getParameter('kernel.root_dir'), $input->getOption('propel'), $bundle, $output);

        if ($input->getOption('propel') === true) {
            if ($schemas = $this->getSchemasFromBundle($bundle)) {
                $schemas = $this->getFinalSchemas($this->getContainer()->get('kernel'));

                $transformer = new \XmlToAppData(null, null, 'UTF-8');
                foreach ($schemas as $fileName => $array) {
                    foreach ($this->getDatabasesFromSchema($array[1], $transformer) as $database) {
                        $this->createForPropel($bundle, $database, $input->getArgument('classes'), $generator, $output);
                    }
                }
            } else {
                $output->writeln(sprintf('No <comment>*schemas.xml</comment> files found in bundle <comment>%s</comment>.', $bundle->getName()));
            }
        } else {
            $this->createForOrm($bundle, $input->getArgument('classes'), $generator, $output);
        }
    }

    protected function createForOrm(BundleInterface $bundle, $entities, CrudGenerator $generator, OutputInterface $output)
    {
        $manager = new DisconnectedMetadataFactory($this->getContainer()->get('doctrine'));
        $metadata = $manager->getBundleMetadata($bundle);

        $generator->openFile();
        /** @var ClassMetadata $m */
        foreach ($metadata->getMetadata() as $m) {
            try {
                $entityMetadata = $manager->getClassMetadata($m->getName());
            } catch (\RuntimeException $e) {
                $entityMetadata = $metadata;
            }

            $name = str_replace($entityMetadata->getNamespace() . '\\', '', $m->name);
            if (0 < count($entities) && !in_array($name, $entities)) {
                continue;
            }

            $output->writeln(sprintf('  > generating <comment>%s</comment>', $m->name));

            $properties = [];
            /** @var  $column */
            foreach ($m->getReflectionProperties() as $key => $column) {
                if ($key !== 'id') {
                    $properties[] = $key;
                }
            }

            $generator->generate($name, $properties);
            $generator->register($name);
        }
        $generator->closeFile();
    }

    protected function createForPropel(\Database $database, $models, CrudGenerator $generator, OutputInterface $output)
    {
        $generator->openFile();
        foreach ($database->getTables() as $table) {
            $name = $table->getPhpName();
            if (0 < count($models) && !in_array($name, $models)) {
                continue;
            }

            $output->writeln(sprintf('  > generating <comment>%s</comment>', $name));

            $properties = [];
            foreach ($table->getColumns() as $column) {
                if (!$column->isPrimaryKey()) {
                    $properties[] = $column->getPhpName();
                }
            }

            $generator->generate($name, $properties);
            $generator->register($name);
        }
        $generator->closeFile();
    }
}
