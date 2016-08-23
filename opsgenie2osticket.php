<?php
 
 /*
 
opsgenie2osticket-for-slack provides a the ability to create osTicket tickets from OpsGenie alerts from within Slack.

MIT License

Copyright (c) [2016] [Richard J. Bocchinfuso]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

* @category   CategoryName
* @package    PackageName
* @author     Richard Bocchinfuso <rbocchinfuso@gmail.com>
* @copyright  2016 Richard J. Bocchinfuso
* @license    MIT License
* @version    v1.201608
* @link       https://github.com/rbocchinfuso/opsgenie2osticket-for-slack

*/


include_once('config.inc.php');

// Check for prod
switch ($prod) {
    case 'y':
        // osTicket URL (Production)
        $mode = "PRODUCTION";
        $osTicketURL = $PRODosTicketURL;
        $osTicketAPIkey = $PRODosTicketAPIkey;
        break;
    default:
        // osTicket URL (Dev)
        $mode = "DEVELOPMENT";
        $osTicketURL = $DEVosTicketURL;
        $osTicketAPIkey = $DEVosTicketAPIkey;
        break;
}

//
// osTicket API integration.
//  url => osTicket URL to api/task/cron stored in congig.inc.php
//  key => osTicket API Key stored in config.inc.php
//  $data add custom required fields to the array.
//


// osTicket url and key in the array below.

$osTickerConfig = array(
    'url'=>$osTicketURL . '/api/tickets.json',  // URL to site.tld/api/tickets.json
		'key'=>$osTicketAPIkey  // API Key goes here
);


// NOTE: some people have reported having to use "http://your.domain.tld/api/http.php/tickets.json" instead.

if($osTickerConfig['url'] === 'http://your.domain.tld/api/tickets.json') {
  echo "<p style=\"color:red;\"><b>Error: No URL</b><br>You have not configured this script with your URL!</p>";
  echo "Please edit this file ".__FILE__." and add your URL at line 18.</p>";
  die();  
}		
if(IsNullOrEmptyString($osTickerConfig['key']) || ($osTickerConfig['key'] === 'PUTyourAPIkeyHERE'))  {
  echo "<p style=\"color:red;\"><b>Error: No API Key</b><br>You have not configured this script with an API Key!</p>";
  echo "<p>Please log into osticket as an admin and navigate to: Admin panel -> Manage -> Api Keys then add a new API Key.<br>";
  echo "Once you have your key edit this file ".__FILE__." and add the key at line 19.</p>";
  die();
}

// Slack slash command POST text
// Grab some of the values from the slash command, create vars for post back to Slack
$command = $_POST['command'];
$text = $_POST['text'];
$token = $_POST['token'];

//var_dump($text);
//var_dump($token);


// Check the token and make sure the request is from our team 
if($SlackToken == 'PUTyourSlackTokenHERE'){ // replace this with the token from your slash command configuration page
  $msg = "The token for the slash command doesn't match. Check your script.";
  die($msg);
  echo $msg;
}


// help text
$help = "Usage:  /ticket [Customer Shortname;OpsGenie TinyID;Ticket Priority]
      Customer Shortnames:  test1, test2. test3
      Ticket Priority:  1 = Low | 2 = Normal | 3 = High | 4 = Emergency
      e.g. - /ticket ast;7205;1
      Other commands:
        /ticket help - return ticketbot help
        /ticket version - return ticketbot version
        /ticket mode - return ticketbot run mode
        /ticket shortnames - return shortname list";

switch ($text) {
    case NULL:  // return help
      $msg = $help;
      die($msg);
      echo $msg;
      break;
    case 'help':  // return help
      $msg = $help;
      die($msg);
      echo $msg;
      break;
    case 'version':  // return version
      $msg = "ticketbot version $version";
      die($msg);
      echo $msg;
      break;
    case 'mode':  // return mode
      $msg = "ticketbot is is running in $mode mode";
      die($msg);
      echo $msg;
      break;
    case 'shortnames':  // return shortname lookup
      $msg = file_get_contents($CustLookup);
      die($msg);
      echo $msg;
      break;
}



