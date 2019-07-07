<?php

/**
 * @file
 * Geysir theming functionalities.
 */

use Drupal\Component\Utility\Html;

/**
 * Prepares variables for Geysir Field Paragraph wrapper templates.
 *
 * Default template: geysir-field-paragraph-wrapper.html.twig.
 *
 * @param array $vars
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: .
 */
function template_preprocess_geysir_field_paragraph_wrapper(array &$vars) {
  $element = $vars['element'];
  $paragraph = $element['#paragraph'];

  $vars['attributes']['data-geysir-paragraph-id'] = ($paragraph)? $paragraph->id() : 0;
  $vars['attributes']['class'][] = Html::cleanCssIdentifier('geysir-field-paragraph-wrapper');

  $vars['content'] = $element['#children'];
  $vars['links'] = $element['#geysir_field_paragraph_links'];
}

/**
 * Prepares variables for Geysir modal content
 *
 * Default template: geysir-modal-content-wrapper.html.twig.
 *
 * @param array $vars
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 */
function template_preprocess_geysir_modal_content_wrapper(array &$vars) {
  $vars['attributes']['id'] = 'geysir-scoped';
  $vars['attributes']['class'][] = 'geysir-scoped-content-wrapper';
  $vars['attributes']['class'][] = 'geysir-scoped-modal';
}

/**
 * Prepares variables for Geysir dialog content
 *
 * Default template: geysir-dialog-content-wrapper.html.twig.
 *
 * @param array $vars
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 */
function template_preprocess_geysir_dialog_content_wrapper(array &$vars) {
  $element = $vars['element'];
  $element['#attributes']['id'] = 'geysir-scoped';
  $element['#attributes']['class'][] = 'geysir-scoped-content-wrapper';
  $element['#attributes']['class'][] = 'geysir-scoped-dialog';
  _attach_geysir_scoped_admin_files($vars);
}

