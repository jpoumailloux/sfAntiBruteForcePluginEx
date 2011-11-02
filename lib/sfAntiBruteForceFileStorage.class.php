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
class sfAntiBruteForceFileStorage
{
  /**
   * The user identifier
   * @var string
   */
  protected $identifier;

  /**
   * The path of the data file
   * @var string
   */
  protected $dataFilePath;

  /**
   * Class constructor
   *
   * @todo Clean identifier to avoid unsafe chars
   *
   * @param string $identifier The user identifier (generally his login)
   */
  public function  __construct($identifier)
  {
    $this->identifier = $this->clean($identifier);
    $this->dataFilePath = sfConfig::get('sf_cache_dir')
      . DIRECTORY_SEPARATOR . 'sfAntiBruteForcePlugin'
      . DIRECTORY_SEPARATOR . $this->identifier;
  }

  /**
   * Retrieves the fail attempts count from the data file
   *
   * @return integer
   */
  public function getAttempts()
  {
    // file doesn't exist, so zero
    if (!file_exists($this->dataFilePath))
    {
      return array();
    }

    // retrieve data from file
    $attempts = file($this->dataFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if($attempts === false)
        return array();
    
    $now = time();
    $duration = sfConfig::get('app_sfAntiBruteForcePlugin_duration', 600);

    foreach ($attempts as $key => $attempt) {
        if($now - $attempt > $duration)
            unset($attempts[$key]);
    }

    return $attempts;
  }

  /**
   * Increases the fail attempts count for this user
   *
   * @return void
   */
  public function increaseAttemptsCount($token)
  {
    // create folder if it doesn't exist
    if (!is_dir(sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . 'sfAntiBruteForcePlugin'))
    {
      $fs = new sfFilesystem();
      $fs->mkdirs(sfConfig::get('sf_cache_dir') . DIRECTORY_SEPARATOR . 'sfAntiBruteForcePlugin', 0777);
    }

    if(!is_array($token))
        $token = array();
    array_push($token, time());

    $handle = fopen($this->dataFilePath, 'wb');
    foreach ($token as $attempt)
        fwrite($handle, $attempt . "\n");

    fclose($handle);
  }

  /**
   * Cleans identifier to avoid security risks
   *
   * @param string $identifier The identifier to clean
   *
   * @return string
   */
  protected function clean($identifier)
  {
    return str_replace(array('/', '.', '\\'), array('', '', ''), $identifier);
  }
}
