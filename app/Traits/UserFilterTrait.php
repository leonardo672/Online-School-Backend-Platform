<?php
// app/Traits/UserFilterTrait.php 
namespace App\Traits;

trait UserFilterTrait
{
    protected function applyUserFilters($query, $filters = [])
    {
        // Filter by role
        if (!empty($filters['role'])) {
            switch ($filters['role']) {
                case 'instructors':
                    $query->instructors();
                    break;
                case 'students':
                    $query->students();
                    break;
                default:
                    $query->where('role', $filters['role']);
            }
        }

        // Search by name or email
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'LIKE', '%' . $filters['search'] . '%');
            });
        }

        return $query;
    }
}