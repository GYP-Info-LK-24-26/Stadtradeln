-- Klarnamen wieder speichern (nur für Auswertung und Ehrung, angezeigt wird das Pseudonym).
-- Bestehende Accounts behalten name = NULL und werden beim Login nach dem Namen gefragt.
ALTER TABLE users ADD COLUMN name VARCHAR(255) NULL AFTER id;
ALTER TABLE pending_registrations ADD COLUMN name VARCHAR(255) NULL AFTER id;
