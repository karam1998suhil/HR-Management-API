<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function index(): JsonResponse
    {
        $positions = Position::withCount('employees')->latest()->get();

        return response()->json($positions);
    }

    public function store(StorePositionRequest $request): JsonResponse
    {
        $position = Position::create($request->validated());

        return response()->json($position, 201);
    }

    public function show(Position $position): JsonResponse
    {
        // load employees that belong to this position
        return response()->json($position->load('employees'));
    }

    public function update(UpdatePositionRequest $request, Position $position): JsonResponse
    {
        $position->update($request->validated());

        return response()->json($position->fresh());
    }

    public function destroy(Position $position): JsonResponse
    {
        // employees with this position will have position_id set to null
        $position->delete();

        return response()->json(['message' => 'Position deleted successfully.']);
    }
}