<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class CrudGenerator
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class CrudGenerator
{
    const DEFAULT_DIRECTORY = '/Resources/config';
    const DEFAULT_LIST_DIRECTORY = '/ListView';
    const DEFAULT_FILTER_DIRECTORY = '/Filter';
    const DEFAULT_FORM_DIRECTORY = '/Form/Filter/Type';
    const DEFAULT_FORM_TYPE_DIRECTORY = '/Form/Type';

    /** @var OutputInterface */
    protected $output;
    /** @var BundleInterface */
    protected $bundle;
    /** @var string */
    protected $kernelRootDir;
    /** @var  boolean */
    protected $isPropel;
    protected $file;

    /**
     * CrudGenerator constructor.
     * @param string $kernelRootDir
     * @param boolean $isPropel
     * @param BundleInterface $bundle
     * @param OutputInterface $output
     */
    public function __construct($kernelRootDir, $isPropel, BundleInterface $bundle, OutputInterface $output)
    {
        $this->isPropel = $isPropel;
        $this->output = $output;
        $this->bundle = $bundle;
        $this->kernelRootDir = $kernelRootDir;
    }

    public function openFile()
    {
        $dir = $this->createDirectory(self::DEFAULT_DIRECTORY);
        $file = new \SplFileInfo(sprintf('%s/crud.yml', $dir));
        $this->file = fopen($file->getPathName(), 'w');
        fwrite($this->file, 'services:' . "\n");
    }

    public function closeFile()
    {
        fclose($this->file);
    }

    public function register($name, $namespace)
    {
        $content = file_get_contents(__DIR__ . '/../Resources/skeleton/services.yml');

        $content = str_replace('##CLASS##', ucfirst($name), $content);
        $content = str_replace('##FQCN##', sprintf('%s\%s', $namespace, $name), $content);
        $content = str_replace('##TYPE_NAME##', $this->fromCamelCase($name), $content);
        $content = str_replace('##ROUTE##', str_replace('_', '-', $this->fromCamelCase($name)), $content);
        fwrite($this->file, $content . "\n");
    }

    public function generate($name, $namespace, $properties)
    {
        $dir = $this->createDirectory(self::DEFAULT_LIST_DIRECTORY);
        $file = new \SplFileInfo(sprintf('%s/%sListViewProvider.php', $dir, $name));
        if (!file_exists($file)) {
            $this->addList($file, $properties, $namespace, $name);
        } else {
            $this->output->writeln(sprintf('File <comment>%-60s</comment> exists, skipped.', $this->getRelativeFileName($file)));
        }

        $dir = $this->createDirectory(self::DEFAULT_FILTER_DIRECTORY);
        $file = new \SplFileInfo(sprintf('%s/%sFilterProvider.php', $dir, $name));
        if (!file_exists($file)) {
            $this->addFilter($file, $properties, $namespace, $name);
        } else {
            $this->output->writeln(sprintf('File <comment>%-60s</comment> exists, skipped.', $this->getRelativeFileName($file)));
        }

        $dir = $this->createDirectory(self::DEFAULT_FORM_DIRECTORY);
        $file = new \SplFileInfo(sprintf('%s/%sFilterType.php', $dir, $name));
        if (!file_exists($file)) {
            $this->addFilterType($file, $properties, $namespace, $name);
        } else {
            $this->output->writeln(sprintf('File <comment>%-60s</comment> exists, skipped.', $this->getRelativeFileName($file)));
        }

        $dir = $this->createDirectory(self::DEFAULT_FORM_TYPE_DIRECTORY);
        $file = new \SplFileInfo(sprintf('%s/%sType.php', $dir, $name));
        if (!file_exists($file)) {
            $this->addFormType($file, $properties, $namespace, $name);
        } else {
            $this->output->writeln(sprintf('File <comment>%-60s</comment> exists, skipped.', $this->getRelativeFileName($file)));
        }
    }

    /**
     * @param  \SplFileInfo $file
     * @return string
     */
    protected function getRelativeFileName(\SplFileInfo $file)
    {
        return substr(str_replace(realpath($this->kernelRootDir . '/../'), '', $file), 1);
    }

    protected function createDirectory($default)
    {
        $fs = new Filesystem();

        if (!is_dir($dir = $this->bundle->getPath() . $default)) {
            $fs->mkdir($dir);
            $this->output->writeln('>>  <info>Dir+</info>     ' . $dir);
        }

        return $dir;
    }

    protected function fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return strtolower(implode('_', $ret));
    }

    protected function addList(\SplFileInfo $file, $properties, $namespace, $name)
    {
        $content = file_get_contents(__DIR__ . '/../Resources/skeleton/ListViewProvider.php');

        $content = str_replace('##NAMESPACE##', str_replace('\Entity', '', $namespace) . str_replace('/', '\\', self::DEFAULT_LIST_DIRECTORY), $content);
        $content = str_replace('##CLASS##', $name . 'ListViewProvider', $content);
        $content = str_replace('##TYPE_NAME##', $this->fromCamelCase($name), $content);
        $content = str_replace('##FFCQN##', sprintf('%s\%s', str_replace('\Entity', '', $namespace), 'Form\Filter\Type\\' . $name . 'FilterType'), $content);
        $content = str_replace('##FCLASS##', $name . 'FilterType', $content);
        $content = $this->addListFields($properties, $content);

        file_put_contents($file->getPathName(), $content);
    }

    protected function addFilter(\SplFileInfo $file, $properties, $namespace, $name)
    {
        $content = file_get_contents(__DIR__ . '/../Resources/skeleton/FilterProvider.php');

        $content = str_replace('##NAMESPACE##', str_replace('\Entity', '', $namespace) . str_replace('/', '\\', self::DEFAULT_FILTER_DIRECTORY), $content);
        $content = str_replace('##CLASS##', $name . 'FilterProvider', $content);
        $content = $this->addFilterFields($properties, $content);

        file_put_contents($file->getPathName(), $content);
    }

    protected function addFilterType(\SplFileInfo $file, $properties, $namespace, $name)
    {
        $content = file_get_contents(__DIR__ . '/../Resources/skeleton/FilterType.php');

        $content = str_replace('##NAMESPACE##', str_replace('\Entity', '', $namespace) . str_replace('/', '\\', self::DEFAULT_FORM_DIRECTORY), $content);
        $content = str_replace('##CLASS##', $name . 'FilterType', $content);
        $content = str_replace('##FQCN##', sprintf('%s\%s', $namespace, $name), $content);
        $content = $this->addFormFields($properties, $content);

        file_put_contents($file->getPathName(), $content);
    }

    protected function addFormType(\SplFileInfo $file, $properties, $namespace, $name)
    {
        $content = file_get_contents(__DIR__ . '/../Resources/skeleton/FormType.php');

        $content = str_replace('##NAMESPACE##', str_replace('\Entity', '', $namespace) . str_replace('/', '\\', self::DEFAULT_FORM_TYPE_DIRECTORY), $content);
        $content = str_replace('##CLASS##', $name . 'Type', $content);
        $content = str_replace('##FQCN##', sprintf('%s\%s', $namespace, $name), $content);
        $content = $this->addFormFields($properties, $content);

        file_put_contents($file->getPathName(), $content);
    }

    protected function addListFields($properties, $content)
    {
        $buildCode = '';
        foreach ($properties as $property) {
            $buildCode .= sprintf("\n->addColumn('%s', 'property')", lcfirst($property));
        }

        return str_replace('##BUILD_CODE##', $buildCode, $content);
    }

    protected function addFilterFields($properties, $content)
    {
        $buildCode = '';
        foreach ($properties as $property) {
            if ($this->isPropel) {
                $body = "\n->addFilter('%s', function (FilterEvent \$event) {
                /** @var \\ModelCriteria \$query */
                \$query = \$event->getQuery();

                \$query->filterBy" . ucfirst($property) . "(\$event->getValue());

                return \$query;
                })";
            } else {
                $body = "\n->addFilter('%s', function (FilterEvent \$event) {
                \$field = \$event->getField();

                return \$event->getQuery()
                    ->andWhere(\$event->getAlias() . '.' . \$field . ' = :' . \$field)
                    ->setParameter(\$field, \$event->getValue());
                })";
            }

            $buildCode .= sprintf($body, lcfirst($property));
        }

        return str_replace('##BUILD_CODE##', $buildCode, $content);
    }

    protected function addFormFields($properties, $content)
    {
        $buildCode = '';
        foreach ($properties as $property) {
            $buildCode .= sprintf("\n->add('%s')", lcfirst($property));
        }

        return str_replace('##BUILD_CODE##', $buildCode, $content);
    }

}