<?php

namespace App\Domains\ECommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'user_id',
        'results_count',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $query, int $resultsCount, ?int $userId = null): void
    {
        self::create([
            'query' => $query,
            'user_id' => $userId,
            'results_count' => $resultsCount,
            'ip_address' => request()->ip(),
        ]);
    }

    public static function popular(int $limit = 10): array
    {
        return self::select('query')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('query')
            ->toArray();
    }
}

