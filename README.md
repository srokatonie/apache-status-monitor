# Apache status monitor (PHP, CRON, Email)

This is a simple PHP script that can be installed as a CRON job to send notifiction every time Apache is down and send notification to your email. It should work on Debian-like distros (Ubuntu, Mint etc.)

The script can be easily adjusted to any service and any linux.

## Installation

### Clone repo and install dependencies via Composer

```
$ composer install
```

### Copy .env file and edit your SMTP details 

```
$ cp .env.example .env
$ nano .env
```

### Test run

```
$ php index.php
```

### Add CRON job

First, find out which PHP you are using

```
$ which php
// ie. /usr/bin/php
```

Then add a CRON job

```
$ crontab -e
```

Add line to run every 5 minutes

```
*/5 * * * * /usr/bin/php /path_to_apache-monitor/index.php > /dev/null 2>&1
# For debugging:
# */1 * * * * /usr/bin/php /path_to_apache-monitor/index.php >> //path_to_apache-monitor/log.txt
```
