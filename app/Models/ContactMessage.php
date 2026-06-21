<?php

namespace App\Models;

use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name', 'email', 'subject', 'message', 'is_read'])]
class ContactMessage extends \Illuminate\Database\Eloquent\Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }
}
