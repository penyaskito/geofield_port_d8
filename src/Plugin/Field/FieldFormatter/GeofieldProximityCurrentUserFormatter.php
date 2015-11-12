<?php

/**
 * @file
 * Contains \Drupal\geofield\Plugin\Field\FieldFormatter\GeofieldProximityCurrentUserFormatter.
 */

namespace Drupal\geofield\Plugin\Field\FieldFormatter;

use Drupal\user\Entity\User;
use geoPHP;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geofield_proximity_current_user' formatter.
 *
 * @FieldFormatter(
 *   id = "geofield_proximity_current_user",
 *   label = @Translation("Proximity to current user"),
 *   field_types = {
 *     "geofield"
 *   }
 * )
 */
class GeofieldProximityCurrentUserFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'current_user_field' => '',
      'unit' => GEOFIELD_KILOMETERS,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $geofields = \Drupal::entityManager()->getFieldMapByFieldType('geofield');
    $options = array();
    if (isset ($geofields['user'])) {
      foreach ($geofields['user'] as $key => $value) {
        $options[$key] = $key;
      }
    }

    $elements['current_user_field'] = array(
      '#title' => t('Source Field'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('current_user_field'),
      '#options' => $options,
      '#states' => array(
        'visible' => array(
          ':input[name="options[source]"]' => array('value' => 'current_user')
        )
      )
    );

    $elements['unit'] = array(
      '#type' => 'select',
      '#title' => $this->t('Unit of Measure'),
      '#description' => '',
      '#options' => geofield_radius_options(),
      '#default_value' => $this->getSetting('unit'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $summary[] = $this->t('User field: @field', array('@field' => $this->getSetting('current_user_field')));
    $summary[] = $this->t('Unit: @unit', array('@unit' => $this->getSetting('unit')));
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $user_object = User::load(\Drupal::currentUser()->id());
    $geofield_name = $this->getSetting('current_user_field');
    $unit = $this->getSetting('unit');
    $radius_options = geofield_radius_options();
    $unit_string = $radius_options[$unit];

    $elements = array();

    foreach ($items as $delta => $item) {
      $destination = \Drupal::service('geophp.geophp')->load($item->value);
      $origin = new \Point($user_object->get($geofield_name)->lon, $user_object->get($geofield_name)->lat);

      $line = new \LineString([$origin, $destination]);
      $output = $line->haversineLength();
      $elements[$delta] = array('#markup' => $this->t('@distance @unit away', ['@distance' => round($unit * deg2rad($output), 2), '@unit' => $unit_string]));
    }

    return $elements;
  }

}
