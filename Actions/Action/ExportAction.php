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
use Vardius\Bundle\ListBundle\Event\ListDataEvent;

/**
 * ExportAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ExportAction extends Action
{
    /** @var int */
    protected $id = null;

    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $repository = $event->getDataProvider()->getSource();
        $request = $event->getRequest();
        $controller = $event->getController();
        $dispatcher = $controller->get('event_dispatcher');
        $snappy = $controller->get('knp_snappy.pdf');
        $responseHandler = $this->getResponseHandler($controller);

        $crudEvent = new CrudEvent($repository, $controller);
        $dispatcher->dispatch(CrudEvents::CRUD_EXPORT, $crudEvent);

        $this->id = $request->get('id');
        $options['template'] = !empty($this->options['template'] ?: (is_numeric($this->id) ? 'show' : 'list'));

        if (is_numeric($this->id)) {
            $dataProvider = $event->getDataProvider();
            $html = $responseHandler->getHtml($event->getView(), $this->getTemplate(), [
                'data' => $dataProvider->get($this->id),
                'ui' => false
            ]);
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
            $listView = $event->getListView();
            $listDataEvent = new ListDataEvent($repository, $request);

            $type = $request->get('type');
            if ($type === 'pdf') {
                $html = $responseHandler->getHtml($event->getView(), $this->getTemplate(), [
                    'list' => $listView->render($listDataEvent, false),
                    'title' => $listView->getTitle(),
                    'ui' => false
                ]);
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
                $response = new StreamedResponse();
                $queryBuilder = $listView->getData($listDataEvent, true, true);

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
