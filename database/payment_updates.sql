ALTER TABLE bookings
    ADD COLUMN payment_method ENUM('bank_transfer', 'card', 'ewallet') DEFAULT NULL AFTER status,
    ADD COLUMN payment_status ENUM('unpaid', 'paid') NOT NULL DEFAULT 'unpaid' AFTER payment_method,
    ADD COLUMN payment_reference VARCHAR(40) DEFAULT NULL AFTER payment_status,
    ADD COLUMN transaction_code VARCHAR(120) DEFAULT NULL AFTER payment_reference,
    ADD COLUMN paid_at TIMESTAMP NULL DEFAULT NULL AFTER transaction_code,
    ADD UNIQUE KEY unique_payment_reference (payment_reference);

UPDATE bookings
SET payment_reference = CONCAT('TVY', LPAD(id, 6, '0'))
WHERE payment_reference IS NULL OR payment_reference = '';

