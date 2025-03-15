<?php

namespace Modules\Storefront\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Storefront\Database\factories\UserActivationCodeFactory;
use Modules\User\Entities\User;

class UserActivationCode extends Model
{
    use HasFactory;
    protected $table = 'user_activation_codes';
    protected $fillable = [
        'user_id',
        'phone',
        'otp_media',
        'activation_code',
        'expiry',
        'created_by',
        'updated_by',
    ];
    protected $casts = [
        'expiry' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
