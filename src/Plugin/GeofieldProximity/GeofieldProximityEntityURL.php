<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\GeofieldProximity\GeofieldProximityEntityURL.
 */

namespace Drupal\geofield\Plugin\GeofieldProximity;

use Drupal\geofield\Plugin\GeofieldProximityBase;

/**
 * Entity from URL proximity implementation for Geofield.
 *
 * @GeofieldProximity(
 *   id = "entity_from_url",
 *   admin_label = @Translation("Entity from URL")
 * )
 */
class GeofieldProximityEntityURL extends GeofieldProximityBase {

  /**
   * @{@inheritdoc}
   */
  public function option_definition(&$options, $views_plugin) {
    $options['geofield_proximity_entity_url_entity_type'] = array(
      'default' => 'node',
    );
    $options['geofield_proximity_entity_url_field'] = array(
      'default' => '',
    );
    $options['geofield_proximity_entity_url_delta'] = array(
      'default' => 0,
    );
  }

  /**
   * @{@inheritdoc}
   */
  public function options_form(&$form, &$form_state, $views_plugin) {
    $entities = entity_get_info();
    $entity_options = array();
    foreach ($entities as $key => $entity) {
      $entity_options[$key] = $entity['label'];
    }

    $form['geofield_proximity_entity_url_entity_type'] = array(
      '#type' => 'select',
      '#title' => t('Entity Type'),
      '#default_value' => $views_plugin->options['geofield_proximity_entity_url_entity_type'],
      '#options' => $entity_options,
      '#states' => array('visible' => array(
				 '#edit-options-source' => array('value' => 'entity_from_url')))
    );

    $geofields = _geofield_get_geofield_fields();
    $field_options = array();
    foreach ($geofields as $key => $field) {
      $field_options[$key] = $key;
    }

    $form['geofield_proximity_entity_url_field'] = array(
      '#type' => 'select',
      '#title' => t('Source Field'),
      '#default_value' => $views_plugin->options['geofield_proximity_entity_url_field'],
      '#options' => $field_options,
      '#states' => array('visible' => array(
			  '#edit-options-source' => array('value' => 'entity_from_url')))
    );
  }

  /**
   * @{@inheritdoc}
   */
  public function getSourceValue($views_plugin) {
    $entity_type = $views_plugin->options['geofield_proximity_entity_url_entity_type'];
    $geofield_name = $views_plugin->options['geofield_proximity_entity_url_field'];
    $delta = $views_plugin->options['geofield_proximity_entity_url_delta'];

    $entity = menu_get_object($entity_type);
    if (isset($entity) && !empty($geofield_name)) {
      $field_data = field_get_items($entity, $geofield_name);

      if ($field_data != FALSE) {
        return array(
          'latitude' => $field_data[$delta]['lat'],
          'longitude' => $field_data[$delta]['lon'],
        );
      }
    }

    return FALSE;
  }
}