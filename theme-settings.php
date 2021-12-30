<?php

/**
 * @file
 * theme-settings.php
 *
 * Provides theme settings for Spectre CSS theme.
 */

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;

const SPECTRE_CACHE_CID = 'spectre_cdn_versions';
const SPECTRE_CDN_URL = 'https://api.cdnjs.com/libraries/spectre.css?fields=versions';
const SPECTRE_LAST_KNOWN_VERSION = '0.5.9';

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function spectre_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {

  // Spectre CSS library.
  $form['library'] = [
    '#type' => 'details',
    '#title' => t('Library'),
    '#open' => TRUE,
    '#tree' => TRUE,
  ];

  $form['library']['source'] = [
    '#type' => 'select',
    '#title' => t('CDN or local library'),
    '#default_value' => theme_get_setting('library.source'),
    '#description' => t('Local version requires that you put all spectre files inside assets/spectre folder'),
    '#options' => [
      'cdn' => t('CDN'),
      'local' => t('Local')
    ]
  ];

  $versions = spectre_get_cdn_versions();
  $form['library']['version'] = [
    '#type' => 'select',
    '#title' => t('CDN version'),
    '#default_value' => theme_get_setting('library.version') ?? SPECTRE_LAST_KNOWN_VERSION,
    '#options' => $versions,
  ];

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

/**
 * Fetch CDN version for Spectre.
 *
 * @return array
 */
function spectre_get_cdn_versions(): array {
  $cache = \Drupal::cache();

  // Retrieve from cache if applicable.
  if ($data = $cache->get(SPECTRE_CACHE_CID)) {
    return $data->data;
  }

  $client = \Drupal::httpClient();
  $versions = [];

  try {
    $response = $client->request('GET', SPECTRE_CDN_URL, []);
    $results = Json::decode($response->getBody()->getContents());
    $version_results = $results['versions'] ?? [];
    foreach ($version_results as $version) {
      $versions[$version] = $version;
    }
  } catch (RequestException $exception) {
    \Drupal::logger('spectre')->error($exception->getMessage());
  }

  if (!empty($versions)) {
    // Cache it for seven days
    $cache->set(SPECTRE_CACHE_CID, $versions, 86400 * 7);
  } else {
    // Use last known version as fallback.
    $versions = [
      SPECTRE_LAST_KNOWN_VERSION => SPECTRE_LAST_KNOWN_VERSION
    ];
  }

  return $versions;
}
