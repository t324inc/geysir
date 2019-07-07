<?php

namespace Drupal\geysir\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\geysir\Ajax\GeysirCloseModalDialogCommand;
use Drupal\geysir\Ajax\GeysirOpenModalDialogCommand;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Controller for all modal dialogs.
 */
class GeysirModalController extends GeysirControllerBase {
   /**
    * Create a modal dialog to add the first paragraph.
    */
   public function addFirst($parent_entity_type, $parent_entity_bundle, $parent_entity_revision, $field, $field_wrapper_id, $delta, $position, $js = 'nojs', $bundle = NULL) {

     $paragraph_title = $this->getParagraphTitle($parent_entity_type, $parent_entity_bundle, $field);

     if ($bundle) {
       $newParagraph = Paragraph::create(['type' => $bundle]);
       $form = $this->entityFormBuilder()->getForm($newParagraph, 'geysir_modal_add', []);
       $form['#title'] = $this->t('Add @paragraph_title', ['@paragraph_title' => $paragraph_title]);
     }
     else {
       $entity = $this->entityTypeManager()->getStorage($parent_entity_type)->loadRevision($parent_entity_revision);
       $bundle_fields = $this->entityFieldManager->getFieldDefinitions($parent_entity_type, $entity->bundle());
       $field_definition = $bundle_fields[$field];
       $bundles = $field_definition->getSetting('handler_settings')['target_bundles'];

       if ($field_definition->getSetting('handler_settings')['negate']) {
         $bundles = array_diff_key(\Drupal::service('entity_type.bundle.info')->getBundleInfo('paragraph'), $bundles);
       }

       $routeParams = [
         'parent_entity_type' => $parent_entity_type,
         'parent_entity_bundle' => $parent_entity_bundle,
         'parent_entity_revision' => $parent_entity_revision,
         'field' => $field,
         'field_wrapper_id' => $field_wrapper_id,
         'delta' => $delta,
         'position' => $position,
         'js' => $js,
       ];

       $form = \Drupal::formBuilder()->getForm('\Drupal\geysir\Form\GeysirModalParagraphAddSelectTypeForm', $routeParams, $bundles);
       $form['#title'] = $this->t('Add @paragraph_title', ['@paragraph_title' => $paragraph_title]);
     }
     return $form;
  }

  /**
   * Create a modal dialog to add a single paragraph.
   */
  public function add($parent_entity_type, $parent_entity_bundle, $parent_entity_revision, $field, $field_wrapper_id, $delta, $paragraph, $paragraph_revision, $position, $js = 'nojs', $bundle = NULL) {

    $paragraph_title = $this->getParagraphTitle($parent_entity_type, $parent_entity_bundle, $field);

    if ($bundle) {
      $newParagraph = Paragraph::create(['type' => $bundle]);
      $form = $this->entityFormBuilder()->getForm($newParagraph, 'geysir_modal_add', []);
      if($js == "ajax") {
        $response = new AjaxResponse();
        $response->addCommand(new GeysirCloseModalDialogCommand('#drupal-off-canvas'));
        $response->addCommand(new GeysirOpenModalDialogCommand($this->t('Add @paragraph_title', ['@paragraph_title' => $paragraph_title]), render($form)));
        return $response;
      } else {
        $form['#title'] = $this->t('Add @paragraph_title', ['@paragraph_title' => $paragraph_title]);
        return $form;
      }
    }
    else {
      // Get the parent revision if available, otherwise the parent.
      $entity = $this->getParentRevisionOrParent($parent_entity_type, $parent_entity_revision);

      $bundle_fields = $this->entityFieldManager->getFieldDefinitions($parent_entity_type, $entity->bundle());
      $field_definition = $bundle_fields[$field];
      $bundles = $field_definition->getSetting('handler_settings')['target_bundles'];

      if ($field_definition->getSetting('handler_settings')['negate']) {
        $bundles = array_diff_key(\Drupal::service('entity_type.bundle.info')->getBundleInfo('paragraph'), $bundles);
      }

      $routeParams = [
        'parent_entity_type'     => $parent_entity_type,
        'parent_entity_bundle'   => $parent_entity_bundle,
        'parent_entity_revision' => $parent_entity_revision,
        'field'                  => $field,
        'field_wrapper_id'       => $field_wrapper_id,
        'delta'                  => $delta,
        'paragraph'              => $paragraph->id(),
        'paragraph_revision'     => $paragraph->getRevisionId(),
        'position'               => $position,
        'js'                     => $js,
      ];

      $form = \Drupal::formBuilder()->getForm('\Drupal\geysir\Form\GeysirModalParagraphAddSelectTypeForm', $routeParams, $bundles);
      $form['#title'] = $this->t('Add @paragraph_title', ['@paragraph_title' => $paragraph_title]);

    }
    return $form;
  }

