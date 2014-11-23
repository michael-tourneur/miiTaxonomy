<?php

return [

  'up' => function() use ($app) {

    $util = $app['db']->getUtility();

    if ($util->tableExists('@miitag_vocabularies') === false) {
      $util->createTable('@miitag_vocabularies', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text');
        $table->addColumn('status', 'smallint');
        $table->setPrimaryKey(['id']);
      });
    }

    if ($util->tableExists('@miitag_terms') === false) {
      $util->createTable('@miitag_terms', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('vocabulary_id', 'integer', ['unsigned' => true, 'length' => 10]);
        $table->addColumn('term', 'string', ['length' => 255, 'default' => 0]);
        $table->addColumn('description', 'text');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('@miitag_vocabularies', ['vocabulary_id'], ['id'], [], 'FK_VOCABULARY_ID');
        $table->addUniqueIndex(['vocabulary_id', 'term'], 'VOCABULARY_TERM');
      });
    }
  }

  ];
