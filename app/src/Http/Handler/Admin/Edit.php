<?php
namespace App\Http\Handler\Admin;

use App\Model\Virsh;
use App\Http\Middleware\AuthMiddleware;
use Juzdy\Http\Handler;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Edit extends Handler
{
    public function __construct() {}

    /**
     * Register middleware for authentication
     */
    protected function registerMiddleware(): void
    {
        $this->addMiddleware(new AuthMiddleware());
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $virshModel = new Virsh();
        $id = $request->query('id');
        $error = '';
        $success = '';
        
        // Load existing poem if editing
        if ($id) {
            $virshModel->load((int)$id);
        }
        
        if ($request->isPost()) {
            try {
                $data = [
                    'title' => $request->post('title'),
                    'virsh' => $request->post('virsh'),
                    'youtube' => $request->post('youtube'),
                    'enabled' => $request->post('enabled') ? 1 : 0,
                ];
                
                // Handle file upload for illustration
                $file = $request->file('illustration');
                if ($file && isset($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '/home/runner/work/web/web/pub/uploads/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'poem_' . time() . '_' . uniqid() . '.' . $extension;
                    $targetPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $data['illustration'] = 'uploads/' . $filename;
                    }
                } elseif ($id && $virshModel->get('illustration')) {
                    // Keep existing illustration if no new file uploaded
                    $data['illustration'] = $virshModel->get('illustration');
                }
                
                $virshModel->setData($data)->save();
                $success = 'Вірш успішно збережено';
                
                // Redirect to list after successful save
                return $this->redirect('/?q=admin/index');
                
            } catch (\Exception $e) {
                $error = 'Помилка збереження: ' . $e->getMessage();
            }
        }
        
        return $this->render('edit', [
            'poem' => $virshModel,
            'error' => $error,
            'success' => $success,
            'isNew' => !$id
        ], 'admin');
    }
}
