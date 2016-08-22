# opsgenie2osticket-for-slack

Custom Slack slash command to push OpsGenie alerts into osTicket.

## Requirements
- A custom slash command on a Slack team
- A web server running PHP5 with cURL enabled

## Usage
##### Web Server
- Download code from GitHub
    - _Note:  If you don't have Git installed you can also just grab the zip:  https://github.com/rbocchinfuso/opsgenie2osticket-for-slack/archive/master.zip_
```
    git clone https://github.com/rbocchinfuso/opsgenie2osticket-for-slack.git
```

- Make sure the opsgenie2osticket-for-slack dir and files have proper ownership and permissions
```
    chown -R www-data:www-data ./opsgenie2osticket-for-slack
    chmod -R 755 ./opsgenie2osticket-for-slack
```

- Copy conig.sample.inc.php to config.inc.php
```
    cp ./conig.sample.inc.php ./config.inc.php
```

##### osTicket
- Login to osTicket as an admin
- Manage -> API Keys -> Add New API Key
_Note:  Be sure you used the proper IP address when adding the API Key.  Check your IP address by running "curl ifconfig.me" on your web server (this is the system you will run the opsgenie2osticket.php code)_
- Update config.inc.php with osTicket URL and API key you just created

```
    // osTicket URL and API Key (Production)
    $PRODosTicketURL = 'http://your.domain.tld';
    $PRODosTicketAPIkey = 'PUTyourAPIkeyHERE';

    // osTicket URL and API Key (Dev)
    $DEVosTicketURL = 'http://your.domain.tld';
    $DEVosTicketAPIkey = 'PUTyourAPIkeyHERE';
```

##### Slack
- Set up a new custom slash command on your Slack team: https://slack.com/apps/A0F82E8CA-slash-commands
- Under "Choose a command", enter whatever you want for the command. /ticket is easy to remember.
- Under "URL", enter the URL for the script on your server.
- Leave "Method" set to "Post".
- Decide whether you want this command to show in the autocomplete list for slash commands.
- If you do, enter a short description and usage hint.
- Update the config.inc.php script with your slash command's token.

```
    // Slack Token
    $SlackToken = 'PUTyourSlackTokenHERE';
```
##### OpsGenie

```
    // OpsGenie API Key
    $OpsGenieAPIkey = 'PUTyourOpsGenieAPIkeyHERE';
``` 

##### Slash command syntax
```
    Usage:  /ticket [Customer Shortname;OpsGenie TinyID;Ticket Priority]
     Customer Shortnames:  test1, test2, test3
     Ticket Priority:  1 = Low | 2 = Normal | 3 = High | 4 = Emergency
     e.g. - /ticket ast;7205;1
     Other commands:
       /ticket mode - return ticketbot run mode
       /ticket shortnames - return shortname list
```

##### Notes
