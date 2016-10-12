<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Actions\Action;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;

/**
 * ExportAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ExportAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event, string $format):Response
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        $request = $event->getRequest();
        $id = $request->get('id');

        if (is_numeric($id)) {
            if (!$controller->has('knp_snappy.pdf')) {
                throw new \Exception('Have you registered KnpSnappyBundle?');
            }

            $snappy = $controller->get('knp_snappy.pdf');
            $showAction = $controller->get('vardius_crud.action_show');
            $html = $showAction->call($event, 'html')->getContent();

            $response = new Response(
                $snappy->getOutputFromHtml($html, [
                    'margin-bottom' => 3,
                    'margin-top' => 3,
                    'margin-left' => 4,
                    'margin-right' => 14
                ]),
                200,
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="export.pdf"'
                )
            );
        } else {
            $type = $request->get('type');
            if ($type === 'pdf') {
                if (!$controller->has('knp_snappy.pdf')) {
                    throw new \Exception('Have you registered KnpSnappyBundle?');
                }

                $snappy = $controller->get('knp_snappy.pdf');
                $listAction = $controller->get('vardius_crud.action_list');
                $html = $listAction->call($event, 'html')->getContent();

                $response = new Response(
                    $snappy->getOutputFromHtml($html, [
                        'margin-bottom' => 3,
                        'margin-top' => 3,
                        'margin-left' => 4,
                        'margin-right' => 14
                    ]),
                    200,
                    array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="export.pdf"'
                    )
                );
            } else {
                $source = $event->getDataProvider()->getSource();
                if (!$source instanceof EntityRepository) {
                    throw new \Exception('CSV export supports only ORM db driver');
                }

                $queryBuilder = $source->createQueryBuilder('vardius_csv_export');
                $crudEvent = new CrudEvent($source, $controller, $queryBuilder);
                $dispatcher = $controller->get('event_dispatcher');
                $queryBuilder = $dispatcher->dispatch(CrudEvents::CRUD_EXPORT, $crudEvent)->getData();

                $response = new StreamedResponse();
                $response->setCallback(
                    function () use ($queryBuilder, $controller) {
                        $handle = fopen('php://output', 'w+');

                        $headers = $controller->getHeaders();
                        if (!empty($headers)) {
                            fputcsv($handle, $headers, ';');
                        }

                        $entityManager = $controller->get('doctrine.orm.entity_manager');

                        $results = $queryBuilder->getQuery()->iterate();
                        while (false !== ($row = $results->next())) {
                            $element = $controller->getRow($row[0]);
                            $entityManager->detach($row[0]);

                            if(count($element))    
                                fputcsv($handle, $element);
                        }

                        fclose($handle);
                    }
                );

                $response->setStatusCode(200);
                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
            }
        }

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->remove('template');

        $resolver->setDefault('pattern', '/export/{type}/{id}');

        $resolver->setDefault('methods', function (Options $options, array $previousValue) {
            if ($options['rest_route']) {
                return ['GET'];
            }

            return $previousValue;
        });

        $resolver->setDefault('requirements', [
            'type' => 'pdf|csv',
            'id' => '\d+'
        ]);

        $resolver->setDefault('defaults', [
            "type" => 'pdf',
            "id" => null
        ]);
    }
}
