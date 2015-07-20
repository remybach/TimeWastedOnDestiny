<?php
/**
* Time Wasted on Destiny API
* This script returns a json array for the total time a player spent on the game Destiny.
*
* @author François Allard <binarmorker@gmail.com>
* @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
* @copyright 2015 François Allard
*/

define("VERSION", 1.4); // The API version

if (isset($_GET['help']) || !isset($_GET['user']) || !isset($_GET['console']) || empty($_GET['user']) || empty($_GET['console'])) {
	// If help is called or the syntax is incorrect
	header("Content-Type: text/plain");
	echo file_get_contents("help.txt"); // Show the help file
} else {
	$hash = md5($_GET['console'] . "-" . $_GET['user']); // Create a unique hash for the entry
    $cacheFile = "cache/" . $hash;
    if (file_exists($cacheFile) && abs(filemtime($cacheFile) - time()) < (60 * 60)) { // 60 seconds x 60 minutes = 1 hour
    	// If the file exists and hasn't expired, just show the file
        $data = file_get_contents($cacheFile);
    } else {
    	// If the file doesn't exist or has expired, create it and show the data
        $data = get_time_wasted($_GET['console'], $_GET['user']);
        file_put_contents($cacheFile, $data);
    }
    if (isset($_GET['fmt'])) {
    	header("Content-Type: text/plain");
    	echo json_encode(json_decode($data), JSON_PRETTY_PRINT);
    } else {
    	header("Content-Type: application/json");
		echo $data;
    }
}

/**
* Get total time wasted by the player.
* This is the main method used to return the json. Caching is done in in the main script.
* 
* @param int $console The console number (1 for xbox, 2 for playstation)
* @param string $name The player's username
* @return string The json array for the complete response
*/
function get_time_wasted($console, $name) {
	try {
		$timer = new Timer();
		$response = array();
		$account = new DestinyAccount($name, $console);
		$account->lookup();
		$account->get_accounts();
		$response["displayName"] = $account->display_name;
		if (array_key_exists(1, $account->accounts)) {
			// If the account contains an entry for Xbox
			$account->fetch(1);
			$xbl_time = $account->accounts[1];
			$response["xbox"] = $xbl_time;
		}
		if (array_key_exists(2, $account->accounts)) {
			// If the account contains an entry for Playstation
			$account->fetch(2);
			$psn_time = $account->accounts[2];
			$response["playstation"] = $psn_time;
		}
		$response["totalTimePlayed"] = $account->total_time;
		$response["totalTimeWasted"] = $account->wasted_time;
		$account->error['LoadTime'] = $timer->get_timer();
		$account->error['CacheTime'] = date("r");
		return json_encode(array("Response" => $response, "Info" => $account->error));
	} catch (Exception $e) {
		return json_encode(array("Response" => "", "Info" => $account->error));
	}
}

/**
* A Destiny account with total time calculations.
* An account contains the icon, time spent and the display name for each console, including a global total time spent and display name.
*/
class DestinyAccount {

	/** @var string The player's username */
	public $name = "";

	/** @var string The player's display name */
	public $display_name = "";

	/** @var int The console identifier */
	public $console = 0;

	/** @var mixed[] The account information for each console */
	public $accounts = array();

	/** @var string The temporary account identifier used before fetching the accounts */
	public $temp_account_id = "";

	/** @var int The total play time */
	public $total_time = 0;

	/** @var int The play time for deleted characters */
	public $wasted_time = 0;

	/** @var mixed[] The error definition at the end of the returned data */
	public $error = array();
	
	/**
	* Construct the account.
	* Stores the username and the console number selected for further use.
	* 
	* @param string $name The player's username
	* @param int $console The console number (1 for xbox, 2 for playstation)
	*/
	function __construct($name, $console) {
		$this->name = $name;
		$this->console = $console;
	}

	/**
	* Lookup the Destiny account.
	* Lookup the selected Destiny account if it exists, or else try with another console.
	* 
	* @param boolean $retry True if the account has to be looked up again with another console
	* @throws Exception if the servers are unreachable or the account is not found
	*/
	function lookup($retry = false) {
		// This endpoint returns the membershipId of a player
		$url = "https://www.bungie.net/platform/destiny/SearchDestinyPlayer/" . $this->console . "/" . $this->name;
		$lookup = file_get_contents($url);
		$response = json_decode($lookup);
		if ($response->ErrorCode == 5) {
			// ErrorCode 5 means servers are in maintenance
			$this->error = Error::show(Error::ERROR, "Destiny is in maintenance");
			throw(new Exception());
		}
		if (!empty($response->Response)) {
			if (!$retry) {
				// Everything is good. Claim $200. Don't go to jail.
				$this->error = Error::show(Error::SUCCESS, "Player found");
			} else {
				// The script had to retry with another console, so it shows a small warning
				$this->error = Error::show(Error::WARNING, "Account found on another platform");
			}
			$this->temp_account_id = $response->Response[0]->membershipId;
		} else {
			if (!$retry) {
				// Glorious console swap on first "Account not found" exception
				$this->swap_console();
				$this->lookup(true);
			} else {
				// That's it, the player can't be found under that name. Bummer.
				$this->error = Error::show(Error::ERROR, "Account not found");
				throw(new Exception());
			}
		}
	}
	
	/**
	* Swap the selected console.
	* Switches between 1 and 2, simply.
	*/
	function swap_console() {
		// If you fail to understand this, I swear to God...
		if ($this->console == 1) {
			$this->console = 2;
		} else if ($this->console == 2) {
			$this->console = 1;
		}
	}

