[program:{{config('webserver.supervisor.prefix')}}-{{$website->uuid}}] 
process_name = %(program_name)s_%(process_num)02d 
command = php {{base_path('artisan')}} queue:work --sleep=3 --tries=3 --tenant={{$website->uuid}} 
autostart = true 
autorestart = true 
user = {{config('webserver.supervisor.user')}} 
numprocs = {{config('webserver.supervisor.numprocs')}}
redirect_stderr = true 
stdout_logfile = {{storage_path("app/tenancy/{$website->uuid}/logs/supervisor.log")}}