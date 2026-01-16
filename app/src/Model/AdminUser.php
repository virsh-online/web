<?php
namespace App\Model;

use Juzdy\Model;

class AdminUser extends Model
{
    protected string $table = 'admin_user';
    
    /**
     * Find admin user by email
     * 
     * @param string $email
     * @return AdminUser|null
     */
    public function findByEmail(string $email): ?AdminUser
    {
        $collection = $this->getCollection();
        $collection->addFilter(['email' => $email]);
        
        if (!$collection->isEmpty()) {
            foreach ($collection as $item) {
                return $item;
            }
        }
        
        return null;
    }
    
    /**
     * Verify if user is enabled
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->get('enabled');
    }
    
    /**
     * Verify password
     * 
     * @param string $password
     * @return bool
     */
    public function verifyPassword(string $password): bool
    {
        $hashedPassword = $this->get('password');
        
        if (empty($hashedPassword)) {
            return false;
        }
        
        return password_verify($password, $hashedPassword);
    }
    
    /**
     * Set password with hashing
     * 
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->set('password', $hashedPassword);
        return $this;
    }
}
