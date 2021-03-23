<?php

namespace Drupal\os2loop_upvote\Helper;

use Drupal\Core\Cache\Cache;
use Drupal\flag\FlagCountManager;

/**
 * Flaghelper for creating top comment and count flags.
 */
class FlagHelper {
  /**
   * The flag service.
   *
   * @var \Drupal\flag\FlagCountManager
   */
  protected $flagService;

  /**
   * Constructor.
   *
   * @param \Drupal\flag\FlagCountManager $flag_service
   *   The flag service.
   */
  public function __construct(FlagCountManager $flag_service) {
    $this->flagService = $flag_service;
  }

  /**
   * Find the top comment.
   *
   * If there is a comment marked as "correct answer", this is it.
   * If there is no "correct answer", the top comment will be the most upvoted.
   */
  public function preprocessField(array &$variables) {
    $field = ['os2loop_question_answers', 'os2loop_post_comments'];
    if (in_array($variables['field_name'], $field)) {
      foreach ($variables['comments'] as $comment) {
        if (isset($comment['#comment'])) {
          $flag_counts = $this->flagService->getEntityFlagCounts($comment['#comment']);
          if (isset($flag_counts['os2loop_upvote_correct_answer'])) {
            $top_comment = $comment;
          }
          else {
            if (isset($flag_counts['os2loop_upvote_upvote_button'])) {
              if (!isset($top_comment)) {
                $top_comment = $comment;
              }
              $top_comment_flag_counts = $this->flagService->getEntityFlagCounts($top_comment['#comment']);
              if (isset($top_comment_flag_counts['os2loop_upvote_upvote_button']) && intval($flag_counts['os2loop_upvote_upvote_button']) > intval($top_comment_flag_counts['os2loop_upvote_upvote_button'])) {
                $top_comment = $comment;
              }
            }
          }
        }
      }
      $top_comment['#top'] = TRUE;
      array_unshift($variables['comments'], $top_comment);
    }
  }

  /**
   * Add number of upvotes to flag.
   */
  public function preprocessFlag(array &$variables) {
    $comment = $variables['flaggable'];
    $flag_counts = $this->flagService->getEntityFlagCounts($comment);
    if (isset($flag_counts['os2loop_upvote_upvote_button'])) {
      $variables['upvotes'] = intval($flag_counts['os2loop_upvote_upvote_button']);
    }
    // Clear cache, so the UI reflects changes in top comment.
    Cache::invalidateTags($comment->getCacheTags());
  }

  /**
   * Add styling class to top comment.
   */
  public function preprocessComment(array &$variables) {
    if (isset($variables['elements']['#top'])) {
      // @todo when styling change these classes to fit.
      $variables['attributes'] += ['class' => ['top-comment']];
    }
  }

}
