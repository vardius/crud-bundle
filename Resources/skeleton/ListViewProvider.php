<?php

namespace ##NAMESPACE##;

use Vardius\Bundle\ListBundle\ListView\Provider\ListViewProvider;

class ##CLASS## extends ListViewProvider
{

    /**
     * {@inheritdoc}
     */
    public function buildListView()
    {
        $listView = $this->listViewFactory->get();

        $listView##BUILD_CODE##
        ->addFilter('##TYPE_NAME##_filter', 'provider.##TYPE_NAME##_filter')
        ;

        return $listView;
    }
}
