<?php

namespace App\Services;

use App\Models\BenefitUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class AdminService
{
    /**
     * Return all users benefits using the filters in the request
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBenefits(Request $request): Collection
    {
        return BenefitUser::with(
            [
                'user' => function ($q) {
                    $q->select('id', 'name', 'dependency_id');
                },
                'user.dependency' => function ($q) {
                    $q->select('id', 'name');
                },
                'benefits' => function ($q) {
                    $q->select('id', 'name');
                    $q->exclude(['encoded_logo']);
                },
                'benefit_detail' => function ($q) {
                    $q->select('id', 'name');
                },
            ]
        )
            ->when(isset($request->year), function ($q) use ($request) {
                return $q->whereYear('created_at', '=', $request->year);
            })
            ->when(isset($request->benefit_id), function ($q) use ($request) {
                return $q->where('benefit_id', '=', $request->benefit_id);
            })
            ->when(isset($request->user_id), function ($q) use ($request) {
                return $q->where('user_id', '=', $request->user_id);
            })
            ->when(isset($request->dependency_id), function ($q) use ($request) {
                return $q->whereRelation('user', function ($q) use ($request) {
                    $q->where('dependency_id', '=', $request->dependency_id);
                });
            })
            ->when(isset($request->is_approved), function ($q) use ($request) {
                return $q->where('is_approved', '=', $request->is_approved);
            })
            ->get();
    }

    /**
     * Returns users benefits grouped by benefit
     * 
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllGroupedBenefits(Request $request)
    {
        return BenefitUser::with([
            'user' => function ($q) {
                $q->select('id', 'name', 'dependency_id');
            },
            'user.dependency' => function ($q) {
                $q->select('id', 'name');
            },
            'benefits' => function ($q) {
                $q->select('id', 'name');
            },
        ])
            ->when(isset($request->year), function ($q) use ($request) {
                return $q->whereYear('created_at', '=', $request->year);
            })
            ->when(isset($request->benefit_id), function ($q) use ($request) {
                return $q->where('benefit_id', '=', $request->benefit_id);
            })
            ->when(isset($request->user_id), function ($q) use ($request) {
                return $q->where('user_id', '=', $request->user_id);
            })
            ->when(isset($request->dependency_id), function ($q) use ($request) {
                return $q->whereRelation('user', function ($q) use ($request) {
                    $q->where('dependency_id', '=', $request->dependency_id);
                });
            })
            ->when(isset($request->is_approved), function ($q) use ($request) {
                return $q->where('is_approved', '=', $request->is_approved);
            })
            ->get([
                'id',
                'benefit_id',
                'user_id',
                'created_at',
            'is_approved',
                'benefit_begin_time',
            ])->groupBy('benefits.name');
    }
}
