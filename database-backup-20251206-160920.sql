-- SQLite Database Export
-- Generated: 2025-12-06 16:09:24
-- Database: database/database.sqlite


-- Table structure for table `migrations`
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

-- Dumping data for table `migrations`
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2025_10_04_021755_add_profile_fields_to_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2025_10_04_021808_create_rooms_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2025_10_04_021820_create_bookings_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2025_10_04_021854_create_bills_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2025_10_04_021907_create_complaints_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2025_10_04_021930_create_payments_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2025_10_04_031320_add_profile_fields_to_users_table_v2', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('11', '2025_10_04_075511_remove_floor_and_room_type_from_rooms_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('12', '2025_10_07_102710_simplify_bills_table_for_room_rent_only', '1');


-- Table structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE "users" ("id" integer primary key autoincrement not null, "name" varchar not null, "email" varchar not null, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "phone" varchar, "address" text, "role" varchar check ("role" in ('admin', 'tenant', 'seeker')) not null default 'seeker', "profile_picture" varchar, "birth_date" date, "gender" varchar check ("gender" in ('male', 'female')), "occupation" varchar, "emergency_contact_name" varchar, "emergency_contact_phone" varchar, "id_card_number" varchar, "id_card_file" varchar);

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `phone`, `address`, `role`, `profile_picture`, `birth_date`, `gender`, `occupation`, `emergency_contact_name`, `emergency_contact_phone`, `id_card_number`, `id_card_file`) VALUES ('1', 'Admin Kos-Kosan', 'admin@koskosan.com', NULL, '$2y$12$XrjPw5wvbqp6Wjfk4kTXkO0J6mYhA7EpFfI5kjFFBWkS./LW6mcf2', NULL, '2025-10-07 10:32:26', '2025-10-07 10:32:26', '081234567890', 'Jl. Admin No. 1, Jakarta', 'admin', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `phone`, `address`, `role`, `profile_picture`, `birth_date`, `gender`, `occupation`, `emergency_contact_name`, `emergency_contact_phone`, `id_card_number`, `id_card_file`) VALUES ('2', 'Jane Smith', 'jane@example.com', NULL, '$2y$12$BszYAWPZX6tEz0C/tDE5VOj9zdG2n74/Qfrwi7Y.n.qqIpcDOE3Wa', NULL, '2025-10-07 10:32:27', '2025-10-07 10:38:22', '081234567892', 'Jl. Seeker No. 1, Jakarta', 'tenant', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);


-- Table structure for table `password_reset_tokens`
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));


-- Table structure for table `sessions`
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

-- Dumping data for table `sessions`
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('cOKByortJPfVQoQdmJpTn9IkEuE58HzPRMwbD2IP', '1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSE9mcFpOTzdaRWhhQ09SNUtpZUNiSzRRZ0pHY2p6MEhrcTY5a3hQTyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi90ZW5hbnRzIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', '1759833917');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('BHrz06kUpvvW4w20r7e47C5Lqet8GkZvJNpdRVwZ', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTk9FSlpoSURYUkUzeFllSkQ2Z0ZqeWlQd25STkVnUU9tM0JBd1VQdiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC90ZW5hbnQvZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', '1759980504');


-- Table structure for table `cache`
DROP TABLE IF EXISTS `cache`;
CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));


-- Table structure for table `cache_locks`
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));


-- Table structure for table `jobs`
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);


-- Table structure for table `job_batches`
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));


-- Table structure for table `failed_jobs`
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" text not null, "queue" text not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);


-- Table structure for table `rooms`
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE "rooms" ("id" integer primary key autoincrement not null, "room_number" varchar not null, "price" numeric not null, "description" text, "facilities" text, "status" varchar check ("status" in ('available', 'occupied', 'maintenance')) not null default 'available', "capacity" integer not null default '1', "area" varchar, "images" text, "created_at" datetime, "updated_at" datetime);

-- Dumping data for table `rooms`
INSERT INTO `rooms` (`id`, `room_number`, `price`, `description`, `facilities`, `status`, `capacity`, `area`, `images`, `created_at`, `updated_at`) VALUES ('1', 'A-101', '1500000', 'Kamar single dengan fasilitas lengkap, AC, WiFi, dan kamar mandi dalam.', '["AC","WiFi","Kamar Mandi Dalam","Lemari","Meja Belajar"]', 'occupied', '1', '3x4 meter', NULL, '2025-10-07 10:32:27', '2025-10-07 10:38:22');
INSERT INTO `rooms` (`id`, `room_number`, `price`, `description`, `facilities`, `status`, `capacity`, `area`, `images`, `created_at`, `updated_at`) VALUES ('6', 'kamar no 2', '500', NULL, '[]', 'available', '1', '3x4', '["rooms\/NRKxIBbYNd3jE6QTVJdJhEX7EJVL9HrJqMTwek1J.jpg"]', '2025-10-09 03:26:18', '2025-10-09 03:26:20');
INSERT INTO `rooms` (`id`, `room_number`, `price`, `description`, `facilities`, `status`, `capacity`, `area`, `images`, `created_at`, `updated_at`) VALUES ('7', 'kamar no 3', '500', NULL, '[]', 'available', '1', '3x4', '["rooms\/N53XdO8JVpM7HUEfnIlQGLzrE1Mkf69cdwQIVcrK.jpg"]', '2025-10-09 03:27:01', '2025-10-09 03:27:01');


