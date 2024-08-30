<?php

namespace App\Repositories;

use App\Models\Abj;
use App\Models\Ksh;
use App\Repositories\Interface\AbjInterface;
use Carbon\Carbon;

class AbjRepository implements AbjInterface
{
    private $abj;

    public function __construct(Abj $abj)
    {
        $this->abj = $abj;
    }

    public function getAllGroupByDistrict()
    {
        $abj = $this->abj
            ->with('district', 'village', 'ksh', 'ksh.district', 'ksh.village', 'ksh.detailKsh', 'ksh.detailKsh.tpaType')
            ->where('is_active', true)
            ->get()
            ->sortBy('created_at')
            ->groupBy('district_id');

        $data = [];

        foreach ($abj as $key => $value) {
            $data[$key]['province'] = Ksh::where('id', $value->first()->ksh_id)->first()->regency->province->name;
            $data[$key]['regency'] = Ksh::where('id', $value->first()->ksh_id)->first()->regency->name;
            $data[$key]['district'] = $value->first()->district->name;
            $data[$key]['village'] = $value->first()->village->name;
            $data[$key]['location'] = $value->map(function ($item) {
                return [
                    'village' => $item->ksh->village,
                    'coordinate' => $item->ksh->detailKsh->map(function ($item) {
                        return $item;
                    }),
                ];
            });
            $data[$key]['abj'] = $value->map(function ($item) {
                return [
                    'abj_total' => $item->abj_total,
                    'created_at' => $item->created_at,
                ];
            });
            $data[$key]['total_sample'] = $value->count();
            $data[$key]['total_check'] = $value->sum(function ($item) {
                return $item->ksh->detailKsh->count();
            });
            $data[$key]['min_long'] = $value->min(function ($item) {
                return $item->ksh->detailKsh->map(function ($item) {
                    return $item->longitude;
                })->min();
            });
            $data[$key]['max_long'] = $value->max(function ($item) {
                return $item->ksh->detailKsh->map(function ($item) {
                    return $item->longitude;
                })->max();
            });
            $data[$key]['abj_total'] = (int) $value->sum('abj_total') / $value->count();
            // indonesia
            $data[$key]['created_at'] = Carbon::parse($value->first()->created_at)->format('d-m-Y');
        }

        return $data;
    }

    public function getGeoJson() {}
}
