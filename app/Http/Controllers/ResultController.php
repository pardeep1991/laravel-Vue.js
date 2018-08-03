<?php

namespace App\Http\Controllers;

use App\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request) {
        return Result::with('keyword')->orderBy('created_at', 'desc')->paginate($request->per_page);
    }

    public function search(Request $request) {
        $keys = $request->all();
        return Result::with('keyword')->whereHas('keyword', function ($query) use($keys) {
            foreach ($keys as $key => $value) {
                switch ($key) {
                    case 'keyword':
                    case 'keyword_category':
                        $query->where($key, 'like', '%'.$value.'%');
                        break;
                    default:
                        break;
                }
            }
        })->where(function ($query) use($keys) {
            foreach ($keys as $key => $value) {
                switch ($key) {
                    case 'url_category':
                    case 'search_engine':
                    case 'title':
                    case 'description':
                    case 'url':
                    case 'url_redirected':
                    case 'url_category':
                    case 'url_status':
                    case 'ip':
                    case 'ip_region':
                        $query->where($key, 'like', '%'.$value.'%');
                        break;
                    default:
                        break;
                }
            }
        })->orderBy('created_at', 'desc')->paginate($request->per_page);
    }

    public function update(Request $request, Result $result) {
        $result->update($request->all());
        return response()->json($result, 200);
    }

    public function delete(Request $request, Result $result) {
        $result->delete();
        return response()->json(null, 204);
    }
}
