<?php

namespace App\Models;

use App\Drink;
use Database\Factories\GuestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['first_name', 'last_name', 'name_key', 'drink'])]
class Guest extends Model
{
    /** @use HasFactory<GuestFactory> */
    use HasFactory;

    /**
     * Fill in the duplicate-detection key before the guest is first saved.
     */
    protected static function booted(): void
    {
        static::creating(function (Guest $guest): void {
            $guest->name_key ??= self::nameKey($guest->first_name, $guest->last_name);
        });
    }

    /**
     * A key that is the same for every spelling of one guest's name, ignoring
     * case, extra spaces and the difference between "е" and "ё".
     */
    public static function nameKey(string $firstName, string $lastName): string
    {
        $normalized = preg_replace('/\s+/u', ' ', mb_strtolower(trim("{$firstName} {$lastName}")));

        return str_replace('ё', 'е', $normalized);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'drink' => Drink::class,
        ];
    }

    /**
     * The ticket number, handed out in the order guests sign up: "№ 0007".
     *
     * @return Attribute<string, never>
     */
    protected function ticketNumber(): Attribute
    {
        return Attribute::get(fn (): string => '№ '.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT));
    }

    /**
     * The guest's first and last name.
     *
     * @return Attribute<string, never>
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => "{$this->first_name} {$this->last_name}");
    }
}
