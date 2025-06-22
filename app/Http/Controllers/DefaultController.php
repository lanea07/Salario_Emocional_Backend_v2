<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Facades\TempTable;
use App\ValueObjects\TempTableColumn;
use Illuminate\Support\Facades\Auth;

class DefaultController extends Controller {

    public function index() {
        $permissions = Auth::user()->getAllPermissions();
        return ApiResponse::sendResponse($permissions, __('controllers/default_controller.default_controller_response'), HttpStatusCodes::OK_200, true);
    }

    public function tempTableExample() {
        $result = TempTable::create('temp_table', [
            new TempTableColumn(
                name: 'id',
                type: 'int',
                options: 'NOT NULL AUTO_INCREMENT PRIMARY KEY'
            ),
        ])
            ->insert([
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ])
            ->select('t.id', 'u.id', 'u.name')
            ->from('temp_table as t')
            ->join('users as u', 't.id', '=', 'u.id')
            ->get();
        return ApiResponse::sendResponse($result);
    }
}
