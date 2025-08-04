START TRANSACTION;
INSERT INTO `cache` (`key`,`value`,`expiration`) VALUES ('laravel_cache_spatie.permission.cache','a:3:{s:5:`alias`;a:4:{s:1:`a`;s:2:`id`;s:1:`b`;s:4:`name`;s:1:`c`;s:10:`guard_name`;s:1:`r`;s:5:`roles`;}s:11:`permissions`;a:8:{i:0;a:4:{s:1:`a`;i:1;s:1:`b`;s:12:`manage users`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}i:1;a:4:{s:1:`a`;i:2;s:1:`b`;s:12:`manage roles`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}i:2;a:4:{s:1:`a`;i:3;s:1:`b`;s:24:`access applications page`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}i:3;a:4:{s:1:`a`;i:4;s:1:`b`;s:14:`view dashboard`;s:1:`c`;s:3:`web`;s:1:`r`;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:4;a:4:{s:1:`a`;i:5;s:1:`b`;s:12:`view profile`;s:1:`c`;s:3:`web`;s:1:`r`;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:`a`;i:6;s:1:`b`;s:23:`view research documents`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}i:6;a:4:{s:1:`a`;i:7;s:1:`b`;s:16:`view evaluations`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}i:7;a:4:{s:1:`a`;i:8;s:1:`b`;s:25:`view event participations`;s:1:`c`;s:3:`web`;s:1:`r`;a:2:{i:0;i:1;i:1;i:3;}}}s:5:`roles`;a:3:{i:0;a:3:{s:1:`a`;i:1;s:1:`b`;s:5:`admin`;s:1:`c`;s:3:`web`;}i:1;a:3:{s:1:`a`;i:3;s:1:`b`;s:11:`super_admin`;s:1:`c`;s:3:`web`;}i:2;a:3:{s:1:`a`;i:2;s:1:`b`;s:4:`user`;s:1:`c`;s:3:`web`;}}}',1753429013);
INSERT INTO `roles` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (1,'admin','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (2,'user','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (3,'super_admin','web','2025-07-23 06:22:07','2025-07-23 06:22:07');
INSERT INTO `model_has_roles` (`role_id`,`model_type`,`model_id`) VALUES (1,'App\Models\User',1);
INSERT INTO `permissions` (`id`,`name`,`guard_name`,`created_at`,`updated_at`) VALUES (1,'manage users','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (2,'manage roles','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (3,'access applications page','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (4,'view dashboard','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (5,'view profile','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (6,'view research documents','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (7,'view evaluations','web','2025-07-23 06:22:07','2025-07-23 06:22:07'),
 (8,'view event participations','web','2025-07-23 06:22:07','2025-07-23 06:22:07');
INSERT INTO `role_has_permissions` (`permission_id`,`role_id`) VALUES (4,1),
 (5,1),
 (6,1),
 (7,1),
 (8,1),
 (3,1),
 (1,1),
 (2,1),
 (4,2),
 (5,2),
 (3,3),
 (2,3),
 (1,3),
 (4,3),
 (7,3),
 (8,3),
 (5,3),
 (6,3);
INSERT INTO `sessions` (`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) VALUES ('H4CuBAuMWCI4KrUDvTDvTe6QTauFaYFRHYt8HDnM',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibDVrY0drSzNYZW9NN2hKbFNjV0RMSGJUbXpFVXI1TWZUOXNzaEpkUiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9kYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1753343553);
INSERT INTO `users` (`id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`,`google_id`,`avatar`,`instructor_number`,`rank`,`phone_number`) VALUES (1,'Vino, Gian Kerry O.','2022310186@pampangastateu.edu.ph',NULL,'$2y$12$ZIhXUvuMcpVYyl72oRBtJ.ss8DD0yAd6WGPuavIkD7gwKusJNEpMi',NULL,'2025-07-23 06:30:38','2025-07-24 07:52:33','116989408190012039419','https://lh3.googleusercontent.com/a/ACg8ocJeVcsXjW_FTL15n42u56FWJkRhKeSYzYXEnw-5qwpf3hIxBHo=s400-c',NULL,NULL,NULL);
COMMIT;