  /**
   * Create a modal dialog to edit a single paragraph.
   */
  public function edit($parent_entity_type, $parent_entity_bundle, $parent_entity_revision, $field, $field_wrapper_id, $delta, $paragraph, $paragraph_revision, $js = 'nojs') {
    $form = $this->entityFormBuilder()->getForm($paragraph_revision, 'geysir_modal_edit', []);
    $paragraph_title = $this->getParagraphTitle($parent_entity_type, $parent_entity_bundle, $field);
    $form['#attached']['library'][] = 'geysir/style_scoped';
    $form['#attached']['library'][] = 'geysir/scoped_admin';
    if($js == "ajax") {
      $scopedwrap = [
        '#theme' => 'geysir_modal_content_wrapper',
        '#content' => $form,
        '#scoped' => $this->attachScopedStyles(),
      ];
      $response = new AjaxResponse();
      $response->addCommand(new GeysirOpenModalDialogCommand($this->t('Editing @paragraph_title', ['@paragraph_title' => $paragraph_title]), $scopedwrap));
      return $response;
    } else {
      $form['#title'] = $this->t('Editing @paragraph_title', ['@paragraph_title' => $paragraph_title]);
      return [
        '#theme' => 'geysir_modal_content_wrapper',
        '#content' => $form,
        '#scoped' => $this->attachScopedStyles(),
      ];
    }
  }

  /**
   * Create a modal dialog to translate a single paragraph.
   */
  public function translate($parent_entity_type, $parent_entity_bundle, $parent_entity_revision, $field, $field_wrapper_id, $delta, Paragraph $paragraph, $paragraph_revision, $js = 'nojs') {
    $langcode = $this->languageManager()->getCurrentLanguage()->getId();
    $translated_paragraph = $paragraph->addTranslation($langcode, $paragraph->toArray());
    $form = $this->entityFormBuilder()->getForm($translated_paragraph, 'geysir_modal_edit', []);
    $paragraph_title = $this->getParagraphTitle($parent_entity_type, $parent_entity_bundle, $field);
    if($js == "ajax") {
      $response = new AjaxResponse();
      $response->addCommand(new GeysirOpenModalDialogCommand($this->t('Translating @paragraph_title', ['@paragraph_title' => $paragraph_title]), render($form)));
      return $response;
    } else {
      $form['#title'] = $this->t('Translating @paragraph_title', ['@paragraph_title' => $paragraph_title]);
      return $form;
    }
  }

  /**
   * Create a modal dialog to delete a single paragraph.
   */
  public function delete($parent_entity_type, $parent_entity_bundle, $parent_entity_revision, $field, $field_wrapper_id, $delta, $paragraph, $paragraph_revision, $js = 'nojs') {
    if ($js == 'ajax') {
      $options = [
        'dialogClass' => 'geysir-dialog',
        'width' => '20%',
      ];

      $form = $this->entityFormBuilder()->getForm($paragraph, 'geysir_modal_delete', []);

      $response = new AjaxResponse();
      $paragraph_title = $this->getParagraphTitle($parent_entity_type, $parent_entity_bundle, $field);
      $response->addCommand(new OpenModalDialogCommand($this->t('Delete @paragraph_title', ['@paragraph_title' => $paragraph_title]), render($form), $options));
      return $response;
    }

    return $this->t('Javascript is required for this functionality to work properly.');
  }

  public function attachScopedStyles() {
    return [
      'css' => [
        '/core/assets/vendor/normalize-css/normalize.css',
        '/core/misc/normalize-fixes.css',
        '/core/themes/classy/css/components/form.css',
        '/core/themes/seven/css/base/elements.css',
        '/core/themes/seven/css/components/tables.css',
        '/core/themes/seven/css/components/form.css',
        '/core/themes/seven/css/components/buttons.css',
        '/core/themes/seven/css/components/details.css',
        '/core/themes/seven/css/components/field-ui.css',
        '/core/modules/media_library/css/media_library.theme.css',
        '/core/themes/stable/css/system/system.admin.css',
        '/modules/d324/d324_media/css/d324_media.common.css',
        '/themes/contrib/adminimal_theme/css/adminimal_fonts.css',
        '/modules/contrib/paragraphs/css/paragraphs.widget.css',
        '/modules/contrib/paragraphs/css/paragraphs.modal.css',
        '/modules/contrib/paragraphs/css/paragraphs.actions.css',
        '/themes/contrib/adminimal_theme/css/adminimal.css',
        '/themes/d324/d324theme_admin/css/d324theme-admin.theme.style.css',
      ],
    ];
  }

}
