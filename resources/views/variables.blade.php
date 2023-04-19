{!!
    json_encode([
    'appName' => config('app.name'),
    'baseURL' => config('app.url') . config('app.site_sub_url') . config('app.api_prefix'),
    'deviceName' => 'spa'
])
!!}
