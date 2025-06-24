<?php

namespace App\Services;

use App\Models\BenefitDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BenefitDetailService
{

    /**
     * Return all benefit details
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBenefitDetail(): Collection
    {
        return BenefitDetail::oldest('name')->get();
    }

    /**
     * Store a new benefit detail
     * 
     * @param array $benefitDetailData
     * @return \App\Models\BenefitDetail
     */
    public function saveBenefitDetail(array $benefitDetailData): BenefitDetail
    {
        $created = DB::transaction(function () use ($benefitDetailData) {
            return BenefitDetail::create($benefitDetailData);
        });
        return $created;
    }

    /**
     * Return a benefit detail by id
     * 
     * @param \App\Models\BenefitDetail $benefitDetail* 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBenefitDetailByID(BenefitDetail $benefitDetail): Collection
    {
        return $benefitDetail->with(['benefit'])->where('id', $benefitDetail->id)->get();
    }

    /**
     * Update a benefit detail
     * 
     * @param array $benefitDetailData
     * @param \App\Models\BenefitDetail $benefitdetail
     * @return \App\Models\BenefitDetail
     */
    public function updateBenefitDetail(array $benefitDetailData, BenefitDetail $benefitdetail): BenefitDetail
    {
        $updated = DB::transaction(function () use ($benefitDetailData, $benefitdetail) {
            return tap($benefitdetail)->update($benefitDetailData);
        });
        return $updated;
    }

    /**
     * Delete a benefit detail
     * 
     * @param \App\Models\BenefitDetail $benefitDetail
     * @return void
     * @throws \Exception
     */
    public function deleteBenefitDetail(BenefitDetail $benefitDetail): void
    {
        throw new \Exception('No se puede eliminar una configuración de beneficio');
    }

    /**
     * Return a datatable of benefit details
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDatatable()
    {
        $model = BenefitDetail::with(['benefit']);
        return DataTables::of($model)->toJson()->getData();
    }
}
