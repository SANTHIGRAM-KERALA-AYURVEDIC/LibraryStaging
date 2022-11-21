#!/bin/bash
exec 2> /tmp/applicationStart.log

# Restart apache
/opt/bitnami/ctlscript.sh start