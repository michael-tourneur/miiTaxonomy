<?php
  
return [
  
  'up' => function() use ($app) {

    $util = $app['db']->getUtility();

    if ($util->tableExists('@miitaxonomy_vocabularies') === false) {
      $util->createTable('@miitaxonomy_vocabularies', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text');
        $table->addColumn('status', 'smallint');
        $table->setPrimaryKey(['id']);
      });
    }

    if ($util->tableExists('@miitaxonomy_terms') === false) {
      $util->createTable('@miitaxonomy_terms', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('vid', 'integer', ['unsigned' => true, 'length' => 10]);
        $table->addColumn('name', 'string', ['length' => 255, 'default' => 0]);
        $table->addColumn('description', 'text');
        $table->addColumn('status', 'smallint');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('@miitaxonomy_vocabularies', ['vocabulary_id'], ['id'], [], 'FK_VOCABULARY_ID');
        $table->addUniqueIndex(['vocabulary_id', 'name'], 'VOCABULARY_TERM');
      });
    }
  }

  ];
