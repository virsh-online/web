<?php
namespace App\Http\Middleware;

use Juzdy\Http\Middleware\MiddlewareInterface;
use Juzdy\Http\HandlerInterface;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;
use Juzdy\Http\Response;

/**
 * Authentication Middleware
 * 
 * Ensures that the user is authenticated before processing the request.
 * Excludes login and logout routes from authentication check.
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Routes that should be excluded from authentication check
     */
    private const EXCLUDED_ROUTES = [
        'admin/login',
        'admin/logout',
    ];

    /**
     * Process the request.
     *
     * @param RequestInterface $request
     * @param HandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, HandlerInterface $handler): ResponseInterface
    {
        // Get current route
        $route = $request->query('q') ?? '';
        
        // Skip authentication for excluded routes
        if ($this->isExcludedRoute($route)) {
            return $handler->handle($request);
        }
        
        // Check if user is authenticated
        $adminUserId = $request->session('admin_user_id');

        if ($adminUserId === null) {
            // Store the intended URL for redirect after login
            $request->session('intended_url', $_SERVER['REQUEST_URI'] ?? '/admin/index');
            
            // Return redirect response instead of using header()
            return (new Response())
                ->reset()
                ->status(302)
                ->header('Location', '/?q=admin/login');
        }

        // User is authenticated, continue to next middleware or handler
        return $handler->handle($request);
    }

    /**
     * Check if the route should be excluded from authentication
     *
     * @param string $route
     * @return bool
     */
    private function isExcludedRoute(string $route): bool
    {
        $route = strtolower(trim($route, '/'));
        
        foreach (self::EXCLUDED_ROUTES as $excludedRoute) {
            $excludedRoute = strtolower(trim($excludedRoute, '/'));
            // Exact match or starts with the route followed by a slash
            if ($route === $excludedRoute || strpos($route, $excludedRoute . '/') === 0) {
                return true;
            }
        }
        
        return false;
    }
}
