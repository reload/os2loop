<?php

namespace Drupal\os2loop_documents\Helper;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\NodeInterface;

/**
 * Form helper.
 */
class FormHelper {

  /**
   * Implements hook_form_BASE_FORM_ID_alter().
   */
  public function alterForm(array &$form, FormStateInterface $formState, string $formId) {
    switch ($formId) {
      case 'node_os2loop_documents_document_form':
      case 'node_os2loop_documents_document_edit_form':
        $node = $formState->getformObject()->getEntity();
        if (!$this->isLegacyDocument($node)) {
          unset($form['os2loop_documents_document_body']);
        }
    }
  }

  /**
   * Check if a document is a legacy document.
   *
   * A legacy document is a document with a non-empty body field.
   */
  private function isLegacyDocument(NodeInterface $node) {
    if ($node->isNew()) {
      return FALSE;
    }

    $body = $node->get('os2loop_documents_document_body')->value;
    return !empty(strip_tags($body ?? ''));
  }

}
