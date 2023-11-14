<?php

return [
  'Wopi' => [

    'proof_validation_enabled' => true,
    'default_permission_edit' => true,
    'default_permission_share' => true,
    'min_ttl_time' => 36000, // 10 hours in seconds
    'default_user' => 'creator',
    'versioning' => 'increment',
    'valid_versioning_type' => ['increment', 'timestamp', 'hash'],
  ],
];
