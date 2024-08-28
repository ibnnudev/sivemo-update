<?php

namespace App\Repositories;

use App\Models\Abj;
use App\Models\Ksh;
use App\Models\User;
use App\Repositories\Interface\KshInterface;
use Illuminate\Support\Facades\DB;

class KshRepository implements KshInterface
{
    private $ksh;

    private $abj;

    public function __construct(Ksh $ksh, Abj $abj)
    {
        $this->ksh = $ksh;
        $this->abj = $abj;
    }

    public function getAll()
    {
        $samples = $this->ksh
            ->with(['regency', 'district', 'village', 'detailKsh' => function ($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true) // Filter data utama Ksh
            ->get();

        $samples->map(function ($sample) {
            $sample->total_sample = $sample->detailKsh->count();

            return $sample;
        });

        return $samples;
    }

    public function getById($id)
    {
        return $this->ksh->with(['regency', 'district', 'village', 'detailKsh'])->find($id);
    }

    public function create($attributes)
    {
        return $this->ksh->create([
            'regency_id' => $attributes['regency_id'],
            'district_id' => $attributes['district_id'],
            'village_id' => $attributes['village_id'],
            'latitude' => $attributes['latitude'],
            'longitude' => $attributes['longitude'],
        ]);
    }

    public function edit($attributes, $id)
    {
        DB::beginTransaction();
        try {
            $this->ksh->find($id)->update([
                'regency_id' => $attributes['regency_id'],
                'district_id' => $attributes['district_id'],
                'village_id' => $attributes['village_id'],
                'latitude' => $attributes['latitude'],
                'longitude' => $attributes['longitude'],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        try {
            $this->abj->where('ksh_id', $id)->update([
                'district_id' => $attributes['district_id'],
                'village_id' => $attributes['village_id'],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        DB::commit();
    }

    public function createMember($attributes)
    {
        return User::create([
            'name' => $attributes['name'],
            'sex' => $attributes['sex'],
            'birthday' => date('Y-m-d', strtotime($attributes['birthday'])),
            'phone' => $attributes['phone'],
            'email' => $attributes['email'],
            'address' => $attributes['address'],
            'password' => password_hash($attributes['password'], PASSWORD_DEFAULT),
            'role_id' => User::KHS_ROLE,
        ]);
    }

    public function getAllMember()
    {
        return User::where('role', 'ksh')->get();
    }

    public function changeStatusMember($attributes)
    {
        return User::find($attributes['id'])->update([
            'is_active' => $attributes['status'],
        ]);
    }

    public function delete($id)
    {
        return $this->ksh->find($id)->update([
            'is_active' => false,
        ]);

    }

    public function delete_abj($id)
    {
        return $this->abj->find($id)->update([
            'is_active' => false,
        ]);
    }
}
