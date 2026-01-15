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
            try {
                $virshModel->load((int)$id);
                if (!$virshModel->isLoaded()) {
                    throw new \Exception('Вірш не знайдено');
                }
            } catch (\Exception $e) {
                $error = 'Помилка завантаження: ' . $e->getMessage();
            }
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
                    // Security validation
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    
                    $fileType = mime_content_type($file['tmp_name']);
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    
                    if (!in_array($fileType, $allowedTypes)) {
                        throw new \Exception('Недопустимий тип файлу. Тільки зображення дозволені.');
                    }
                    
                    if (!in_array($extension, $allowedExtensions)) {
                        throw new \Exception('Недопустиме розширення файлу.');
                    }
                    
                    if ($file['size'] > $maxSize) {
                        throw new \Exception('Файл занадто великий. Максимум 5MB.');
                    }
                    
                    // Use document root to determine upload directory
                    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? realpath(__DIR__ . '/../../../../../../pub');
                    $uploadDir = rtrim($docRoot, '/') . '/uploads/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $filename = 'poem_' . time() . '_' . uniqid() . '.' . $extension;
                    $targetPath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        $data['illustration'] = 'uploads/' . $filename;
                    }
                } elseif ($file && $file['error'] !== UPLOAD_ERR_NO_FILE && $file['error'] !== UPLOAD_ERR_OK) {
                    // Handle other upload errors
                    throw new \Exception('Помилка завантаження файлу: код ' . $file['error']);
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
