<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    /**
     * An assignment has the following properties:
     * 1. A title
     * 2. A description
     * 3. A due date
     * 4. A total mark
     * 5. A pass mark
     * 6. A number of attempts
     * 
     */
}
