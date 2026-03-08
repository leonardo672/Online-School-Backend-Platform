<?php
// app/Traits/HasDropdownData.php

namespace App\Traits;

use App\Models\User;
use App\Models\Course;

trait HasDropdownData
{
    /**
     * Get users for dropdown
     */
    protected function getUsersForDropdown()
    {
        return User::select('id', 'name')->orderBy('name')->get();
    }

    /**
     * Get courses for dropdown
     */
    protected function getCoursesForDropdown()
    {
        return Course::select('id', 'title', 'name')->orderBy('title')->get();
    }
}