-- Table structure for table `bookings`
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE "bookings" ("id" integer primary key autoincrement not null, "user_id" integer not null, "room_id" integer not null, "check_in_date" date not null, "check_out_date" date, "booking_fee" numeric not null, "status" varchar check ("status" in ('pending', 'confirmed', 'rejected', 'cancelled')) not null default 'pending', "documents" text, "payment_proof" varchar, "notes" text, "admin_notes" text, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("room_id") references "rooms"("id") on delete cascade);

-- Dumping data for table `bookings`
INSERT INTO `bookings` (`id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `booking_fee`, `status`, `documents`, `payment_proof`, `notes`, `admin_notes`, `created_at`, `updated_at`) VALUES ('1', '2', '1', '2025-10-14 00:00:00', '2025-10-17 00:00:00', '150000', 'confirmed', '["booking-documents\/fqTXnzht9oUpHRcMs9lMB3scjQUfLK1qCH0OhVl3.png"]', NULL, NULL, NULL, '2025-10-07 10:37:53', '2025-10-07 10:38:22');


-- Table structure for table `bills`
DROP TABLE IF EXISTS `bills`;
CREATE TABLE "bills" ("id" integer primary key autoincrement not null, "user_id" integer not null, "room_id" integer not null, "month" integer not null, "year" integer not null, "amount" numeric not null default '0', "total_amount" numeric not null, "status" varchar check ("status" in ('pending', 'paid', 'overdue')) not null default 'pending', "due_date" date not null, "paid_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("room_id") references "rooms"("id") on delete cascade);

-- Dumping data for table `bills`
INSERT INTO `bills` (`id`, `user_id`, `room_id`, `month`, `year`, `amount`, `total_amount`, `status`, `due_date`, `paid_at`, `created_at`, `updated_at`) VALUES ('3', '2', '1', '10', '2025', '500000', '500000', 'paid', '2025-10-31 00:00:00', '2025-10-07 10:42:40', '2025-10-07 10:40:42', '2025-10-07 10:42:40');


-- Table structure for table `complaints`
DROP TABLE IF EXISTS `complaints`;
CREATE TABLE "complaints" ("id" integer primary key autoincrement not null, "user_id" integer not null, "room_id" integer, "category" varchar not null, "title" varchar not null, "description" text not null, "location" varchar, "images" text, "priority" varchar check ("priority" in ('low', 'medium', 'high', 'urgent')) not null default 'medium', "status" varchar check ("status" in ('new', 'in_progress', 'resolved', 'closed')) not null default 'new', "admin_response" text, "resolved_at" datetime, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("room_id") references "rooms"("id") on delete set null);


-- Table structure for table `payments`
DROP TABLE IF EXISTS `payments`;
CREATE TABLE "payments" ("id" integer primary key autoincrement not null, "user_id" integer not null, "bill_id" integer not null, "amount" numeric not null, "payment_method" varchar check ("payment_method" in ('bank_transfer', 'cash', 'e_wallet', 'other')) not null, "payment_proof" varchar, "status" varchar check ("status" in ('pending', 'verified', 'rejected')) not null default 'pending', "verified_by" integer, "verified_at" datetime, "notes" text, "admin_notes" text, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("bill_id") references "bills"("id") on delete cascade, foreign key("verified_by") references "users"("id") on delete set null);

-- Dumping data for table `payments`
INSERT INTO `payments` (`id`, `user_id`, `bill_id`, `amount`, `payment_method`, `payment_proof`, `status`, `verified_by`, `verified_at`, `notes`, `admin_notes`, `created_at`, `updated_at`) VALUES ('1', '2', '3', '500001', 'e_wallet', 'payment-proofs/1iOzJVxxhDUX1aeV99CbHrA0wzntNt9rcjhNhDpK.jpg', 'verified', '1', '2025-10-07 10:42:40', NULL, NULL, '2025-10-07 10:42:11', '2025-10-07 10:42:40');

