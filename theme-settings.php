<?php

/**
 * @file
 * theme-settings.php
 *
 * Provides theme settings for Spectre CSS theme.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function spectre_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  // Buttons.
  $form['button'] = [
    '#type' => 'details',
    '#title' => t('Button'),
    '#open' => TRUE,
    '#tree' => TRUE,
  ];
  $form['button']['size'] = [
    '#type' => 'select',
    '#title' => t('Button size'),
    '#default_value' => theme_get_setting('button.size'),
    '#options' => [
      'default' => t('Normal'),
      'btn-sm' => t('Small'),
      'btn-lg' => t('Large'),
    ],
  ];
  $form['button']['icon'] = [
    '#type' => 'checkbox',
    '#title' => t('Use icons'),
    '#description' => t('Use button icons'),
    '#default_value' => theme_get_setting('button.icon'),
  ];

  // Tables.
  $form['table'] = [
    '#type' => 'details',
    '#title' => t('Table'),
    '#open' => TRUE,
    '#tree' => TRUE,
  ];
  $form['table']['hover'] = [
    '#type' => 'checkbox',
    '#title' => t('Hover style'),
    '#default_value' => theme_get_setting('table.hover'),
  ];
  $form['table']['striped'] = [
    '#type' => 'checkbox',
    '#title' => t('Striped table'),
    '#default_value' => theme_get_setting('table.striped'),
  ];
  $form['table']['scroll'] = [
    '#type' => 'checkbox',
    '#title' => t('Scrollable table'),
    '#default_value' => theme_get_setting('table.scroll'),
  ];

  // Labels.
  $form['label'] = [
    '#type' => 'details',
    '#title' => t('Label'),
    '#open' => TRUE,
    '#tree' => TRUE,
  ];

  $form['label']['enable'] = [
    '#type' => 'checkbox',
    '#title' => t('Use Spectre label style'),
    '#default_value' => theme_get_setting('label.enable'),
  ];

  $form['label']['style'] = [
    '#type' => 'checkbox',
    '#title' => t('Rounded label style'),
    '#default_value' => theme_get_setting('label.style'),
    '#states' => [
      'visible' => [
        ':input[name="label[enable]"]' => ['checked' => TRUE],
      ],
    ],
  ];
}
