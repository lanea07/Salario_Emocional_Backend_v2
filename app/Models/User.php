<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LaravelPropertyBag\Settings\HasSettings;
use Spatie\Permission\Traits\HasRoles;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;
use Throwable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasRecursiveRelationships, HasSettings;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'requirePassChange' => 'boolean',
            'valid_id' => 'boolean'
        ];
    }

    /**
     * Get the identifier that will be stored in the JWT token.
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return an array with custom claims to be added to the JWT token.
     */
    public function getJWTCustomClaims() {
        return [];
    }

    public function getParentKeyName()
    {
        return 'leader';
    }

    public function benefit_user()
    {
        return $this->hasMany(BenefitUser::class);
    }

    public function benefits()
    {
        return $this->belongsToMany(Benefit::class)->withTimestamps();
    }

    public function leader_user()
    {
        return $this->belongsTo(User::class, 'leader');
    }

    public function positions()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    public function hasRoles(array $roles)
    {
        return $this->roles->pluck('name')->intersect($roles)->count();
    }

    public function isAdmin()
    {
        return $this->hasRoles(['Admin']);
    }

    public function requirePassChange()
    {
        return $this->requirePassChange;
    }

    public function dependency()
    {
        return $this->belongsTo(Dependency::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            return $this->where('id', $value)->firstOrFail();
        } catch (Throwable $th) {
            throw new ModelNotFoundException('Usuario no encontrado');
        }
    }

    public function isValid()
    {
        return (bool)$this->valid_id;
    }

    public function scopeIs_Valid()
    {
        return $this->where('valid_id', true);
    }
}
