<?php
  
return [
  
  'up' => function() use ($app) {

    $util = $app['db']->getUtility();

    if ($util->tableExists('@miitaxonomy_vocabularies') === false) {
      $util->createTable('@miitaxonomy_vocabularies', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('machine_name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text');
        $table->addColumn('status', 'smallint');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['machine_name'], 'VOCABULARY_MACHINE_NAME');
      });
    }

    if ($util->tableExists('@miitaxonomy_terms') === false) {
      $util->createTable('@miitaxonomy_terms', function($table) {
        $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('vid', 'integer', ['unsigned' => true, 'length' => 10]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'text');
        $table->addColumn('status', 'smallint');
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('@miitaxonomy_vocabularies', ['vid'], ['id'], [], 'FK_VOCABULARY_ID');
        $table->addUniqueIndex(['vid', 'name'], 'VOCABULARY_TERM');
      });
    }

    if ($util->tableExists('@miitaxonomy_index') === false) {
      $util->createTable('@miitaxonomy_index', function($table) {
        $table->addColumn('tid', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
        $table->addColumn('entity_id', 'string', ['length' => 255, 'default' => 0]);
        $table->addColumn('entity', 'string', ['length' => 255, 'default' => 0]);
        $table->addColumn('created', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['tid', 'entity_id', 'entity']);
        $table->addForeignKeyConstraint('@miitaxonomy_terms', ['tid'], ['id'], [], 'FK_TERM_ID');
      });
    }
  }

  ];
