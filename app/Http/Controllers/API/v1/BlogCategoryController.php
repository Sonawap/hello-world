<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\category\CreateCategory;
use App\Models\BlogCategory;
use Exception;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = BlogCategory::with('blogs')->get();
        return ResponseController::response(true,[
            'categories' => $categories
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCategory $request)
    {
        try {
            $category = new BlogCategory();
            $category->title = $request->title;
            $category->user_id = auth()->id();
            $category->save();

            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Category has been created',
                'category' => $category
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);
            return ResponseController::response(true,[
                ResponseController::ERROR => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = BlogCategory::with('blogs')->where('id', $id)->first();
        if($category){
            return ResponseController::response(true,[
                'category' => $category
            ], Response::HTTP_OK);
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateCategory $request, $id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            if($request->title){
                $category->title = $request->title;
            }

            $category->save();
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Category has been updated',
                'category' => $category
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            report($e);
            return ResponseController::response(true,[
                ResponseController::ERROR => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = BlogCategory::where('id', $id)->first();
        if($category){
            try {
                $category->delete();
                return ResponseController::response(true,[
                    ResponseController::MESSAGE => 'Category has been deleted'
                ], Response::HTTP_OK);
            } catch (Exception $e) {
                report($e);
                return ResponseController::response(true,[
                    ResponseController::ERROR => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
