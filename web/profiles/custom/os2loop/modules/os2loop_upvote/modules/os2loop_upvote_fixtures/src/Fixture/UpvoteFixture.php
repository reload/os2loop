<?php

namespace Drupal\os2loop_upvote_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\flag\FlagServiceInterface;
use Drupal\os2loop_post_fixtures\Fixture\CommentFixture;
use Drupal\os2loop_post_fixtures\Fixture\PostFixture;
use Drupal\os2loop_question_fixtures\Fixture\QuestionFixture;

/**
 * Comment fixture.
 *
 * @package Drupal\os2loop_post_fixtures\Fixture
 */
class UpvoteFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {
  /**
   * The flag service.
   *
   * @var \Drupal\flag\FlagServiceInterface
   */
  private $flagService;

  /**
   * Constructor.
   */
  public function __construct(FlagServiceInterface $flagService) {
    $this->flagService = $flagService;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    // Create the two different flags.
    $flag_id_upvote = 'os2loop_upvote_upvote_button';
    $flag_id_correct_answer = 'os2loop_upvote_correct_answer';
    $flag_upvote = $this->flagService->getFlagById($flag_id_upvote);
    $flag_correct_answer = $this->flagService->getFlagById($flag_id_correct_answer);

    // Flag entities with a specific flag.
    $this->flagService->flag($flag_upvote, $this->getReference('comment:the-first-comment'));
    $this->flagService->flag($flag_upvote, $this->getReference('comment:answer-with-attitude'));
    $this->flagService->flag($flag_upvote, $this->getReference('comment:strongly-agree'));
    $this->flagService->flag($flag_correct_answer, $this->getReference('comment:gloomy-comment'));
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      CommentFixture::class,
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
