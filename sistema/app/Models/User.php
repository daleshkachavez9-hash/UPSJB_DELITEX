<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Fillable(['name', 'email', 'password', 'role_id', 'dni'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    // Método del diagrama: +iniciarSesion(): bool
    // Compara la contraseña escrita con la encriptada y, si coincide, abre la sesión del usuario
    public function iniciarSesion(string $password, bool $recordar = false): bool
    {
        if (! Hash::check($password, $this->password)) {
            return false;
        }

        Auth::login($this, $recordar);

        return true;
    }

    // Permiso por rol: ¿el usuario puede entrar a este módulo?
    public function puede(string $modulo): bool
    {
        return (bool) $this->role?->permite($modulo);
    }

    // Iniciales para el avatar del menú (María Quispe -> MQ)
    public function iniciales(): string
    {
        return collect(explode(' ', $this->name))->filter()->take(2)
            ->map(fn ($parte) => mb_strtoupper(mb_substr($parte, 0, 1)))->implode('');
    }
}
