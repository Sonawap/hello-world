<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\API\ResponseController;
use App\Http\Requests\Career\CreateCareer;
use App\Http\Requests\Career\UpdateCareer;
use App\Models\Career;
use Exception;

class CareerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $careers = Career::all();
        return ResponseController::response(true,[
            'careers' => $careers
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCareer $request)
    {
        try {
            $career = new Career();
            $career->title = $request->title;
            $career->type = $request->type;
            $career->location = $request->location;
            $career->details = $request->details;
            $career->requirement = $request->requirement;
            $career->user_id = auth()->id();
            $career->save();

            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Career has been created',
                'career' => $career
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
        $career = Career::where('id', $id)->first();
        if($career){
            return ResponseController::response(true,[
                'career' => $career
            ], Response::HTTP_OK);
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Career not found'
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
    public function update(UpdateCareer $request, $id)
    {
        try {
            $career = Career::findOrFail($id);
            if($request->title){
                $career->title = $request->title;
            }
            if($request->type){
                $career->type = $request->type;
            }
            if($request->location){
                $career->location = $request->location;
            }
            if($request->details){
                $career->details = $request->details;
            }
            if($request->requirement){
                $career->requirement = $request->requirement;
            }
            $career->save();

            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Career has been updated',
                'career' => $career
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
        $career = Career::where('id', $id)->first();
        if($career){
            try {
                $career->delete();
                return ResponseController::response(true,[
                    ResponseController::MESSAGE => 'Career has been deleted'
                ], Response::HTTP_OK);
            } catch (Exception $e) {
                report($e);
                return ResponseController::response(true,[
                    ResponseController::ERROR => $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }else{
            return ResponseController::response(true,[
                ResponseController::MESSAGE => 'Career not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
