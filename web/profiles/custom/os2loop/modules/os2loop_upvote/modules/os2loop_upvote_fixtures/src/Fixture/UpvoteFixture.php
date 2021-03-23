<?php

namespace Drupal\os2loop_upvote_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\os2loop_post_fixtures\Fixture\PostFixture;
use Drupal\os2loop_question_fixtures\Fixture\QuestionFixture;

/**
 * Comment fixture.
 *
 * @package Drupal\os2loop_post_fixtures\Fixture
 */
class UpvoteFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    // Load flag service.
    $flag_service = \Drupal::service('flag');

    // Create the two different flags.
    $flag_id_upvote = 'os2loop_upvote_upvote_button';
    $flag_id_correct_answer = 'os2loop_upvote_correct_answer';
    $flag_upvote = $flag_service->getFlagById($flag_id_upvote);
    $flag_correct_answer = $flag_service->getFlagById($flag_id_correct_answer);

    // Flag entities with a specific flag.
    $flag_service->flag($flag_upvote, $this->getReference('comment:the-first-comment'));
    $flag_service->flag($flag_upvote, $this->getReference('comment:answer-with-attitude'));
    $flag_service->flag($flag_upvote, $this->getReference('comment:strongly-agree'));
    $flag_service->flag($flag_correct_answer, $this->getReference('comment:gloomy-comment'));
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      PostFixture::class,
      QuestionFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['os2loop_upvote'];
  }

}
