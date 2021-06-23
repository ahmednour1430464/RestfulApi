<?php

namespace App\Http\Middleware;

use App\Transformers\Transformer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        $transformedAttributes = [];
        foreach ($request->request->all() as $input => $value) {
            $transformedAttributes[$transformer::originalAttribute($input)] = $value;
        }
        $request->replace($transformedAttributes);
        $response = $next($request);
        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            $transformedErrors = [];
            $data = $response->getData();
            foreach ($data->error as $field => $error) {
               
                $transformedField = $transformer::TransformedAttribute($field);
                if ($transformedField=='') {
                   $transformedField=$field;
                }
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }
            $data->error=$transformedErrors;
            $response->setData($data);
        }
        return $response;
    }
}
