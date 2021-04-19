<?php

namespace Drupal\os2loop_lists\Helper;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Os2Loop list helper.
 *
 * Helper class for creating content lists.
 */
class Helper {
  /**
   * The logged in user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Helper constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   Logged in user account.
   */
  public function __construct(AccountProxyInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * Create current user.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container.
   *
   * @return static
   *   The current user.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * Get content related by subject.
   *
   * @return array
   *   An array of content that shares subject term with the logged in users
   *   expertises terms.
   */
  public function getContentByUserExpertise(): array {
    $user_expertises = $this->getCurrentUserExpertisesId();
    if (empty($user_expertises)) {
      return [];
    }
    return \Drupal::entityTypeManager()
      ->getListBuilder('node')
      ->getStorage()
      ->loadByProperties([
        'type' => 'os2loop_question',
        'status' => 1,
        'os2loop_shared_subject' => $user_expertises,
      ]);
  }

  /**
   * Get content related by profession.
   *
   * @return array
   *   An array of content that shares profession terms with the logged in
   *   users professions terms.
   */
  public function getContentByUserProfession(): array {
    $user_professions = $this->getCurrentUserProfessionsId();
    if (empty($user_professions)) {
      return [];
    }
    return \Drupal::entityTypeManager()
      ->getListBuilder('node')
      ->getStorage()
      ->loadByProperties([
        'type' => 'os2loop_question',
        'status' => 1,
        'os2loop_shared_profession' => $user_professions,
      ]);
  }

  /**
   * The logged in users expertises.
   *
   * @return array
   *   An array of logged in users expertise taxonomy term ids.
   */
  private function getCurrentUserExpertisesId(): array {
    $term_ids = [];
    $user = User::load($this->currentUser->id());
    $expertises = $user->get('os2loop_user_areas_of_expertise')->getValue();
    foreach ($expertises as $expertise) {
      $term_ids[] = $expertise['target_id'];
    }
    return $term_ids;
  }

  /**
   * The logged in users professions.
   *
   * @return array
   *   An array of logged in users profession taxonomy term ids.
   */
  private function getCurrentUserProfessionsId(): array {
    $term_ids = [];
    $user = User::load($this->currentUser->id());
    $professions = $user->get('os2loop_user_professions')->getValue();
    foreach ($professions as $profession) {
      $term_ids[] = $profession['target_id'];
    }
    return $term_ids;
  }

}
