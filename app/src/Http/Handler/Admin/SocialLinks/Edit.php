<?php
namespace App\Http\Handler\Admin\SocialLinks;

use App\Http\Handler\Admin\AdminHandler;
use App\Model\SocialLink;
use Juzdy\Http\RequestInterface;
use Juzdy\Http\ResponseInterface;

class Edit extends AdminHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $socialLinkModel = new SocialLink();
        $id = $request->query('id');
        $error = '';
        $success = '';
        
        // Load existing link if editing
        if ($id) {
            try {
                $socialLinkModel->load((int)$id);
                if (!$socialLinkModel->isLoaded()) {
                    throw new \Exception('Посилання не знайдено');
                }
            } catch (\Exception $e) {
                $error = 'Помилка завантаження: ' . $e->getMessage();
            }
        }
        
        if ($request->isPost()) {
            try {
                $data = [
                    'name' => $request->post('name'),
                    'url' => $request->post('url'),
                    'icon' => $request->post('icon'),
                    'enabled' => $request->post('enabled') ? 1 : 0,
                    'sort_order' => (int)$request->post('sort_order', 0),
                ];
                
                // Validate input
                $validationErrors = $this->validateInput($data);
                if (!empty($validationErrors)) {
                    throw new \Exception(implode('; ', $validationErrors));
                }
                
                $data = $this->sanitizeInput($data);
                
                $socialLinkModel->setData($data)->save();
                $success = 'Посилання успішно збережено';
                
                // Redirect to list after successful save
                return $this->redirect('/?q=admin/sociallinks/index');
                
            } catch (\Exception $e) {
                $error = 'Помилка збереження: ' . $e->getMessage();
            }
        }
        
        return $this->render('sociallinks/edit', [
            'link' => $socialLinkModel,
            'error' => $error,
            'success' => $success,
            'isNew' => !$id
        ], 'admin');
    }

    private function validateInput(array $data): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Назва не може бути порожньою';
        }
        
        if (empty($data['url'])) {
            $errors[] = 'URL не може бути порожнім';
        }
        
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Некоректний URL';
        }
        
        return $errors;
    }

    private function sanitizeInput(array $data): array
    {
        return [
            'name' => htmlspecialchars(trim($data['name'])),
            'url' => filter_var(trim($data['url']), FILTER_SANITIZE_URL),
            'icon' => htmlspecialchars(trim($data['icon'])),
            'enabled' => isset($data['enabled']) && $data['enabled'] ? 1 : 0,
            'sort_order' => (int)$data['sort_order'],
        ];
    }
}
