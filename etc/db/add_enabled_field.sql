-- Add 'enabled' field to virsh table for admin control
-- This allows admins to publish/unpublish poems

-- Add enabled column if it doesn't exist
ALTER TABLE virsh ADD COLUMN IF NOT EXISTS enabled TINYINT(1) DEFAULT 1 NOT NULL;

-- Set all existing poems as enabled by default
UPDATE virsh SET enabled = 1 WHERE enabled IS NULL;

-- Add index for better performance when filtering by enabled status
CREATE INDEX IF NOT EXISTS idx_virsh_enabled ON virsh(enabled);
