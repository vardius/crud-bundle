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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AbstractCommand
 * @package Vardius\Bundle\CrudBundle\Command
 * @author Rafał Lorenz <vardius@gmail.com>
 */
abstract class AbstractCommand extends ContainerAwareCommand
{

    protected function getDatabasesFromSchema(\SplFileInfo $file, \XmlToAppData $transformer = null)
    {
        if (null === $transformer) {
            $transformer = new \XmlToAppData(null, null, 'UTF-8');
        }

        $config = new \QuickGeneratorConfig();

        if (file_exists($propelIni = $this->getContainer()->getParameter('kernel.root_dir') . '/config/propel.ini')) {
            foreach ($this->getProperties($propelIni) as $key => $value) {
                if (0 === strpos($key, 'propel.')) {
                    $newKey = substr($key, strlen('propel.'));

                    $j = strpos($newKey, '.');
                    while (false !== $j) {
                        $newKey = substr($newKey, 0, $j) . ucfirst(substr($newKey, $j + 1));
                        $j = strpos($newKey, '.');
                    }

                    $config->setBuildProperty($newKey, $value);
                }
            }
        }

        $transformer->setGeneratorConfig($config);

        return $transformer->parseFile($file->getPathName())->getDatabases();
    }

    /**
     * Returns an array of properties as key/value pairs from an input file.
     *
     * @param  string $file A file properties.
     * @return array  An array of properties as key/value pairs.
     */
    protected function getProperties($file)
    {
        $properties = array();

        if (false === $lines = @file($file)) {
            throw new \Exception(sprintf('Unable to parse contents of "%s".', $file));
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if ('' == $line || in_array($line[0], array('#', ';'))) {
                continue;
            }

            $pos = strpos($line, '=');
            $property = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            if ("true" === $value) {
                $value = true;
            } elseif ("false" === $value) {
                $value = false;
            }

            $properties[$property] = $value;
        }

        return $properties;
    }

    /**
     * Return a list of final schema files that will be processed.
     *
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     *
     * @return array
     */
    protected function getFinalSchemas(KernelInterface $kernel, BundleInterface $bundle = null)
    {
        if (null !== $bundle) {
            return $this->getSchemasFromBundle($bundle);
        }

        $finalSchemas = array();
        foreach ($kernel->getBundles() as $bundle) {
            $finalSchemas = array_merge($finalSchemas, $this->getSchemasFromBundle($bundle));
        }

        return $finalSchemas;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     *
     * @return array
     */
    protected function getSchemasFromBundle(BundleInterface $bundle)
    {
        $finalSchemas = array();

        if (is_dir($dir = $bundle->getPath() . '/Resources/config')) {
            $finder = new Finder();
            $schemas = $finder->files()->name('*schema.xml')->followLinks()->in($dir);

            if (iterator_count($schemas)) {
                foreach ($schemas as $schema) {
                    $logicalName = $this->transformToLogicalName($schema, $bundle);
                    $finalSchema = new \SplFileInfo($this->getFileLocator()->locate($logicalName));

                    $finalSchemas[(string)$finalSchema] = array($bundle, $finalSchema);
                }
            }
        }

        return $finalSchemas;
    }

    /**
     * @param  \SplFileInfo $schema
     * @param  BundleInterface $bundle
     * @return string
     */
    protected function transformToLogicalName(\SplFileInfo $schema, BundleInterface $bundle)
    {
        $schemaPath = str_replace(
            $bundle->getPath() . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR,
            '',
            $schema->getRealPath()
        );

        return sprintf('@%s/Resources/config/%s', $bundle->getName(), $schemaPath);
    }

    /**
     * @return \Symfony\Component\Config\FileLocatorInterface
     */
    protected function getFileLocator()
    {
        return $this->getContainer()->get('file_locator');
    }
}
