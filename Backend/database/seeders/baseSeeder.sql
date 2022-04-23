INSERT INTO `divisions` (`id`, `user_id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Kitchen', NULL, NULL, NULL),
(2, 1, 'Living room', NULL, NULL, NULL),
(3, 1, 'Garage', NULL, NULL, NULL);

INSERT INTO `equipments` (`id`, `user_id`, `equipment_type_id`, `name`, `description`, `division_id`, `consumption`, `standby`, `activity`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Toaster', NULL, 1, '1300.00', '0.10', 'Yes', '2022-04-11 07:52:14', '2022-04-11 07:52:14', NULL),
(2, 1, 2, 'TV', NULL, 2, '70.00', '0.10', 'Yes', '2022-04-11 07:53:04', '2022-04-11 07:53:04', NULL),
(3, 1, 3, 'Microwave', NULL, 1, '1000.00', '0.10', 'Yes', '2022-04-11 07:54:04', '2022-04-11 07:54:04', NULL),
(4, 1, 4, 'Garage Door', NULL, 3, '500.00', '0.10', 'Yes', '2022-04-11 07:56:05', '2022-04-11 07:56:05', NULL),
(5, 1, 5, 'Exhaust Fan', NULL, 1, '180.00', '0.10', 'Yes', '2022-04-11 07:59:27', '2022-04-11 07:59:27', NULL),
(6, 2, 1, 'Toaster', NULL, NULL, '1300.00', '0.10', 'Yes', '2022-04-11 07:52:14', '2022-04-11 07:52:14', NULL),
(7, 2, 2, 'TV', NULL, NULL, '70.00', '0.10', 'Yes', '2022-04-11 07:53:04', '2022-04-11 07:53:04', NULL),
(8, 2, 3, 'Microwave', NULL, NULL, '1000.00', '0.10', 'Yes', '2022-04-11 07:54:04', '2022-04-11 07:54:04', NULL),
(9, 2, 4, 'Garage Door', NULL, NULL, '500.00', '0.10', 'Yes', '2022-04-11 07:56:05', '2022-04-11 07:56:05', NULL),
(10, 2, 5, 'Exhaust Fan', NULL, NULL, '180.00', '0.10', 'Yes', '2022-04-11 07:59:27', '2022-04-11 07:59:27', NULL),
(11, 1, 8, 'Heater', NULL, 1, '2000.00', '0.10', 'Yes', '2022-04-18 20:01:07', '2022-04-18 20:01:07', NULL),
(12, 2, 8, 'Heater', NULL, NULL, '2000.00', '0.10', 'Yes', '2022-04-18 20:08:48', '2022-04-18 20:08:48', NULL);

INSERT INTO `equipment_types` (`id`, `name`, `activity`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Toaster', 'Yes', '2022-04-18 18:49:52', '2022-04-18 18:49:52', NULL),
(2, 'TV', 'Yes', '2022-04-18 18:50:04', '2022-04-18 18:50:04', NULL),
(3, 'Microwave', 'Yes', '2022-04-18 18:50:13', '2022-04-18 18:50:13', NULL),
(4, 'Garage Door', 'Yes', '2022-04-18 18:50:25', '2022-04-18 18:50:25', NULL),
(5, 'Exhaust Fan', 'Yes', '2022-04-18 18:50:34', '2022-04-18 18:50:34', NULL),
(6, 'Fridge', 'No', '2022-04-18 18:51:06', '2022-04-18 18:51:06', NULL),
(7, 'Freezer', 'No', '2022-04-18 18:51:24', '2022-04-18 18:51:24', NULL),
(8, 'Heater', 'Yes', '2022-04-18 19:59:28', '2022-04-18 19:59:28', NULL);

INSERT INTO `users` (`id`, `name`, `email`, `password`, `birthdate`, `get_started`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Fabio Gaspar', 'fabio.c.gaspar@mail.pt', '$2y$10$IIWDeXYbvfn7z/Ojw5CkreDgc7BDb41qLBxYNAdksoL3TtHizKmjy', '2001-12-04 00:00:00', 0, 'C', '2022-02-02 20:25:34', '2022-02-02 20:25:34', NULL),
(2, 'Daniel Carreira', 'daniel@mail.pt', '$2y$10$IIWDeXYbvfn7z/Ojw5CkreDgc7BDb41qLBxYNAdksoL3TtHizKmjy', '2001-08-10 22:00:00', 0, 'A', '2022-02-02 20:25:34', '2022-02-02 20:25:34', NULL),
(3, 'RapidMiner', 'rapidminer@ai.pt', '$2y$10$IIWDeXYbvfn7z/Ojw5CkreDgc7BDb41qLBxYNAdksoL3TtHizKmjy', '2001-08-10 22:00:00', 0, 'P', '2022-02-02 20:25:34', '2022-02-02 20:25:34', NULL);
