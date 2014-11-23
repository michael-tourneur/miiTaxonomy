<?php

namespace Mii\Tag;

use Pagekit\Framework\Application;
use Pagekit\Extension\Extension;

class MiiTagExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app['events']->dispatch('miiTag.boot');
      }

      public function enable()
      {
        if ($version = $this['migrator']->create('extension://miitag/migrations', $this['option']->get('miiTag:version'))->run()) {

          $this['option']->set('miiTag:version', $version);
        }

      }

      public function uninstall()
      {
        $this['option']->remove('miiTag:version');
      }

}
