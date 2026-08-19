<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Domains\ECommerce\Models\Product;
use App\Services\AiAssistantService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    protected $aiService;

    public function __construct(AiAssistantService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function query(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'question' => 'required|string|max:500',
        ]);

        $product = Product::findOrFail($request->id);
        $answer = $this->aiService->generateAnswer($product, $request->question);

        return response()->json([
            'status' => 'success',
            'answer' => $answer,
        ]);
    }
}
