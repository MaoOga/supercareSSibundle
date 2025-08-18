-- Add reset token columns to nurses table for password reset functionality
ALTER TABLE nurses 
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expiry TIMESTAMP NULL;

-- Add index for better performance when searching by reset token
CREATE INDEX idx_reset_token ON nurses(reset_token);
