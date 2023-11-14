<?php

return [
  'Wopi' => [
    'default_permission_edit' => true,
    'default_permission_share' => true,
    'min_ttl_time' => 36000, // 10 hours in seconds
    'default_user' => 'creator',
    'versioning' => 'incremental',
    'valid_versioning_type' => ['incremental', 'timestamp', 'hash'],
  ],
];
