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

* @package    opsgenie2osticket
* @author     Richard Bocchinfuso <rbocchinfuso@gmail.com>
* @copyright  2016 Richard J. Bocchinfuso
* @license    MIT License
* @version    v1.201608
* @link       https://github.com/rbocchinfuso/opsgenie2osticket-for-slack

*/

// If 1, display things to debug.
$debug='0';

// Version
$version = 'v1.20160822-beta';

// If "y" use osTicket Production settings
$prod='n';

// Customer lookup file
$CustLookup = './custlookup.sample.csv';

// osTicket URL (Production)
$PRODosTicketURL = 'http://your.domain.tld';
$PRODosTicketAPIkey = 'PUTyourAPIkeyHERE';

// osTicket URL (Dev)
$DEVosTicketURL = 'http://your.domain.tld';
$DEVosTicketAPIkey = 'PUTyourAPIkeyHERE';

// Slack Token
$SlackToken = 'PUTyourSlackTokenHERE';

// OpsGenie API Key
$OpsGenieAPIkey = 'PUTyourOpsGenieAPIkeyHERE';

?>
