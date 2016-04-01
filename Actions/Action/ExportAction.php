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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
    public function call(ActionEvent $event, $format)
    {
        $controller = $event->getController();

        $this->checkRole($controller);

        if (!$controller->has('knp_snappy.pdf')) {
            throw new \Exception('Have you registered KnpSnappyBundle?');
        }

        $request = $event->getRequest();
        $snappy = $controller->get('knp_snappy.pdf');
        $id = $request->get('id');

        if (is_numeric($id)) {
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
                $repository = $event->getDataProvider()->getSource();
                $queryBuilder = $repository->createQueryBuilder('vardius_csv_export');
                $crudEvent = new CrudEvent($repository, $controller, $queryBuilder);

                $dispatcher = $controller->get('event_dispatcher');
                $dispatcher->dispatch(CrudEvents::CRUD_EXPORT, $crudEvent);

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

        $resolver->setDefault('requirements', [
            'type' => 'pdf|csv'
        ]);

        $resolver->setDefault('defaults', [
            "type" => 'pdf',
            "id" => null
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'export';
    }

}
