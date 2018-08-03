<?php

namespace App\Http\Controllers;

use App\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function index() {
        return Keyword::orderBy('created_at', 'desc')->paginate(10);
    }

    public function show($id) {
        return Keyword::find($id);
    }

    public function store(Request $request) {
        $keyword = Keyword::create($request->all());
        return response()->json($keyword, 201);
    }

    public function update(Request $request, Keyword $keyword) {
        $keyword->update($request->all());
        return response()->json($keyword, 200);
    }

    public function delete(Request $request, Keyword $keyword) {
        $keyword->delete();
        return response()->json(null, 204);
    }
}
