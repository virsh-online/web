<?php
namespace App\Http\Middleware;

use App\Model\AdminUser;
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
     * Process the request.
     *
     * @param RequestInterface $request
     * @param HandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, HandlerInterface $handler): ResponseInterface
    {
        // Check if user is authenticated
        $adminUserId = $request->session('admin_user_id');

        if ($adminUserId === null) {
            // Store the intended URL for redirect after login
            $request->session('intended_url', $_SERVER['REQUEST_URI'] ?? '/admin/index');
            
            // Return redirect response instead of using header()
            return (new Response())
                ->reset()
                ->status(302)
                ->header('Location', '/admin/login');
        }
        
        // Verify user exists and is enabled in database
        try {
            $adminUserModel = new AdminUser();
            $adminUserModel->load((int)$adminUserId);
            
            if (!$adminUserModel->isLoaded() || !$adminUserModel->isEnabled()) {
                // User not found or disabled - clear session and redirect to login
                $request->session('admin_user_id', null);
                $request->session('intended_url', $_SERVER['REQUEST_URI'] ?? '/admin/index');
                
                return (new Response())
                    ->reset()
                    ->status(302)
                    ->header('Location', '/admin/login');
            }
        } catch (\Exception $e) {
            // Database error - clear session and redirect to login
            error_log('Auth middleware error: ' . $e->getMessage());
            $request->session('admin_user_id', null);
            
            return (new Response())
                ->reset()
                ->status(302)
                ->header('Location', '/admin/login');
        }

        // User is authenticated, continue to next middleware or handler
        return $handler->handle($request);
    }
}
