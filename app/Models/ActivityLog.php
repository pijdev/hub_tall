<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $actor_type
 * @property int|null $actor_id
 * @property string $action
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $ip_address
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'actor_type',
        'actor_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
    ];

    /**
     * Get the actor (user) that performed the action.
     */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the subject that the action was performed on.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity.
     */
    public static function log(
        string $action,
        string $description,
        ?Model $actor = null,
        ?Model $subject = null,
        ?string $ipAddress = null,
    ): self {
        return static::create([
            'actor_type' => $actor ? get_class($actor) : null,
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }
}