	/**
	* Add the account for a given console.
	* Add the console contents and associate it to a console number.
	* 
	* @param int $console The console number (1 for xbox, 2 for playstation)
	* @param mixed[] $contents The data contents for the account
	*/
	function add_account($console, $contents) {
		$this->accounts[$console] = $contents;
	}
	
	/**
	* Get the Bungie account.
	* Get membership information for each console fro the fetched Bungie account.
	* 
	* @throws Exception if the servers are unreachable
	*/
	function get_accounts() {
		// This endpoint returns relevant data on each console account linked to a Bungie account
		$url = "https://www.bungie.net/platform/user/GetBungieAccount/" . $this->temp_account_id . "/" . $this->console;
		$lookup = file_get_contents($url);
		$response = json_decode($lookup);
		if (isset($response->Response->bungieNetUser)) {
			$this->display_name = $response->Response->bungieNetUser->displayName;
		}
		if (count($response->Response->destinyAccounts) == 0) {
			// No destiny account mean something went wrong (because we looked 
			// up the bungie account using a destiny account, so it must exist. DUH)
			$this->error = Error::show(Error::ERROR, "Destiny is in maintenance");
			throw(new Exception());
		}
		foreach ($response->Response->destinyAccounts as $account) {
			if ($account->userInfo->membershipType != $this->console && count($response->Response->destinyAccounts) == 1) {
				// This is a weird error. If you only played the Alpha or Beta on a console, 
				// but left to play the complete game on another console, this would show up.
				$this->error = Error::show(Error::WARNING, "Account found but played an earlier version of the game");
			}
			$this->add_account($account->userInfo->membershipType, json_decode(json_encode($account->userInfo), true));
		}
	}
	
	/**
	* Get and store information on the account.
	* Fetch information from bungie given a console and an already set membership identifier.
	* 
	* @param int $console The console number (1 for xbox, 2 for playstation)
	*/
	function fetch($console) {
		// This endpoint returns stats for every character created on the account
		$url = "https://www.bungie.net/Platform/Destiny/Stats/Account/" . $this->accounts[$console]['membershipType'] . "/" . $this->accounts[$console]['membershipId'];
		$lookup = file_get_contents($url);
		$response = json_decode($lookup);
        $count = 0;
        $deleted_count = 0;
        $time_played = 0;
        $time_wasted = 0;
        if (isset($response->Response->characters)) {
            $count = count($response->Response->characters);
            foreach ($response->Response->characters as $character) {
                if ($character->deleted) {
                    $deleted_count++;
                }
            }
        }
        if (isset($response->Response->mergedAllCharacters->merged->allTime->secondsPlayed->basic->value)) {
            $time_played = $response->Response->mergedAllCharacters->merged->allTime->secondsPlayed->basic->value;
        }
        if (isset($response->Response->mergedDeletedCharacters->merged->allTime->secondsPlayed->basic->value)) {
            $time_wasted = $response->Response->mergedDeletedCharacters->merged->allTime->secondsPlayed->basic->value;
        }
		$this->accounts[$console]['characters']['total'] = $count;
		$this->accounts[$console]['characters']['deleted'] = $deleted_count;
		$this->accounts[$console]['timePlayed'] = $time_played;
		$this->accounts[$console]['timeWasted'] = $time_wasted;
		$this->total_time += $time_played;
		$this->wasted_time += $time_wasted;
	}
}

/**
* Generate an error.
* Creates an easy to read error array to be converted into an output format like json.
*/
class Error {
	const SUCCESS = "Success";
	const WARNING = "Warning";
	const ERROR = "Error";
	
	/**
	* Show the error.
	* Show an error array with the specified status and message.
	*
	* @param mixed $error_type The error type, either Success, Warning or Error
	* @param string $message The message to show with the error type
	* @return The error array
	*/
	static function show($error_type, $message) {
		return array("Status" => $error_type,
					 "Message" => $message,
					 "LoadTime" => 0,
					 "CacheTime" => 0,
					 "ApiVersion" => VERSION);
	}
}

/**
* Create and manage a system timer.
* The Timer can be used to calculate the script execution time.
*/
class Timer {

	/** @var boolean True if the timer is still counting */
	private $time_running;

	/** @var int Total time the script has been running */
	private $exec_time;

	/**
	* Create the timer and start it.
	* The timer object, once being created, starts its counter and sets itself to 0.
	*/
	function __construct() {
		$this->reset_timer();
		// Creating the timer starts it
		$this->start_timer();
	}

	/**
	* Reset the timer.
	* The timer stops itself and sets itself to 0.
	*/
	function reset_timer() {
		$this->time_running = false;
		$this->exec_time = 0;
	}

	/**
	* Start the timer.
	* The timer starts with the current computer time as value.
	*/
	function start_timer() {
		// Starts the timer
		$this->exec_time = microtime(true);
		$this->time_running = true;
	}

	/**
	* Stop and get time timer value.
	* The timer is stopped and its value is calculated returned.
	*
	* @return int The time counted from the start, in seconds with a precision to 4 digits
	*/
	function get_timer() {
		// Stops and saves the timer if it's started
		if ($this->time_running) {
			// Don't calculate again if the timer was already stopped
			$this->time_running = false;
			$this->exec_time = round(microtime(true) - $this->exec_time, 4);
		}
		// Shows the timer value
		return $this->exec_time;
	}
}