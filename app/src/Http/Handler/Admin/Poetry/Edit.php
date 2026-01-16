<?php
namespace App\Http\Handler\Admin\Poetry;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\Virsh;
use Juzdy\Config;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Edit extends AdminHandler
{
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
                $illustrationPath = $this->uploadIllustration($request, $id, $virshModel);
                
                $data = [
                    'title' => $request->post('title'),
                    'virsh' => $request->post('virsh'),
                    'youtube' => $request->post('youtube'),
                    'enabled' => $request->post('enabled') ? 1 : 0,
                    'illustration' => $illustrationPath,
                ];
                
                
                // Validate input
                $validationErrors = $this->validateInput($data);
                if (!empty($validationErrors)) {
                    throw new \Exception(implode('; ', $validationErrors));
                }
                
                $data = $this->sanitizeInput($data);
                
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

    private function validateInput(array $data): array
    {
        $errors = [];
        
        if (empty($data['title'])) {
            $errors[] = 'Заголовок не може бути порожнім';
        }
        
        if (empty($data['virsh'])) {
            $errors[] = 'Вірш не може бути порожнім';
        }
        
        return $errors;
    }

    private function sanitizeInput(array $data): array
    {
        $sanitized = [
            'title' => htmlspecialchars(trim($data['title'])),
            'virsh' => htmlspecialchars(trim($data['virsh'])),
            'youtube' => filter_var(trim($data['youtube']), FILTER_SANITIZE_URL),
            'enabled' => isset($data['enabled']) && $data['enabled'] ? 1 : 0,
        ];
        
        // Only include illustration if it's set
        if (isset($data['illustration'])) {
            $sanitized['illustration'] = $data['illustration'];
        }
        
        return $sanitized;
    }

    private function uploadIllustration(RequestInterface $request, ?int $id, Virsh $virshModel): string
    {
        $file = $request->file('illustration');

        $file = is_array($file) && isset($file[0]) ? $file[0] : null;

        if (!is_array($file)) {
            throw new \Exception('Неправильний формат файлу завантаження.');
        }
        
        // If no file was uploaded, keep existing illustration
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $id && $virshModel->get('illustration') ? $virshModel->get('illustration') : '';
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Помилка завантаження файлу: код ' . $file['error']);
        }
        
        // Security validation
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Use finfo for reliable MIME type detection
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new \Exception('Не вдалося ініціалізувати перевірку типу файлу.');
        }
        
        $fileType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if ($fileType === false) {
            throw new \Exception('Не вдалося визначити тип файлу.');
        }
        
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new \Exception('Недопустимий тип файлу. Тільки зображення дозволені (JPG, PNG, GIF, WEBP).');
        }
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new \Exception('Недопустиме розширення файлу.');
        }
        
        if ($file['size'] > $maxSize) {
            throw new \Exception('Файл занадто великий. Максимум 5MB.');
        }
        
        // Get upload directory from configuration
        $uploadDir = Config::get('path.uploads');
        if (!$uploadDir) {
            throw new \Exception('Конфігурація директорії завантажень не знайдена.');
        }
        
        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception('Не вдалося створити директорію для завантажень.');
            }
        }
        
        // Generate unique filename
        $filename = 'poem_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = rtrim($uploadDir, '/') . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new \Exception('Не вдалося завантажити файл.');
        }
        
        return $filename;
    }
}
