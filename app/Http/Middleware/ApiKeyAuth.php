<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from header or query parameter
        $apiKey = $request->header('X-API-Key') 
               ?? $request->header('Authorization') 
               ?? $request->query('api_key');

        // Remove 'Bearer ' prefix if present
        if ($apiKey && str_starts_with($apiKey, 'Bearer ')) {
            $apiKey = substr($apiKey, 7);
        }

        // Check if API key is provided
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Please provide X-API-Key header or api_key query parameter.',
            ], 401);
        }

        // Find client by API key
        $client = ApiClient::findByApiKey($apiKey);

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
            ], 401);
        }

        // Check if client is active
        if (!$client->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'API key is inactive. Please contact administrator.',
            ], 403);
        }

        // Check IP whitelist
        $clientIp = $request->ip();
        if (!$client->isIpWhitelisted($clientIp)) {
            return response()->json([
                'success' => false,
                'message' => 'IP address is not whitelisted.',
            ], 403);
        }

        // Record usage
        $client->recordUsage();

        // Attach client to request for later use
        $request->merge(['api_client' => $client]);

        return $next($request);
    }
}
