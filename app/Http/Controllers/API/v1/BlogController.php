<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\Blog\CreateBlog;
use App\Http\Requests\Blog\UpdateBlog;
use App\Models\Blog;
use App\Models\BlogCategory;
use Exception;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Blog::with('category')->get();
        return ResponseController::response(true,[
            'blogs' => $blogs
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateBlog $request)
    {
        try {
            $category = BlogCategory::where('id', $request->blog_categories_id)->first();
            if(!$category){
                return ResponseController::response(true,[
                    ResponseController::ERROR => "Category not found"
                ], Response::HTTP_BAD_REQUEST);
            }
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->cover_image = $request->cover_image;
            $blog->tags = json_encode($request->tags);
            $blog->body = $request->body;
            $blog->blog_categories_id = $category->id;
            $blog->user_id = auth()->id();
            $blog->save();

            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Blog has been created',
                'blog' => $blog
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
        $blog = Blog::with('category')->where('id', $id)->first();
        if($blog){
            return ResponseController::response(true,[
                'blog' => $blog
            ], Response::HTTP_OK);
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Blog not found'
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
    public function update(UpdateBlog $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->title = $request->title;
            if($request->cover_image){
                $blog->cover_image = $request->cover_image;
            }
            if($request->tags){
                $blog->tags = json_encode($request->tags);
            }
            if($request->body){
                $blog->body = $request->body;
            }
            if($request->blog_categories_id){
                $category = BlogCategory::where('id', $request->blog_categories_id)->first();
                if(!$category){
                    return ResponseController::response(true,[
                        ResponseController::ERROR => "Category not found"
                    ], Response::HTTP_BAD_REQUEST);
                }else{
                    $blog->blog_categories_id = $category->id;
                }
            }
            if($request->cover_image){
                $blog->cover_image = $request->cover_image;
            }

            $blog->user_id = auth()->id();
            $blog->save();
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Blog has been updated',
                'blog' => $blog
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
        $blog = Blog::where('id', $id)->first();
        if($blog){
            try {
                $blog->delete();
                return ResponseController::response(true,[
                    ResponseController::MESSAGE => 'Blog has been deleted'
                ], Response::HTTP_OK);
            } catch (Exception $e) {
                report($e);
                return ResponseController::response(true,[
                    ResponseController::ERROR => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Blog not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
