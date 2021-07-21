<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    //
    public function index()
    {
        $articles = Article::all();

        return response()->json($articles, 200);
    }

    public function show(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);

        return response()->json($article, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);            

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json($errors, 401);
        }

        $article = Article::create([
            'title' => $request->title,
            'content' => $request->content,
        ]);


        return response()->json($article, 201);
    }

    public function update(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);            

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json($errors, 401);
        }

        $article->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json($article, 200);
    }

    public function delete(Request $request, $articleId)
    {
        $article = Article::findOrFail($articleId);
        $article->delete();

        //return response()->json(['message' => 'successfully deleted.'], 204);
        return response()->json(null, 204);
    }
}
