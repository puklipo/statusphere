<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Record\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Revolution\Bluesky\Facades\Bluesky;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'did',
        'handle',
        'issuer',
        'avatar',
        'refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'refresh_token',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Latest status.
     */
    protected function status(): Attribute
    {
        return Attribute::get(function () {
            $res = Bluesky::listRecords(
                repo: $this->did,
                collection: Status::NSID,
                limit: 1,
            );

            $record = $res->collect('records')->first();

            if (empty($record)) {
                return null;
            }

            $date = Carbon::parse(data_get($record, 'value.createdAt'));
            data_set($record, 'value.createdAt', $date);

            return $record;
        })->shouldCache();
    }
}
