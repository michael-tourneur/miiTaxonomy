<?php

return [

    'main' => 'Mii\\Tag\\MiiTagExtension',

    'autoload' => [

        'Mii\\Tag\\' => 'src'

    ],

    'resources' => [

        'export' => [
            'view' => 'views'
        ]

    ],

    'controllers' => 'src/Controller/*Controller.php',

    'menu' => [

      'miiTag' => [
        'label'  => 'miiTag',
        'icon'   => 'extension://miitag/extension.png',
        'url'    => '@miiTag/admin/vocabulary',
        'active' => '@miiTag/admin/vocabulary*',
        'access' => 'miiTag: manage vocabularies || miiQA: manage tags',
        'priority' => 0
      ],
      'miiTag: answer list' => [
        'label'  => 'Vocabularies',
        'parent' => 'miiTag',
        'url'    => '@miiTag/admin/vocabulary',
        'active' => '@miiTag/admin/vocabulary*',
        'access' => 'miiTag: manage vocabularies'
      ],
      'miiTag: tag list' => [
        'label'  => 'Tags',
        'parent' => 'miiTag',
        'url'    => '@miiTag/admin/tag',
        'active' => '@miiTag/admin/tag*',
        'access' => 'miiTag: manage tags'
      ],

    ],

    'permissions' => [

      'miiTag: manage settings' => [
        'title' => 'Manage settings'
      ],
      'miiTag: manage vocabularies' => [
        'title' => 'Manage vocabularies'
      ],
      'miiTag: manage tags' => [
        'title' => 'Manage tags'
      ]

    ],


    'defaults' => [

      'index.items_per_page'  => 20,

    ],


    'settings' => [

        'system' => 'extension://miitag/views/admin/settings.razr'

    ]

];
