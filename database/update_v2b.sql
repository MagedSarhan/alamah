USE alamah_db;

-- Add description column (ignore if exists)
ALTER TABLE categories ADD COLUMN description VARCHAR(200) DEFAULT NULL AFTER label;
