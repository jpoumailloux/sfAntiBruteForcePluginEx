sfAntiBruteForcePluginEx plugin
=============================

The `sfAntiBruteForcePluginEx` helps you securing your web application against [brute force attacks](http://en.wikipedia.org/wiki/Brute_force_attack).
It consists of an improvement of the `sfAntiBruteForcePlugin` by [Grégory Marchal](http://www.symfonic.fr/en/2010/12/sfantibruteforceplugin-project/). While the latter is only able to count the failed attemps per account per day and once the threshold is reached to block later attemps until the end of the current day for this account, the `sfAntiBruteForcePluginEx` plugin allows instead to specify the maximum number of attemps for a given period, for example only allows 5 attemps for a given account every 10 minutes.

Principle
---------

To prevent brute force attacks, we need to count the fail attempts for a given user. To do so, you can count the failed authentication for a given username. If the defined threshold is reached for the given period, you can forbid him to login. Or even better, you can add a CAPTCHA on the login form. Feel free to do what you prefer.


Features
--------

As the `sfAntiBruteForcePluginEx` plugin is based on the `sfAntiBruteForcePlugin` plugin, it is similarly structured. It proposes a management class with 2 static methods. They allow to count authentication attempts, and to know if a user has reached his attempts threshold. Here is how to use it.

This code takes place in the action that handles the login process.

    [php]
    public function executeLogin(sfWebRequest $request)
    {
      $this->form = new LoginForm();

      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('login'));

        // retrieve the given username
        $taintedValues = $this->form->getTaintedValues();

        // check that he hasn't already reached the threshold
		$token = sfAntiBruteForceManager::canTryAuthentication($login);
        if (!$token)
        {
          // go away hacker!
          $this->forward404();
        }

        if ($this->form->isValid())
        {
          // authenticate user and redirect
          $this->getUser()->setAuthenticated(true);
          $this->redirect('@homepage');
        }
        else
        {
          // on failed authentication, increase counter for this user
		  sfAntiBruteForceManager::notifyFailedAuthentication($taintedValues['username'], $token);
        }
      }
    }

You can customize the number of failed authentication threshold and blacklisting duration in your `app.yml` file:

    [yaml]
	all:
	  sfAntiBruteForcePlugin:
	    threshold:        5   # number of allowed failed attempts before blacklisting
	    duration:         600 # duration of the blacklisting, in seconds

Changelog
---------

### 2011-11-02 | 1.0

  * thebrowser: release to the public