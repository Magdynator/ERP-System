<?php

declare(strict_types=1);

namespace Erp\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Erp\Core\Traits\HasAuditLog;

abstract class BaseModel extends Model
{
    use SoftDeletes, HasAuditLog;

    protected $guarded = ['id'];

    public $timestamps = true;
}
