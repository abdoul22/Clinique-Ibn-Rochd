<?php

return [
    // Si true, la commande planifiée déduit automatiquement les crédits du personnel en fin de mois
    'auto_deduct' => env('PAYROLL_AUTO_DEDUCT', false),
];
