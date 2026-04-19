<?php

return array_replace_recursive(
    require base_path('vendor/livewire/livewire/config/livewire.php'),
    [
        'temporary_file_upload' => [
            'rules' => ['required', 'file', 'max:20480'],
        ],
    ]
);
