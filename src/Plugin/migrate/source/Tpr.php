<?php

declare(strict_types = 1);

namespace Drupal\helfi_tpr\Plugin\migrate\source;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\helfi_api_base\Plugin\migrate\source\HttpSourcePluginBase;

/**
 * Source plugin for retrieving data from Tpr.
 *
 * @MigrateSource(
 *   id = "tpr"
 * )
 */
class Tpr extends HttpSourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * Keep track of ignored rows to stop migrate after N ignored rows.
   *
   * @var int
   */
  protected int $ignoredRows = 0;

  /**
   * The total count.
   *
   * @var int
   */
  protected int $count = 0;

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return 'Tpr';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return ['id' => ['type' => 'string']];
  }

  /**
   * {@inheritdoc}
   */
  public function count($refresh = FALSE) {
    return $this->count;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function next() {
    parent::next();

    // Check if the current row has changes and increment ignoredRows variable
    // to allow us to stop migrate early if we have no changes.
    if ($this->isPartialMigrate() && $this->currentRow && !$this->currentRow->changed()) {
      $this->ignoredRows++;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function initializeListIterator() : \Iterator {
    $content = $this->getContent($this->configuration['url']);
    $this->count = count($content);

    $dates = [];
    // Sort data by modified_time.
    foreach ($content as $key => $item) {
      $dates[$key] = DateTimePlus::createFromFormat('Y-m-d\TH:i:s', $item['modified_time'])->format('U');
    }
    array_multisort($dates, SORT_DESC, $content);

    foreach ($content as $object) {
      // Skip entire migration once we've reached the number of maximum
      // ignored (not changed) rows.
      // @see static::NUM_IGNORED_ROWS_BEFORE_STOPPING.
      if ($this->isPartialMigrate() && ($this->ignoredRows >= static::NUM_IGNORED_ROWS_BEFORE_STOPPING)) {
        break;
      }
      $object += $this->getContent($this->buildCanonicalUrl((string) $object['id']));

      yield $object;
    }
  }

}
