<?php

namespace Mii\Taxonomy;

use Pagekit\Framework\Application;
use Pagekit\Extension\Extension;

class MiiTaxonomyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
      parent::boot($app);

      $app['miiTaxonomy.api'] = function() {
          return new Api;
      };

      $app['events']->dispatch('miiTaxonomy.boot');

    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {

      if ($version = $this['migrator']->create('extension://miitaxonomy/migrations', $this['option']->get('miiTaxonomy:version'))->run()) {
        $this['option']->set('miiTaxonomy:version', $version);
      }

    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
      $this['option']->remove('miiTaxonomy:version');
    }

}