// validate query format
$regex = '/\w+;\w+;\w+/';
if (preg_match($regex, $text)) {
    // the expression matches the query string
} else {
    // if preg_match() returns false, then the regex does not match the string
    $msg = "ERROR:: malformed query";
    die($msg);
    echo $msg;
}


// Parsse text into ticket variables

list($shortname, $tinyid, $priority) = explode(";", $text);
//var_dump($shortname); // Customer Shortname
//var_dump($tinyid); // OpsGenie TinyID
//var_dump($priority); // osTicket Priority

// Customer Lookup
$f = fopen($CustLookup, "r");
//print_r(fgetcsv($f));
while ($row = fgetcsv($f)) {
    if ($row[0] == $shortname) {
        $user = $row[1];
        $email = $row[2];
        $phone = $row[3];
        //break;
    }
}
fclose($f);

//var_dump($user);
//var_dump($email);
//var_dump($phone);


// Get OpsGenie Alert Details

$OpsGenieURL='https://api.opsgenie.com/v1/json/alert?apiKey=' . $OpsGenieAPIkey . '&tinyId=' . $tinyid;

// Initiate curl
$OpsGenieCURL = curl_init();
// Disable SSL verification
curl_setopt($OpsGenieCURL, CURLOPT_SSL_VERIFYPEER, false);
// Will return the response, if false it print the response
curl_setopt($OpsGenieCURL, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($OpsGenieCURL, CURLOPT_URL,$OpsGenieURL);
// Execute
$OpsGenieResponse=curl_exec($OpsGenieCURL);
// Closing
curl_close($OpsGenieCURL);

$obj=json_decode($OpsGenieResponse);
$OpsGenieMessage = $obj->message;
$OpsGenieDesciption = $obj->description;

//var_dump($OpsGenieMessage);
//var_dump($OpsGenieDesciption);
		
// Fill in the data for the new ticket, this will likely come from $_POST.
// NOTE: your variable names in osT are case sensiTive. 
// So when adding custom lists or fields make sure you use the same case
// For examples on how to do that see Agency and Site below.
$data = array(
    'name'          =>          $user,  // from name aka User/Client Name
    'email'         =>          $email,  // from email aka User/Client Email
    'phone'   	    =>          $phone,  // phone number aka User/Client Phone Number
    'subject'       =>          $OpsGenieMessage . " [OpsGenie2osTicket-for-Slack]",  // test subject, aka Issue Summary
    'message'       =>          $OpsGenieDesciption . "\n\n -------------------- \n ### Ticket created using OpsGenie2osTicket-for-Slack ###",  // test ticket body, aka Issue Details.
    'ip'            =>          $_SERVER['REMOTE_ADDR'], // Should be IP address of the machine thats trying to open the ticket.
    'topicId'       =>          '1', // the help Topic that you want to use for the ticket
    'priorityId'    =>          $priority, // ticket priority
    //'Agency'      =>          '100', //this is an example of a custom list entry. This should be the number of the entry.
    //'Site'        =>          'Miami', // this is an example of a custom text field.  You can push anything into here you want.	
    'attachments'   => array()
);

// more fields are available and are documented at:
// https://github.com/osTicket/osTicket-1.8/blob/develop/setup/doc/api/tickets.md

if($debug=='1') {
  print_r($data);
  die();
}

/*
// Add in attachments here if necessary
$data['attachments'][] =
array('file.txt' =>
        'data:text/plain;base64;'
            .base64_encode(file_get_contents('/file.txt')));  // replace ./file.txt with /path/to/your/test/filename.txt
*/ 
 

//pre-checks
function_exists('curl_version') or die('CURL support required');
function_exists('json_encode') or die('JSON support required');

//set timeout
set_time_limit(30);

// osTicket curl post
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $osTickerConfig['url']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.8');
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$osTickerConfig['key']));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$result=curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code != 201)
    die('Unable to create ticket: '.$result);

$ticket_id = (int) $result;

// Continue onward here if necessary. $ticket_id has the ID number of the
// newly-created ticket

function IsNullOrEmptyString($question){
    return (!isset($question) || trim($question)==='');

}

// Send the osTicket number back to the user. 
echo "osTicket number " . $result . " created.";

?>

