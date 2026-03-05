return [
    'driver' => env('ACTIVITY_LOGGER_DRIVER', 'database'),
    
    'username_column' => 'name',
    
    'log_uf_ids' => true,
    
    'log_only_custom_attributes' => false,
    
    'ignore_changed_attributes' => false,
    
    'log_only_dirty_attributes' => true,
    
    'log_to_sentry' => false,
    
    'log_to_sentry_tags' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_context' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_extra' => [],
    
    'log_to_sentry_level' => 'info',
    
    'log_to_sentry_exception' => false,
    
    'log_to_sentry_exception_level' => 'error',
    
    'log_to_sentry_exception_tags' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_exception_context' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_exception_extra' => [],
    
    'log_to_sentry_exception_level' => 'error',
    
    'log_to_sentry_exception_tags' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_exception_context' => [
        'application' => env('APP_NAME', 'Laravel'),
    ],
    
    'log_to_sentry_exception_extra' => [],
    
    'log_to_sentry_exception_level' => 'error',
];