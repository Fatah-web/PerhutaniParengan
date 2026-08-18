<?php

function filterMandor($baseSql, $aliasBkph = 'b')
{
    $role      = $profil['role'] ?? '';
    $mandor_id = $profil['mandor_id'] ?? '';

    if (strtolower($role) === 'mandor' && !empty($mandor_id)) {

        // cek apakah query sudah punya WHERE
        if (stripos($baseSql, 'where') !== false) {
            $baseSql .= " AND $aliasBkph.mandor_id = '$mandor_id' ";
        } else {
            $baseSql .= " WHERE $aliasBkph.mandor_id = '$mandor_id' ";
        }
    }

    return $baseSql;
}