-- phpMyAdmin SQL Dump
-- version 4.4.15.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2016 at 02:20 AM
-- Server version: 5.6.28
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manafx`
--

-- --------------------------------------------------------

--
-- Table structure for table `fx_api`
--

CREATE TABLE IF NOT EXISTS `fx_api` (
  `user_id` bigint(20) NOT NULL,
  `api_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fx_api`
--

INSERT INTO `fx_api` (`user_id`, `api_key`, `ip_address`) VALUES
(1, 'XW4MfjeaWVKsjLnFVG3lh4VJbkJ4O83I', '__IPLIST__,');

-- --------------------------------------------------------

--
-- Table structure for table `fx_menus`
--

CREATE TABLE IF NOT EXISTS `fx_menus` (
  `menu_id` int(11) NOT NULL,
  `menu_parent` int(11) NOT NULL DEFAULT '0',
  `menu_type` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_title` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `menu_roles` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_status` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fx_menus`
--

INSERT INTO `fx_menus` (`menu_id`, `menu_parent`, `menu_type`, `menu_key`, `menu_title`, `menu_action`, `menu_roles`, `menu_status`, `menu_description`) VALUES
(1, 0, 'i', 'main-menu', 'Main Menu', ' ', 'data,*,', 'A', 'Simple Home Menu'),
(2, 0, 'i', 'secondary-menu', 'Secondary Menu', ' ', 'data,*,', 'A', 'Simple HomSecondary Menu');

-- --------------------------------------------------------

--
-- Table structure for table `fx_options`
--

