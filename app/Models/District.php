<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    public $table = 'districts';

    protected $fillable = [
        'id',
        'regency_id',
        'name',
        'is_active',
    ];

    // RELATIONSHIPS
    public function regency()
    {
        return $this->belongsTo(Regency::class);
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'district_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function samples()
    {
        return $this->hasMany(Sample::class);
    }

    public function larvae()
    {
        return $this->hasMany(Larvae::class);
    }

    public function ksh()
    {
        return $this->hasMany(Ksh::class, 'ksh_id', 'id');
    }

    public function abj()
    {
        return $this->hasMany(Abj::class);
    }

    // SCOPES
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });
    }

    public function scopeFilterByRegency($query, array $filters)
    {
        $query->when($filters['regency_id'] ?? false, function ($query, $regency_id) {
            $query->where('regency_id', $regency_id);
        });
    }

    public function scopeFilterByProvince($query, array $filters)
    {
        $query->when($filters['province_id'] ?? false, function ($query, $province_id) {
            $query->where('province_id', $province_id);
        });
    }

    public function scopeFilterByDistrict($query, array $filters)
    {
        $query->when($filters['district_id'] ?? false, function ($query, $district_id) {
            $query->where('district_id', $district_id);
        });
    }

    public function scopeFilterByVillage($query, array $filters)
    {
        $query->when($filters['village_id'] ?? false, function ($query, $village_id) {
            $query->where('village_id', $village_id);
        });
    }
}
