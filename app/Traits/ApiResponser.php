<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser{

    protected function successResponse($status, $data, $code)
	{
		return response()->json([
			'status'=> $status,
			'message' => "Your request has been processed successfully",
			'data' => $data
		], $code);
	}

	protected function errorResponse($status, $errors, $code)
	{
		return response()->json([
			'status'=> $status,
            'message' => "Your request failed to process",
			'errors' => $errors,
			'data' => null
		], $code);
	}

}