CREATE TABLE IF NOT EXISTS `fx_options` (
  `option_id` bigint(20) unsigned NOT NULL,
  `option_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_ci,
  `option_autoload` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `option_identity` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `option_description` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fx_options`
--

INSERT INTO `fx_options` (`option_id`, `option_name`, `option_value`, `option_autoload`, `option_identity`, `option_description`) VALUES
(1, 'title', 'MANA FRAMEWORK', 'N', 'application', ''),
(2, 'description', 'Mana Application Framework, powered by PhalconPHP', 'N', 'application', ''),
(3, 'baseUrl', 'http://manafx.dev', 'N', 'application', ''),
(4, 'admin_email', 'admin@manafx.com', 'N', 'application', ''),
(5, 'users_can_register', '1', 'N', 'application', ''),
(6, 'default_role', '3', 'N', 'application', ''),
(7, 'timezone_identifier', 'Asia/Jakarta', 'N', 'application', ''),
(8, 'date_format', 'Y/m/d', 'N', 'application', ''),
(9, 'time_format', 'H:i:s', 'N', 'application', ''),
(10, 'start_of_week', '0', 'N', 'system', ''),
(11, 'template', 'manafx-bootstrap', 'N', 'application', ''),
(12, 'theme', 'default', 'N', 'application', ''),
(13, 'active_modules', 'a:0:{}', 'N', 'application', 'Option to store an active modules'),
(14, 'query_limit', '200', 'N', 'database', 'Query limit per page'),
(15, 'grid_rows', '20', 'Y', 'public', 'Default grid row count to display'),
(16, 'debug_mode', 'on', 'N', 'system', ''),
(17, 'profiler_mode', 'off', 'N', 'system', ''),
(18, 'maintenance_mode', 'off', 'N', 'system', ''),
(19, 'offline_message_mode', 'custom', 'N', 'system', ''),
(20, 'offline_message_backend', 'Down for maintenance.', 'N', 'system', ''),
(21, 'offline_message_frontend', 'We are performing scheduled maintenance. We should be back online shortly. Thanks for your patience.', 'N', 'system', ''),
(22, 'mail_sending', 'on', 'N', 'application', NULL),
(23, 'mail_massmail', 'off', 'N', 'application', NULL),
(24, 'mail_from_email', 'donotreply@manafx.com', 'N', 'application', NULL),
(25, 'mail_from_name', 'ManaFx', 'N', 'application', NULL),
(26, 'mail_mailer', 'phpmail', 'N', 'application', NULL),
(27, 'mail_sendmail_path', '/usr/sbin/sendmail', 'N', 'application', NULL),
(28, 'mail_smtp_auth', 'no', 'N', 'application', NULL),
(29, 'mail_smtp_security', 'none', 'N', 'application', NULL),
(30, 'mail_smtp_host', 'localhost', 'N', 'application', NULL),
(31, 'mail_smtp_port', '25', 'N', 'application', NULL),
(32, 'mail_smtp_username', '', 'N', 'application', NULL),
(33, 'mail_smtp_password', '', 'N', 'application', NULL),
(34, 'frontpage_mode', 'route_to', 'N', 'system', ''),
(35, 'route_to', 'a:3:{s:6:"module";s:6:"manafx";s:10:"controller";s:5:"index";s:6:"action";s:5:"index";}', 'N', 'system', ''),
(36, 'redirect_to', 'http://manafx.com', 'N', 'system', '');

-- --------------------------------------------------------

--
-- Table structure for table `fx_permissions`
--

CREATE TABLE IF NOT EXISTS `fx_permissions` (
  `permission_id` int(10) unsigned NOT NULL,
  `permission_role_id` int(10) unsigned NOT NULL,
  `permission_module` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_controller` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_action` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_usermeta`
--

CREATE TABLE IF NOT EXISTS `fx_usermeta` (
  `usermeta_id` bigint(20) unsigned NOT NULL,
  `usermeta_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `usermeta_key` varchar(168) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usermeta_value` longtext COLLATE utf8mb4_unicode_ci,
  `usermeta_tag` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_users`
--

CREATE TABLE IF NOT EXISTS `fx_users` (
  `user_id` bigint(20) unsigned NOT NULL,
  `user_firstname` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_lastname` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_username` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_roles` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_registered` datetime NOT NULL,
  `user_activation_key` mediumtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fx_users`
--

INSERT INTO `fx_users` (`user_id`, `user_firstname`, `user_lastname`, `user_email`, `user_username`, `user_password`, `user_roles`, `user_status`, `user_registered`, `user_activation_key`) VALUES
(1, 'Superz', 'Administrator', 'sa@local.host', 'sa', '$2a$12$Ytcqf7pteXTZC2pvDcw7BOlW2Kv3Z1WAlHct5kQ4xWNE8b2pxKjwa', 'data,1,', 'A', '2014-07-08 16:00:41', ''),
(2, 'Henry', 'Gundala', 'henry@gundala.petir', 'admin', '$2a$12$PlDUZvijBzvGAySG8iwLnuKaiN9joKY1zEoMlzAtT.xYy.7E9.cM2', 'data,2,', 'A', '2015-06-22 05:08:17', ''),
(3, 'user', 'saja', 'user@manafx.com', 'user', '$2a$12$dbsaxh4jRlhfnVgCKgLj2e5BujAvPYQfJs.BxxA905aGgNys00Tn2', 'data,3,', 'A', '2015-09-08 09:21:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_agents`
--

CREATE TABLE IF NOT EXISTS `fx_user_agents` (
  `id` int(10) NOT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_email_confirmations`
--

CREATE TABLE IF NOT EXISTS `fx_user_email_confirmations` (
  `email_confirmation_id` bigint(20) unsigned NOT NULL,
  `email_confirmation_user_id` bigint(20) unsigned NOT NULL,
  `email_confirmation_code` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_confirmation_created` int(10) unsigned NOT NULL,
  `email_confirmation_modified` int(10) unsigned DEFAULT NULL,
  `email_confirmation_confirmed` char(1) COLLATE utf8mb4_unicode_ci DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_failed_logins`
--

CREATE TABLE IF NOT EXISTS `fx_user_failed_logins` (
  `failed_login_id` bigint(20) unsigned NOT NULL,
  `failed_login_user_id` bigint(20) unsigned DEFAULT NULL,
  `failed_login_ip_address` char(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_login_user_agent_id` int(10) NOT NULL,
  `failed_login_time` int(4) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_password_changes`
--

CREATE TABLE IF NOT EXISTS `fx_user_password_changes` (
  `password_change_id` bigint(20) unsigned NOT NULL,
  `password_change_user_id` bigint(20) unsigned NOT NULL,
  `password_change_ip_address` char(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_change_user_agent_id` int(10) NOT NULL,
  `password_change_created` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_password_resets`
--

CREATE TABLE IF NOT EXISTS `fx_user_password_resets` (
  `password_reset_id` bigint(20) unsigned NOT NULL,
  `password_reset_user_id` bigint(20) unsigned NOT NULL,
  `password_reset_code` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_created` int(10) unsigned NOT NULL,
  `password_reset_modified` int(10) unsigned DEFAULT NULL,
  `password_reset_status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_remembers`
--

CREATE TABLE IF NOT EXISTS `fx_user_remembers` (
  `remember_id` bigint(20) unsigned NOT NULL,
  `remember_user_id` bigint(20) unsigned NOT NULL,
  `remember_token` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_user_agent_id` int(10) NOT NULL,
  `remember_created` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_roles`
--

CREATE TABLE IF NOT EXISTS `fx_user_roles` (
  `role_id` int(4) unsigned NOT NULL,
  `role_name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_status` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `role_description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fx_user_roles`
--

INSERT INTO `fx_user_roles` (`role_id`, `role_name`, `role_status`, `role_description`) VALUES
(1, 'Super Administrator', 'S', 'Someone with access to the site network administration features controlling the entire network'),
(2, 'Administrator', 'S', 'Somebody who has access to all the administration features'),
(3, 'Editor', 'S', 'Somebody who can publish and manage posts and pages as well as manage other users` posts, etc'),
(4, 'Author', 'S', 'Somebody who can publish and manage their own posts'),
(5, 'Contributor', 'S', 'Somebody who can write and manage their posts but not publish them'),
(6, 'Subscriber', 'S', 'Somebody who can only manage their profile'),
(7, 'Super Moderator', 'S', 'Someone granted special powers to enforce the rules of all Internet forum'),
(8, 'Moderator', 'S', 'Somebody granted special powers to enforce the rules of an Internet forum'),
(9, 'Member', 'S', 'General Forum Member'),
(10, 'Guests', 'S', 'Guests');

-- --------------------------------------------------------

--
-- Table structure for table `fx_user_success_logins`
--

CREATE TABLE IF NOT EXISTS `fx_user_success_logins` (
  `success_login_id` bigint(20) unsigned NOT NULL,
  `success_login_user_id` bigint(20) unsigned NOT NULL,
  `success_login_ip_address` char(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `success_login_user_agent_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fx_api`
--
ALTER TABLE `fx_api`
  ADD UNIQUE KEY `user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `fx_menus`
--
ALTER TABLE `fx_menus`
  ADD PRIMARY KEY (`menu_id`) USING BTREE;

--
-- Indexes for table `fx_options`
--
ALTER TABLE `fx_options`
  ADD PRIMARY KEY (`option_id`);

--
-- Indexes for table `fx_permissions`
--
ALTER TABLE `fx_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD KEY `profilesId` (`permission_role_id`);

--
-- Indexes for table `fx_usermeta`
--
ALTER TABLE `fx_usermeta`
  ADD PRIMARY KEY (`usermeta_id`),
  ADD UNIQUE KEY `user_id_meta_key` (`usermeta_user_id`,`usermeta_key`) USING BTREE,
  ADD KEY `user_id` (`usermeta_user_id`),
  ADD KEY `meta_key` (`usermeta_key`);

--
-- Indexes for table `fx_users`
--
ALTER TABLE `fx_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD UNIQUE KEY `user_username` (`user_username`) USING BTREE;

--
-- Indexes for table `fx_user_agents`
--
ALTER TABLE `fx_user_agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- Indexes for table `fx_user_email_confirmations`
--
ALTER TABLE `fx_user_email_confirmations`
  ADD PRIMARY KEY (`email_confirmation_id`);

--
-- Indexes for table `fx_user_failed_logins`
--
ALTER TABLE `fx_user_failed_logins`
  ADD PRIMARY KEY (`failed_login_id`),
  ADD KEY `usersId` (`failed_login_user_id`);

--
-- Indexes for table `fx_user_password_changes`
--
ALTER TABLE `fx_user_password_changes`
  ADD PRIMARY KEY (`password_change_id`),
  ADD KEY `usersId` (`password_change_user_id`);

--
-- Indexes for table `fx_user_password_resets`
--
ALTER TABLE `fx_user_password_resets`
  ADD PRIMARY KEY (`password_reset_id`),
  ADD KEY `usersId` (`password_reset_user_id`);

--
-- Indexes for table `fx_user_remembers`
--
ALTER TABLE `fx_user_remembers`
  ADD PRIMARY KEY (`remember_id`),
  ADD KEY `token` (`remember_token`);

--
-- Indexes for table `fx_user_roles`
--
ALTER TABLE `fx_user_roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `fx_user_success_logins`
--
ALTER TABLE `fx_user_success_logins`
  ADD PRIMARY KEY (`success_login_id`),
  ADD KEY `usersId` (`success_login_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fx_menus`
--
ALTER TABLE `fx_menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `fx_options`
--
ALTER TABLE `fx_options`
  MODIFY `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `fx_permissions`
--
ALTER TABLE `fx_permissions`
  MODIFY `permission_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_usermeta`
--
ALTER TABLE `fx_usermeta`
  MODIFY `usermeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_users`
--
ALTER TABLE `fx_users`
  MODIFY `user_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `fx_user_agents`
--
ALTER TABLE `fx_user_agents`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_email_confirmations`
--
ALTER TABLE `fx_user_email_confirmations`
  MODIFY `email_confirmation_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_failed_logins`
--
ALTER TABLE `fx_user_failed_logins`
  MODIFY `failed_login_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_password_changes`
--
ALTER TABLE `fx_user_password_changes`
  MODIFY `password_change_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_password_resets`
--
ALTER TABLE `fx_user_password_resets`
  MODIFY `password_reset_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_remembers`
--
ALTER TABLE `fx_user_remembers`
  MODIFY `remember_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fx_user_roles`
--
ALTER TABLE `fx_user_roles`
  MODIFY `role_id` int(4) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `fx_user_success_logins`
--
ALTER TABLE `fx_user_success_logins`
  MODIFY `success_login_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
