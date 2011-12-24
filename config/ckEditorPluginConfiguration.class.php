<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ckEditorPluginConfiguration
 *
 * @author teito
 */
class ckEditorPluginConfiguration extends sfPluginConfiguration
{

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('form.method_not_found', array($this, 'listenToMethodNotFound'));
  }

  public function listenToMethodNotFound($event)
  {
      switch ($event['method'])
      {
          case 'richText':
              $form = $event->getSubject();
              $fields = $event['arguments'][0];

              sfContext::getInstance()->getResponse()->addJavascript('/ckEditorPlugin/ckeditor/ckeditor.js', 'last', array());

              if (!is_array($fields))
              {
                  $fields = array($fields);
              }

              $ids = array();

              foreach($fields as $field => $config)
              {
                  if (isset($config['sub']))
                  {
                      $id = $form[$config['sub']][$field]->renderId();
                  }
                  else
                  {
                      $id = $form[$field]->renderId();
                  }

                  if (isset($config['config']))
                  {
                      $toolbarConfig = $config['config'];
                  }
                  else
                  {
                      $toolbarConfig = sfConfig::get('app_ckEditorPlugin_config');
                  }

                  $ids[$id] = $toolbarConfig;
              }

              $html = $this->template($ids);

              $form->setWidget('richTextConfig', new sfWidgetFormStaticHtml(array('html' => $html)));
              $form->getWidgetSchema()->moveField('richTextConfig', 'last');
              return true;
              break;
      }
      return false;
  }

  public function template($ids)
  {
      $configFragments = array();
      foreach ($ids as $id => $toolbarConfig)
      {
          $configVar = 'ckConfig_' . $id;

          $configFragments []= <<<TEO
    var {$configVar} =
    {
        toolbar : {$toolbarConfig}
    };

    CKEDITOR.replace( '{$id}', {$configVar} );
TEO;
      }

      $configHtml = implode(" ", $configFragments);

      $tpl = <<<TEO
      RICH TEXT ACTIVATED
<script>
    {$configHtml}
</script>
TEO;
      return $tpl;
  }
}

?>
