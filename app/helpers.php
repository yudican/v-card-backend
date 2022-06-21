<?php

// define permissions
if (!function_exists('permissionLists')) {
  function permissionLists()
  {
    $permissions = [
      'create' => 'Create',
      // 'read' => 'Read',
      'update' => 'Update',
      'delete' => 'Delete',
    ];
    return $permissions;
  }
}
// crop string middle
if (!function_exists('cropString')) {
  function cropString($longString)
  {
    if (!$longString) {
      return null;
    }

    if (strlen($longString) < 25) {
      return $longString;
    }

    $separator = '/.../';
    $separatorlength = strlen($separator);
    $maxlength = 25 - $separatorlength;
    $start = $maxlength / 2;
    $trunc =  strlen($longString) - $maxlength;

    return substr_replace($longString, $separator, $start, $trunc);
  }
}
