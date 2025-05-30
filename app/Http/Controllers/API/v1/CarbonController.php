<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Carbon;
use App\Models\SolarPanel;
use App\Services\CarbonCalculator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\API\v1\StoreCarbonRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CarbonController extends Controller
{
    public function store(StoreCarbonRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $solarPanel = SolarPanel::find($validatedData['solar_panel_id']);

            if (!$solarPanel) {
                return errorResponse(
                    message: 'Solar panel not found.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }
            // Calculate CO2 saved and equivalent trees
            $co2Saved = CarbonCalculator::calculateCO2Saved($solarPanel->energy_produced, $validatedData['emission_factor']);
            $equivalentTrees = CarbonCalculator::calculateEquivalentTreesPlanted($co2Saved);

            $carbon = Carbon::create([
                'solar_panel_id' => $validatedData['solar_panel_id'],
                'co2_saved' => $co2Saved,
                'equivalent_trees' => $equivalentTrees,
            ]);

            return successResponse(
                data: $carbon,
                message: 'Carbon record created successfully.',
                statusCode: Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            Log::error('Failed to create carbon record: ' . $e->getMessage());

            return errorResponse(
                message: 'An error occurred while creating the carbon record.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $carbon = Carbon::findOrFail($id);
        
            if ($carbon->solarPanel->user_id !== Auth::id()) {
                return errorResponse(
                    message: 'You are not authorized to view this carbon data.',
                    statusCode: Response::HTTP_FORBIDDEN
                );
            }
            return successResponse(
                data: $carbon,
                message: "Carbon fetched successfully.",
                statusCode: Response::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {

            return errorResponse(
                message: 'Carbon not found.',
                statusCode: Response::HTTP_NOT_FOUND,
                errors: $e->getMessage()
            );
        }
        catch (\Exception $e) {

            return errorResponse(
                message: 'An error occurred while fetching Carbon.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: $e->getMessage(),
            );
        }

    }


}
