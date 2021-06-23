<?php

namespace  App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 *  ApiResponser
 */
trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code)->header('Access-Control-Allow-Origin','*');
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code)->header('Access-Control-Allow-Origin','*');
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;
        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance, $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse($collection, $code);
        }
        $transformer = $collection->first()->transformer;

        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);


        return $this->successResponse($collection, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    private function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    private function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }
    private function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);
            if (isset($attribute, $value)) {
                $collection = $collection->where($attribute, $value);
            }
        }
        return $collection;
    }
    private function paginate(Collection $collection)
    {
        $rules=[
            'per_page'=>'integer|min:2|max:50'
        ];

        Validator::validate(request()->all(),$rules);

        $currentPage=LengthAwarePaginator::resolveCurrentPage();
        $perPage=15;
        if (request()->has('per_page')) {
            $perPage=(int)request()->per_page;
        }

        $result=$collection->slice(($currentPage-1)*$perPage,$perPage)->values();

        $paginated=new LengthAwarePaginator($result,$collection->count(),$perPage,$currentPage,[
            'path'=>LengthAwarePaginator::resolveCurrentPath(),
        ]);
        $paginated->appends(request()->all());
        return $paginated;
    }
}
