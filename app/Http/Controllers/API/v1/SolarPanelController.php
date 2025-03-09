<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SolarPanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $user = Auth::user();
            $solarPanels = $user->solarPanels()->with('carbon')->get();

            return successResponse(
                data: $solarPanels,
                message: "Solar panels fetched successfully.",
                statusCode: Response::HTTP_OK
            );

        } catch (\Exception $e) {
            Log::error('Failed to fetch solarPanels ' . $e->getMessage());

            return errorResponse(
                message: 'An error occurred while fetching solar panels.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors:$e->getMessage()
            );
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $user = Auth::user();

            $solarPanel = $user->solarPanels()->with('carbon')->findOrFail($id);


            return successResponse(
                data: $solarPanel,
                message: "Solar panel fetched successfully.",
                statusCode: Response::HTTP_OK
            );


        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            return errorResponse(
                message: 'Solar panel not found.',
                statusCode: Response::HTTP_NOT_FOUND,
                errors: $e->getMessage()
            );
        }
        catch (\Exception $e) {

           return errorResponse(
               message: 'An error occurred while fetching solarPanel.',
               statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
               errors: $e->getMessage(),
           );
        }

    }


}
