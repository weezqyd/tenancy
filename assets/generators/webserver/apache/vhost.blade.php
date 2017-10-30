#
#   Auto generated Apache configuration
#       @time: {{ date('H:i:s d-m-Y') }}
#       @author: elimuswift/tenancy
#       @website: {{ $website->uuid }}
#

@foreach($website->hostnames()->get() as $hostname)
    @include('tenancy.generator::webserver.apache.blocks.server', [
        'hostname' => $hostname,
        'ssl' => $hostname->certificate
    ])
@endforeach
