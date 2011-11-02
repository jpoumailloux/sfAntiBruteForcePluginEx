<?php
/**
 * This file is part of the sfAntiBruteForcePluginEx package.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfAntiBruteForceFileStorage allows to manage file storage of users counters
 * Largely based on the sfAntiBruteForcePlugin plugin by Grégoire Marchal <gregoire.marchal@gmail.com>
 *
 * @package    sfAntiBruteForcePluginEx
 * @author     Julien Poumailloux <thebrowser@gmail.com>
 */
class sfAntiBruteForceManager
{
  /**
   * Increments the failed attempts of the user
   *
   * @param string $identifier The user identifier (generally his login)
   *
   * @return void
   */
  public static function notifyFailedAuthentication($identifier, $token)
  {
    $storage = new sfAntiBruteForceFileStorage($identifier);
    $storage->increaseAttemptsCount($token);
  }

  /**
   * Tells if the user has reached the attempts threshold
   *
   * @param string $identifier The user identifier (generally his login)
   *
   * @return bool
   */
  public static function canTryAuthentication($identifier)
  {

    // first, retrieve threshold from config
    $threshold = sfConfig::get('app_sfAntiBruteForcePlugin_threshold', 50);

    // then, retrieve user count
    $storage = new sfAntiBruteForceFileStorage($identifier);

    // check the fail counter
    $attempts = $storage->getAttempts();

    return $attempts !== null && is_array($attempts) && count($attempts) < $threshold ? $attempts : false;
  }
}
