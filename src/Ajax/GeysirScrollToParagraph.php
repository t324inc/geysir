<?php

namespace Drupal\geysir\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Defines an AJAX command that scrolls the window to a given paragraph
 *
 * @ingroup ajax
 */
class GeysirScrollToParagraph implements CommandInterface {

  /**
   * The id of the target paragraph
   *
   * @var integer
   */
  protected $paragraph_id;

  /**
   * Constructs an GeysirScrollToParagraph object.
   *
   * @param integer $paragraph_id
   *   The paragraph id to scroll to
   */
  public function __construct($paragraph_id) {
    $this->paragraph_id = $paragraph_id;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'geysirScrollToParagraph',
      'paragraph_id' => $this->paragraph_id,
    ];
  }

}
