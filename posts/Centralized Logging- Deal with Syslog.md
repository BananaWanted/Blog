Centralized Logging: Deal with Syslog
Sometimes it could be tough dealing with syslog, especially when it comes with requirements like:
Centralized logging
Applications writing a lot of logs to syslog

The Problematic Default Behavior
I'm currently using Ubuntu 16.04 LTS, the default setup is most log messages are handled by journald which end up to a binary file in /run/log. journald collects logs from most common sources:
stdout stderr of systemd units
syscall to syslog
/dev/log 

A log entry in journald is unlike the ones in traditional syslog which are plain text, is actually a structured data. By default the journald will fill some fields for you, like the name of systemd unit, source machine hostname, … And more importantly, you can add customized fields to it. This makes journald special because TK unified interface for big apps like docker
However, journald CAN NOT/NOT GOOD AT do these things:
Collect remote syslog on UDP port 514
Make up the log files in /var/log folder
Programmatically process the syslog (i.e. add calculated fields)
Express to an arbitrary external system TK journal-remote does exists for communication between journald instances

And to solve some of these problems, Ubuntu uses rsyslogd as the center of the logging system and usejournald to serving rsyslogd as a major ingest source. However, during the forwarding:
Customized fields will be lost
File size TK

The Idea
We should keep journald
We could get rid of rsyslogd
find some other softwares
Those softwares should:
Read full structured logs from journald
Listen on UDP 514 as syslog server
Support log stream processing
Compress the data when save to disk

Fluentd & Logstash
Kafka
Elasticsearch
The Goal
It's true, I'm trying to index the syslog on my Ubuntu-based Gateway into Elasticsearch and analysis with Kibana.
The problem is, I applied iptables logging on all outgoing packets, which generate a lot of logs.
iptables ... -j LOG --log-prefix 'IPT: ...'
Current Architecture
Problems Already Solved by Current Approach
Problems to Solve
The Solution