<?php

return [

    'main' => 'Mii\\Taxonomy\\MiiTaxonomyExtension',

    'autoload' => [

        'Mii\\Taxonomy\\' => 'src'

    ],

    'resources' => [

        'export' => [
            'view' => 'views',
            'asset' => 'assets'
        ]

    ],

    'controllers' => 'src/Controller/*Controller.php',

    'menu' => [

      'miiTaxonomy' => [
        'label'  => 'miiTaxonomy',
        'icon'   => 'extension://miitaxonomy/extension.png',
        'url'    => '@miiTaxonomy/admin/vocabulary',
        'active' => '@miiTaxonomy/admin/vocabulary*',
        'access' => 'miiTaxonomy: manage vocabularies || miiQA: manage terms',
        'priority' => 0
      ],
      'miiTaxonomy: answer list' => [
        'label'  => 'Vocabularies',
        'parent' => 'miiTaxonomy',
        'url'    => '@miiTaxonomy/admin/vocabulary',
        'active' => '@miiTaxonomy/admin/vocabulary*',
        'access' => 'miiTaxonomy: manage vocabularies'
      ],
      'miiTaxonomy: term list' => [
        'label'  => 'Terms',
        'parent' => 'miiTaxonomy',
        'url'    => '@miiTaxonomy/admin/term',
        'active' => '@miiTaxonomy/admin/term*',
        'access' => 'miiTaxonomy: manage terms'
      ],

    ],

    'permissions' => [

      'miiTaxonomy: manage settings' => [
        'title' => 'Manage settings'
      ],
      'miiTaxonomy: manage vocabularies' => [
        'title' => 'Manage vocabularies'
      ],
      'miiTaxonomy: manage terms' => [
        'title' => 'Manage terms'
      ]

    ],


    'defaults' => [

      'index_items_per_page'  => 15,

    ],


    'settings' => [

        'system' => 'extension://miitaxonomy/views/admin/settings.razr'

    ]

];
