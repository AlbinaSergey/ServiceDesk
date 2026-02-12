INSERT INTO branches (id, name, code) VALUES (1, 'Главный офис', 'HQ');
INSERT INTO users (id, username, full_name, role, pin_hash, branch_id, is_active)
VALUES (1, 'admin', 'System Admin', 'admin', '$2y$10$6O2Vy4h2ZxvI7k2Qw0I64eGZrV8f2z0wNQf.53mmx4nWJsmhQ6U8W', 1, 1);
INSERT INTO module_registry (module_key, name, status) VALUES
('tickets', 'Tickets', 'enabled'),
('catalog', 'Catalog', 'disabled'),
('contracts', 'Contracts', 'disabled');
INSERT INTO feature_flags (flag_key, is_enabled, description)
VALUES ('kanban_board', 0, 'Kanban board for tickets');
