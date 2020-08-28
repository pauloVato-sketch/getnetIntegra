#!/bin/bash
#ssh root@192.168.122.52 rm /var/www/$1 -Rf
sshpass -p "zeedhi@tek" scp ./toProduction/app/build/outputs/apk/app-production-release.apk zeedhi@192.168.122.52:/var/www/$1