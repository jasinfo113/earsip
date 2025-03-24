<?php

namespace App\Models\Api\V1;

use App\Models\General\Reference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class News extends Model
{
    protected $table = 'cms_news';

    protected $hidden = [
        'platform_ids',
        'category_id',
        'description',
        'url',
        'ref_action',
        'ref_data',
        'headline',
        'pin',
        'duration',
        'status',
        'created_at',
        'created_from',
        'created_by',
        'updated_at',
        'updated_from',
        'updated_by',
        'is_deleted',
        'deleted_at',
        'deleted_from',
        'deleted_by',
    ];

    protected $appends = ['estimation', 'category'];

    public function scopeActive(Builder $query)
    {
        $query->where(['status' => 1, 'is_deleted' => 0]);
        $query->whereRaw('FIND_IN_SET(?, platform_ids)', [2]);
    }

    public function scopeHeadline(Builder $query)
    {
        $query->where('headline', 1);
    }

    public function scopePin(Builder $query)
    {
        $query->where('pin', 1);
    }

    public function getImageAttribute()
    {
        $image = $this->attributes['image'] ?? "";
        return _diskPathUrl('uploads', $image, asset('assets/images/default.png'));
    }

    public function getEstimationAttribute()
    {
        return ($this->duration ? number_format($this->duration) . ' menit baca' : '');
    }

    public function getCategoryAttribute()
    {
        return Reference::active()->find($this->category_id);
    }

    public function scopeApi(Builder $query, int $user_id)
    {
        $query
            ->selectRaw('cms_news.*')
            ->selectRaw('IF(cms_news_view.id IS NOT NULL, 1,0) AS is_viewed')
            ->selectRaw('IF(cms_news_share.id IS NOT NULL, 1,0) AS is_shared')
            ->selectRaw("CONCAT('" . env('WEB_URL', 'https://pemadam.jakarta.go.id/') . "berita/',STRING_REPLACE(cms_news.title),'-',cms_news.id) AS url")
            ->leftJoin('cms_news_view', function (JoinClause $join) use ($user_id) {
                $join->on('cms_news.id', 'cms_news_view.news_id')
                    ->where(['cms_news_view.created_by' => $user_id]);
            })
            ->leftJoin('cms_news_share', function (JoinClause $join) use ($user_id) {
                $join->on('cms_news.id', 'cms_news_share.news_id')
                    ->where(['cms_news_share.created_by' => $user_id]);
            });
    }

    protected function casts(): array
    {
        return [
            'date' => 'date:d F Y',
            'category' => 'array',
            'ref_data' => 'array',
            'duration' => 'integer',
            'is_viewed' => 'boolean',
            'is_shared' => 'boolean',
        ];
    }
}
