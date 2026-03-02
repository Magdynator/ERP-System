<?php

declare(strict_types=1);

namespace Erp\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'audit_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'action',
        'auditable_type',
        'auditable_id',
        'user_id',
        'old_values',
        'new_values',
        'ip_address' // Adding in case it might be required later, but Schema output only showed action, auditable_type, auditable_id, user_id, old_values, new_values, created_at, updated_at
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Get the auditable model that generated the log.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class ?? \Erp\Core\Models\User::class);
    }
}
