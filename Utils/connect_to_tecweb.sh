#!/bin/sh

if [ -z $1 ]
then
	echo "Manca l'username. invocare lo script in questo modo:"
	echo "\tsh connect_to_tecweb.sh username_laboratorio"
	echo "Dove username_laboratorio è l'uesrname che usi per connetteri in laboratorio"
	exit 1
fi

ssh -N ssh.studenti.math.unipd.it -l $1 -L8080:tecweb:80 -L8443:tecweb:443 -L8022:tecweb:22 &

pid_of_tunnel_ssh=$!
echo "Pid of ssh tunnel: $pid_of_tunnel_ssh"

nc -z localhost 8022
while [ $? -ne 0 ]
do
	sleep 1
	nc -z localhost 8022
done
ssh localhost -p 8022 -l $1

echo "Chiusura tunneling"
kill -9 $pid_of_tunnel_ssh
