<?php

namespace Wind\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * This is the abstract model class.
 */
abstract class AbstractModel extends Eloquent
{
    use BaseModelTrait, RevisionableTrait, SoftDeletes;

    protected $revisionCreationsEnabled = true;
    /**
     * A list of methods protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = ['_token', '_method', 'password', 'open_id', 'id'];

    /**
     * The model name.
     *
     * @var string
     */
    public static $name = 'base_model';
}
