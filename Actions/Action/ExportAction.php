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

use Doctrine\ORM\EntityManager;
use Knp\Snappy\GeneratorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vardius\Bundle\CrudBundle\Actions\Action;
use Vardius\Bundle\CrudBundle\Event\ActionEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvent;
use Vardius\Bundle\CrudBundle\Event\CrudEvents;
use Vardius\Bundle\ListBundle\Event\ListDataEvent;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * ExportAction
 *
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ExportAction extends Action
{
    /** @var  EntityManager */
    protected $entityManager;
    /** @var  GeneratorInterface */
    protected $snappy;

    /**
     * @param EntityManager $entityManager
     * @param $snappy
     * @param TwigEngine $templating
     */
    function __construct(EntityManager $entityManager, GeneratorInterface $snappy, TwigEngine $templating)
    {
        parent::__construct($templating);
        $this->entityManager = $entityManager;
        $this->snappy = $snappy;
    }

    /**
     * {@inheritdoc}
     */
    public function call(ActionEvent $event)
    {
        $repository = $event->getDataProvider()->getSource();
        $request = $event->getRequest();
        $controller = $event->getController();

        $crudEvent = new CrudEvent($repository, $controller);
        $this->dispatcher->dispatch(CrudEvents::CRUD_EXPORT, $crudEvent);

        $listView = $event->getListView();
        $listDataEvent = new ListDataEvent($repository, $request);

        $type = $request->get('type');
        if ($type === 'pdf') {
            $html = $this->getHtml($event->getView(), [
                'list' => $listView->render($listDataEvent, false),
                'title' => $listView->getTitle()
            ]);
            $response = new Response(
                $this->snappy->getOutputFromHtml($html, [
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

                    $results = $queryBuilder->getQuery()->iterate();
                    while (false !== ($row = $results->next())) {
                        $element = $controller->getRow($row[0]);
                        $this->entityManager->detach($row[0]);

                        fputcsv($handle, $element);
                    }

                    fclose($handle);
                }
            );

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventsNames()
    {
        return 'export';
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return 'list';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteDefinition()
    {
        return array(
            'pattern' => '/export/{type}',
            'requirements' => [
                'id' => 'pdf|csv'
            ],
            'defaults' => [
                "type" => 'pdf'
            ]
        );
    }

}
