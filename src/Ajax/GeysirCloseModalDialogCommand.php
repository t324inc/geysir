<?php

namespace Drupal\geysir\Ajax;

use Drupal\Core\Ajax\CloseDialogCommand;

/**
 * Defines an AJAX command that closes the currently visible Geysir modal.
 *
 * @ingroup ajax
 */
class GeysirCloseModalDialogCommand extends CloseDialogCommand {

  /**
   * Constructs a GeysirCloseModalDialogCommand object.
   *
   * @param bool $persist
   *   (optional) Whether to persist the dialog in the DOM or not.
   */
  public function __construct($selector = NULL, $persist = FALSE) {
    if(empty($selector)) {
      $this->selector = GeysirOpenModalDialogCommand::MODAL_SELECTOR;
    } else {
      $this->selector = $selector;
    }
    $this->persist = $persist;
  }

}
