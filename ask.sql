-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2020 at 09:32 AM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `askme_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_invitations`
--

CREATE TABLE `admin_invitations` (
  `id` int(11) NOT NULL,
  `code` varchar(300) NOT NULL DEFAULT '0',
  `posted` varchar(50) NOT NULL DEFAULT '0',
  `status` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `time` int(32) NOT NULL DEFAULT 0,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_views`
--

CREATE TABLE `announcement_views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `announcement_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `apps_sessions`
--

CREATE TABLE `apps_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `session_id` varchar(120) NOT NULL DEFAULT '',
  `platform` varchar(32) NOT NULL DEFAULT '',
  `platform_details` text DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_receipts`
--

CREATE TABLE `bank_receipts` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `description` tinytext DEFAULT NULL,
  `price` varchar(50) NOT NULL DEFAULT '0',
  `mode` varchar(50) NOT NULL DEFAULT '',
  `track_id` varchar(50) CHARACTER SET utf8mb4 DEFAULT '',
  `approved` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `receipt_file` varchar(250) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `promote_charge_amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE `banned` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `time` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(4000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `name`, `value`) VALUES
(1, 'theme', 'default'),
(2, 'censored_words', ''),
(3, 'title', 'AskMe'),
(4, 'name', 'AskMe'),
(5, 'keyword', 'askme'),
(6, 'email', 'deendoughouz@gmail.com'),
(7, 'description', 'askme description'),
(8, 'validation', 'on'),
(9, 'recaptcha', 'on'),
(10, 'recaptcha_key', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'),
(11, 'language', 'english'),
(21, 'smtp_or_mail', 'smtp'),
(22, 'smtp_host', ''),
(23, 'smtp_username', ''),
(24, 'smtp_password', ''),
(25, 'smtp_encryption', 'ssl'),
(26, 'smtp_port', '465'),
(27, 'delete_account', 'on'),
(36, 'last_admin_collection', '1587480435'),
(37, 'user_statics', '[{&quot;month&quot;:&quot;January&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;February&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;March&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;April&quot;,&quot;new_users&quot;:1},{&quot;month&quot;:&quot;May&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;June&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;July&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;August&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;September&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;October&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;November&quot;,&quot;new_users&quot;:0},{&quot;month&quot;:&quot;December&quot;,&quot;new_users&quot;:0}]'),
(38, 'questions_statics', '[{&quot;month&quot;:&quot;January&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;February&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;March&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;April&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;May&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;June&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;July&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;August&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;September&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;October&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;November&quot;,&quot;new_questions&quot;:0},{&quot;month&quot;:&quot;December&quot;,&quot;new_questions&quot;:0}]'),
(45, 'user_registration', 'on'),
(58, 'google', ''),
(59, 'last_created_sitemap', '10-01-2019'),
(60, 'is_ok', '1'),
(64, 'paypal_id', ''),
(65, 'paypal_secret', ''),
(66, 'paypal_mode', 'sandbox'),
(67, 'last_backup', '09-01-2019'),
(68, 'user_ads', 'on'),
(71, 's3_upload', 'off'),
(72, 's3_bucket_name', ''),
(73, 'amazone_s3_key', ''),
(74, 'amazone_s3_s_key', ''),
(75, 'region', 'us-east-1'),
(87, 'night_mode', 'light'),
(92, 'ftp_username', ''),
(98, 'ftp_host', 'localhost'),
(99, 'ftp_port', '21'),
(101, 'ftp_password', ''),
(102, 'ftp_upload', 'off'),
(103, 'ftp_endpoint', 'storage.askme.com'),
(104, 'ftp_path', './'),
(137, 'server_key', '4354435354'),
(145, 'max_image_upload_size', '1000'),
(146, 'google_place_api', 'AIzaSyB7rRpQJyQJZYzxrvStRGFkbB0MxXWGrO0'),
(147, 'facebook_url', 'https://www.facebook.com'),
(148, 'twitter_url', 'https://www.twitter.com'),
(149, 'google_url', 'https://www.google.com'),
(150, 'profile_visit_notification', 'on'),
(152, 'ad_c_price', '0.05'),
(153, 'ad_v_price', '0.01'),
(154, 'ads_currency', 'USD'),
(156, 'google_map_api', 'AIzaSyBOfpaMO_tMMsuvS2T4zx4llbtsFqMuT9Y'),
(157, 'last_update', '1538060393'),
(158, 'total_questions', '0'),
(159, 'total_answers', '0'),
(160, 'total_anon_questions', '0'),
(161, 'total_non_anon_questions', '0'),
(162, 'total_shares', '0'),
(163, 'total_replys', '0'),
(164, 'total_active_users', '1'),
(165, 'total_unactive_users', '0'),
(166, 'auto_friend_users', 'qbizns'),
(168, 'fb_login', 'on'),
(169, 'plus_login', 'on'),
(170, 'tw_login', 'on'),
(174, 'promote_question_cost', '1.23'),
(175, 'max_post_per_hour', '20'),
(176, 'max_user_reg_hour', '2'),
(177, 'post_text_limit', '400'),
(178, 'nearby_question_distance', '10'),
(179, 'last_promote_question_update', '1607934675'),
(180, 'version', '1.2'),
(181, 'server', 'ajax'),
(182, 'server', 'nodejs'),
(183, 'video_upload', 'on'),
(184, 'currency_symbol_array', 'a:10:{s:3:\"USD\";s:1:\"$\";s:3:\"EUR\";s:3:\"€\";s:3:\"JPY\";s:2:\"¥\";s:3:\"TRY\";s:3:\"₺\";s:3:\"GBP\";s:2:\"£\";s:3:\"RUB\";s:6:\"руб\";s:3:\"PLN\";s:3:\"zł\";s:3:\"ILS\";s:3:\"₪\";s:3:\"BRL\";s:2:\"R$\";s:3:\"INR\";s:3:\"₹\";}'),
(185, 'currency_array', 'a:10:{i:0;s:3:\"USD\";i:1;s:3:\"EUR\";i:2;s:3:\"JPY\";i:3;s:3:\"TRY\";i:4;s:3:\"GBP\";i:5;s:3:\"RUB\";i:6;s:3:\"PLN\";i:7;s:3:\"ILS\";i:8;s:3:\"BRL\";i:9;s:3:\"INR\";}'),
(186, 'paypal_currency', 'Usd'),
(187, 'checkout_currency', ''),
(188, 'checkout_payment', 'no'),
(189, 'checkout_mode', 'sandbox'),
(190, 'checkout_seller_id', ''),
(191, 'checkout_publishable_key', ''),
(192, 'credit_card', 'no'),
(193, 'stripe_currency', ''),
(194, 'bank_payment', 'yes'),
(195, 'bank_transfer_note', 'In order to confirm the bank transfer, you will need to upload a receipt or take a screenshot of your transfer within 1 day from your payment date. If a bank transfer is made but no receipt is uploaded within this period, your order will be cancelled. We will verify and confirm your receipt within 3 working days from the date you upload it.'),
(196, 'server', 'ajax'),
(197, 'verification_badge', 'on'),
(198, 'login_auth', '0'),
(199, 'two_factor', '1'),
(200, 'two_factor_type', 'email'),
(201, 'sms_phone_number', ''),
(202, 'sms_twilio_password', ''),
(203, 'sms_twilio_username', ''),
(204, 'sms_t_phone_number', ''),
(205, 'invite_links_system', '0'),
(206, 'user_links_limit', ''),
(207, 'expire_user_links', ''),
(208, 'stripe_payment', 'on'),
(209, 'stripe_version', ''),
(210, 'stripe_secret', ''),
(211, 'spaces_key', ''),
(212, 'spaces_secret', ''),
(213, 'space_name', ''),
(214, 'space_region', ''),
(215, 'spaces', 'off'),
(216, 'cloud_upload', 'off'),
(217, 'cloud_file_path', ''),
(218, 'cloud_bucket_name', ''),
(219, 'max_upload_all_users', '0'),
(220, 'fileSharing', 'on'),
(221, 'audio_upload', 'on'),
(222, 'mime_types', 'text/plain,video/mp4,video/mov,video/mpeg,video/flv,video/avi,video/webm,audio/wav,audio/mpeg,video/quicktime,audio/mp3,image/png,image/jpeg,image/gif,application/pdf,application/msword,application/zip,application/x-rar-compressed,text/pdf,application/x-pointplus,text/css'),
(223, 'allowedExtenstion', 'jpg,png,jpeg,gif,mkv,docx,zip,rar,pdf,doc,mp3,mp4,flv,wav,txt,mov,avi,webm,wav,mpeg,mp4'),
(224, 'wowonder_domain_uri', ''),
(225, 'vkontakte_domain_uri', ''),
(226, 'wowonder_img', ''),
(227, 'vkonktake_domain_uri', ''),
(228, 'vkontakte_app_key', ''),
(229, 'vkontakte_app_ID', ''),
(230, 'vkontakte_login', 'off'),
(231, 'wowonder_login', 'on'),
(232, 'wowonder_app_key', ''),
(233, 'wowonder_app_ID', ''),
(234, 'twitter_app_key', ''),
(235, 'twitter_app_ID', ''),
(236, 'bank_description', '<div class=\"bank_info\"><div class=\"dt_settings_header bg_gradient\"><div class=\"dt_settings_circle-1\"></div><div class=\"dt_settings_circle-2\"></div><div class=\"bank_info_innr\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 24 24\"><path fill=\"currentColor\" d=\"M11.5,1L2,6V8H21V6M16,10V17H19V10M2,22H21V19H2M10,10V17H13V10M4,10V17H7V10H4Z\"></path></svg><h4 class=\"bank_name\">Garanti Bank</h4><div class=\"row\"><div class=\"col col-md-12\"><div class=\"bank_account\"><p>4796824372433055</p><span class=\"help-block\">Account number / IBAN</span></div></div><div class=\"col col-md-12\"><div class=\"bank_account_holder\"><p>Antoian Kordiyal</p><span class=\"help-block\">Account name</span></div></div><div class=\"col col-md-6\"><div class=\"bank_account_code\"><p>TGBATRISXXX</p><span class=\"help-block\">Routing code</span></div></div><div class=\"col col-md-6\"><div class=\"bank_account_country\"><p>United States</p><span class=\"help-block\">Country</span></div></div></div></div></div></div>'),
(237, 'facebook_app_ID', ''),
(238, 'facebook_app_key', ''),
(239, 'google_app_ID', ''),
(240, 'google_app_key', '');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL DEFAULT 0,
  `user_two` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `custom_pages`
--

CREATE TABLE `custom_pages` (
  `id` int(11) NOT NULL,
  `page_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_title` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_content` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_type` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `follower_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
  `id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `last_trend_time` int(11) NOT NULL DEFAULT 0,
  `trend_use_num` int(11) NOT NULL DEFAULT 0,
  `expire` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `invitation_links`
--

CREATE TABLE `invitation_links` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `invited_id` int(11) NOT NULL DEFAULT 0,
  `code` varchar(300) NOT NULL DEFAULT '',
  `time` int(50) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `keyword_search`
--

CREATE TABLE `keyword_search` (
  `id` int(11) UNSIGNED NOT NULL,
  `keyword` varchar(255) DEFAULT '',
  `hits` int(11) UNSIGNED DEFAULT 0,
  `time` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `langs`
--

CREATE TABLE `langs` (
  `id` int(11) NOT NULL,
  `lang_key` varchar(160) DEFAULT NULL,
  `english` text DEFAULT NULL,
  `arabic` text DEFAULT NULL,
  `dutch` text DEFAULT NULL,
  `french` text DEFAULT NULL,
  `german` text DEFAULT NULL,
  `russian` text DEFAULT NULL,
  `spanish` text DEFAULT NULL,
  `turkish` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `langs`
--

INSERT INTO `langs` (`id`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(1, 'copyright', 'Copyright © {1} {0}. All rights reserved.', 'حقوق الطبع والنشر © {1} {0}. كل الحقوق محفوظة.', 'Copyright © {1} {0}. Alle rechten voorbehouden.', 'Copyright © {1} {0}. Tous les droits sont réservés.', 'Copyright © {1} {0}. Alle Rechte vorbehalten.', 'авторское право © {1} {0}. Все права защищены.', 'Copyright © {1} {0}. Todos los derechos reservados.', 'Telif hakkı © {1} {0}. Tüm hakları Saklıdır.'),
(2, 'about_us', 'About Us', 'معلومات عنا', 'Over ons', 'À propos de nous', 'Über uns', 'Насчет нас', 'Sobre nosotros', 'Hakkımızda'),
(3, 'terms', 'Terms', 'شروط', 'Voorwaarden', 'termes', 'Bedingungen', 'термины', 'Condiciones', 'şartlar'),
(4, 'contact', 'Contact', 'اتصل', 'Contact', 'Contact', 'Kontakt', 'контакт', 'Contacto', 'Temas'),
(5, 'agency', 'Agency', 'وكالة', 'agentschap', 'Agence', 'Agentur', 'Агентство', 'Agencia', 'Ajans'),
(6, 'startup', 'Start-Up', 'بدء', 'Opstarten', 'Commencez', 'Anlaufen', 'Запускать', 'Puesta en marcha', 'Başlamak'),
(7, 'business', 'Business', 'اعمال', 'Bedrijf', 'Affaires', 'Geschäft', 'Бизнес', 'Negocio', 'iş'),
(8, 'available_in', '{0} available in', '{0} متاح في', '{0} beschikbaar in', '{0} disponible dans', '{0} verfügbar in', '{0} доступно в', '{0} disponible en', '{0} mevcut'),
(9, 'curious', 'Curious?', 'فضولي؟', 'Nieuwsgierig?', 'Curieuse?', 'Neugierig?', 'Любопытно?', '¿Curioso?', 'Meraklı?'),
(10, 'just_ask', 'Just ask!', 'فقط إسأل!', 'Gewoon vragen!', 'Il suffit de demander!', 'Einfach fragen!', 'Просто спроси!', '¡Solo pregunta!', 'Sadece sor!'),
(11, 'openly_or_anonymously', 'Openly or anonymously', 'بصراحة أو مجهول', 'Openlijk of anoniem', 'Ouvertement ou anonymement', 'Offen oder anonym', 'Открыто или анонимно', 'Abiertamente o anónimamente', 'Açık veya anonim olarak'),
(12, 'get_started', 'Get Started', 'البدء', 'Begin', 'Commencer', 'Loslegen', 'Начать', 'Empezar', 'Başlamak'),
(13, 'login', 'Login', 'تسجيل دخول', 'Log in', 's\'identifier', 'Anmeldung', 'авторизоваться', 'iniciar sesión', 'oturum aç'),
(14, 'home', 'Home', 'الصفحة الرئيسية', 'Huis', 'Accueil', 'Zuhause', 'Главная', 'Casa', 'Ev'),
(15, 'register', 'Register', 'تسجيل', 'Registreren', 'registre', 'Registrieren', 'регистр', 'Registro', 'Kayıt olmak'),
(16, 'sign_up_reg', 'Sign Up!', 'سجل!', 'Inschrijven!', 'S\'inscrire!', 'Anmelden!', 'Подписаться!', '¡Regístrate!', 'Kaydol!'),
(17, 'fill_out_form', 'Fill out the form to get started.', 'املأ النموذج للبدء.', 'Vul het formulier in om te beginnen.', 'Remplissez le formulaire pour commencer.', 'Füllen Sie das Formular aus, um zu beginnen.', 'Заполните форму, чтобы начать.', 'Rellena el formulario para empezar.', 'Başlamak için formu doldurun.'),
(18, 'username', 'Username', 'اسم المستخدم', 'Gebruikersnaam', 'Nom d\'utilisateur', 'Nutzername', 'имя пользователя', 'Nombre de usuario', 'Kullanıcı adı'),
(19, 'email_id', 'Email ID', 'عنوان الايميل', 'E-mail identiteit', 'Identifiant Email', 'E-Mail', 'Эл. адрес', 'Identificación de correo', 'Email kimliği'),
(20, 'password', 'Password', 'كلمه السر', 'Wachtwoord', 'Mot de passe', 'Passwort', 'пароль', 'Contraseña', 'Parola'),
(21, 'confirm_password', 'Confirm Password', 'تأكيد كلمة المرور', 'bevestig wachtwoord', 'Confirmez le mot de passe', 'Passwort bestätigen', 'Подтвердите Пароль', 'Confirmar contraseña', 'Şifreyi Onayla'),
(22, 'terms_and_conditions', 'Terms and Conditions', 'الأحكام والشروط', 'Voorwaarden', 'Termes et conditions', 'Geschäftsbedingungen', 'Условия и положения', 'Términos y Condiciones', 'Şartlar ve koşullar'),
(23, 'already_have_an_account', 'Already have an account?', 'هل لديك حساب؟', 'Heb je al een account?', 'Vous avez déjà un compte?', 'Hast du schon ein Konto?', 'Уже есть аккаунт?', '¿Ya tienes una cuenta?', 'Zaten hesabınız var mı?'),
(24, 'i_agree_to_the', 'I agree to the', 'أنا أوافق على', 'Ik ga akkoord met de', 'je suis d\'accord avec le', 'Ich stimme dem zu', 'я согласен', 'Estoy de acuerdo con la', 'Katılıyorum'),
(25, 'cookie_message', 'This website uses cookies to ensure you get the best experience on our website.', 'يستخدم موقع الويب هذا ملفات تعريف الارتباط لضمان حصولك على أفضل تجربة على موقعنا.', 'Deze website maakt gebruik van cookies om ervoor te zorgen dat u de beste ervaring op onze website krijgt.', 'Ce site utilise des cookies pour vous garantir la meilleure expérience sur notre site.', 'Diese Website verwendet Cookies, um sicherzustellen, dass Sie das beste Erlebnis auf unserer Website erhalten.', 'Этот веб-сайт использует куки-файлы, чтобы обеспечить вам максимальную отдачу от нашего веб-сайта.', 'Este sitio web utiliza cookies para garantizar que obtenga la mejor experiencia en nuestro sitio web.', 'Bu web sitesi, web sitemizde en iyi deneyimi yaşamanızı sağlamak için çerezleri kullanır.'),
(26, 'cookie_dismiss', 'Got It!', 'فهمتك!', 'Begrepen!', 'Je l\'ai!', 'Ich habs!', 'Понял!', '¡Lo tengo!', 'Anladım!'),
(27, 'cookie_link', 'Learn More', 'أعرف أكثر', 'Kom meer te weten', 'Apprendre encore plus', 'Lern mehr', 'Учить больше', 'Aprende más', 'Daha fazla bilgi edin'),
(28, 'about', 'About', 'حول', 'Wat betreft', 'Sur', 'Über', 'Около', 'Acerca de', 'hakkında'),
(29, 'services', 'Services', 'خدمات', 'Diensten', 'Prestations de service', 'Dienstleistungen', 'Сервисы', 'Servicios', 'Hizmetler'),
(30, 'more', 'More', 'أكثر من', 'Meer', 'Plus', 'Mehr', 'Больше', 'Más', 'Daha'),
(31, 'welcome_pack', 'Welcome Back!', 'مرحبا بعودتك!', 'Welkom terug!', 'Nous saluons le retour!', 'Willkommen zurück!', 'Добро пожаловать обратно!', '¡Dar una buena acogida!', 'Tekrar hoşgeldiniz!'),
(32, 'login_to_manage_our_account', 'Login to manage your account.', 'تسجيل الدخول لإدارة حسابك.', 'Meld u aan om uw account te beheren.', 'Connectez-vous pour gérer votre compte.', 'Melden Sie sich an, um Ihr Konto zu verwalten.', 'Войдите, чтобы управлять своей учетной записью.', 'Inicie sesión para gestionar su cuenta.', 'Hesabınızı yönetmek için giriş yapın.'),
(33, 'forget_your_password', 'Forgot your password ?', 'نسيت رقمك السري ؟', 'Uw wachtwoord vergeten ?', 'Mot de passe oublié ?', 'Haben Sie Ihr Passwort vergessen ?', 'Забыли пароль ?', 'Olvidaste tu contraseña ?', 'Parolanızı mı unuttunuz ?'),
(35, 'dont_have_account', 'Do not have an account?', 'لا تملك حساب؟', 'Heb je geen account?', 'Vous n\'avez pas de compte?', 'Haben Sie kein Konto?', 'Еще не регистрировались?', '¿No tiene una cuenta?', 'Bir hesabınız yok mu?'),
(36, 'or', 'OR', 'أو', 'OF', 'OU', 'ODER', 'ИЛИ ЖЕ', 'O', 'VEYA'),
(37, 'facebook', 'Facebook', 'فيس بوك', 'Facebook', 'Facebook', 'Facebook', 'facebook', 'Facebook', 'Facebook'),
(38, 'google', 'Google', 'جوجل', 'Google', 'Google', 'Google', 'Google', 'Google', 'Google'),
(39, 'twitter', 'Twitter', 'تغريد', 'tjilpen', 'Gazouillement', 'Twitter', 'щебет', 'Gorjeo', 'heyecan'),
(40, 'enter_your_email_address', 'Enter your email address below.', 'أدخل عنوان البريد الالكتروني أدناه.', 'Voer hieronder uw e-mailadres in.', 'Entrez votre adresse email ci-dessous.', 'Geben Sie unten Ihre E-Mail-Adresse ein.', 'Введите адрес вашей электронной почты ниже.', 'Ingrese su dirección de correo electrónico a continuación.', 'E-posta adresinizi aşağıya girin.'),
(42, 'request_reset_link', 'Request Reset Link', 'طلب إعادة تعيين الرابط', 'Verzoek Reset Link', 'Demander un lien de réinitialisation', 'Reset-Link anfordern', 'Запросить сброс ссылки', 'Solicitar Restablecer Enlace', 'Sıfırlama Bağlantısı İste'),
(43, 'back_to_login', 'Back to Login', 'العودة إلى تسجيل الدخول', 'Terug naar Inloggen', 'Retour connexion', 'Zurück zur Anmeldung', 'Вернуться на страницу входа', 'Atrás para iniciar sesión', 'Girişe Geri Dön'),
(44, 'reset_password', 'Reset password', 'إعادة تعيين كلمة المرور', 'Wachtwoord opnieuw instellen', 'Réinitialiser le mot de passe', 'Passwort zurücksetzen', 'Сброс пароля', 'Restablecer la contraseña', 'Şifreyi yenile'),
(45, 'privacy_policy', 'Privacy Policy', 'سياسة خاصة', 'Privacybeleid', 'Politique de confidentialité', 'Datenschutz-Bestimmungen', 'политика конфиденциальности', 'Política de privacidad', 'Gizlilik Politikası'),
(46, 'terms_of_use', 'Terms of Use', 'تعليمات الاستخدام', 'Gebruiksvoorwaarden', 'Conditions d\'utilisation', 'Nutzungsbedingungen', 'Условия эксплуатации', 'Términos de Uso', 'Kullanım Şartları'),
(47, 'contact_us', 'Contact Us', 'اتصل بنا', 'Neem contact met ons op', 'Contactez nous', 'Kontaktiere uns', 'Связаться с нами', 'Contáctenos', 'Bizimle iletişime geçin'),
(48, 'first_name', 'First name', 'الاسم الاول', 'Voornaam', 'Prénom', 'Vorname', 'Имя', 'Nombre de pila', 'İsim'),
(49, 'last_name', 'Last name', 'الكنية', 'Achternaam', 'Nom de famille', 'Nachname', 'Фамилия', 'Apellido', 'Soyadı'),
(50, 'send', 'Send', 'إرسال', 'Sturen', 'Envoyer', 'Senden', 'послать', 'Enviar', 'göndermek'),
(51, 'how_can_we_help', 'How can we help?', 'كيف يمكن أن نساعد؟', 'Hoe kunnen we helpen?', 'Comment pouvons nous aider?', 'Wie können wir helfen?', 'Как мы можем помочь?', '¿Cómo podemos ayudar?', 'Nasıl yardımcı olabiliriz?'),
(52, 'please_check_details', 'Please check the details', 'يرجى التحقق من التفاصيل', 'Controleer de details', 'S\'il vous plaît vérifier les détails', 'Bitte überprüfen Sie die Details', 'Пожалуйста, проверьте детали', 'Por favor revise los detalles', 'Lütfen detayları kontrol et'),
(53, 'email_invalid_characters', 'E-mail is invalid', 'البريد الإلكتروني غير صالح', 'Email is ongeldig', 'Le courriel est invalide', 'E-Mail ist ungültig', 'E-mail неверен', 'El correo electrónico es invalido', 'E-posta geçersiz'),
(54, 'email_sent', 'E-mail sent successfully! Please check your inbox/spam.', 'تم إرسال البريد الإلكتروني بنجاح! يرجى التحقق من البريد الوارد / البريد المزعج.', 'E-mail succesvol verzonden! Controleer uw inbox / spam.', 'E-mail envoyé avec succès! Veuillez vérifier votre boîte de réception / spam.', 'Email wurde erfolgreich Versendet! Bitte überprüfen Sie Ihren Posteingang / Spam.', 'Письмо успешно отправлено! Пожалуйста, проверьте ваш почтовый ящик / спам.', 'Correo electrónico enviado con éxito! Por favor revise su bandeja de entrada / spam.', 'E-posta başarıyla gönderildi! Lütfen gelen kutunuzu / spam adresinizi kontrol edin.'),
(55, 'error_msg', 'Something went wrong Please try again later!', 'هناك شئ خاطئ، يرجى المحاولة فى وقت لاحق!', 'Er is iets misgegaan Probeer het later opnieuw!', 'Quelque chose c\'est mal passé. Merci d\'essayer plus tard!', 'Etwas ist schief gelaufen. Bitte versuchen Sie es später noch einmal!', 'Что-то пошло не так. Пожалуйста, повторите попытку позже!', 'Algo salió mal Por favor, intente de nuevo más tarde!', 'Bir şeyler yanlış oldu. Lütfen sonra tekrar deneyiniz!'),
(57, 'email_not_exist', 'E-mail not exist', 'البريد الإلكتروني غير موجود', 'E-mail bestaat niet', 'E-mail n\'existe pas', 'E-Mail existiert nicht', 'Электронная почта не существует', 'E-mail no existe', 'E-posta mevcut değil'),
(59, 'account_is_not_active', 'Your account is not active yet, didn\'t get the email?', 'حسابك غير نشط حتى الآن ، لم أحصل على البريد الإلكتروني؟', 'Uw account is nog niet actief, heeft de e-mail niet ontvangen?', 'Votre compte n\'est pas encore actif, vous n\'avez pas reçu l\'e-mail?', 'Ihr Konto ist noch nicht aktiv, Sie haben die E-Mail nicht erhalten?', 'Ваша учетная запись еще не активна, не получили письмо?', 'Su cuenta aún no está activa, ¿no recibió el correo electrónico?', 'Hesabınız henüz aktif değil, e-postayı alamadınız mı?'),
(60, 'resend_email', 'Resend E-mail', 'إعادة إرسال البريد الإلكتروني', 'Email opnieuw verzenden', 'Ré-envoyer l\'email', 'E-Mail zurücksenden', 'Переслать E-mail', 'Reenviar email', 'Emaili yeniden gönder'),
(61, 'invalid_username_or_password', 'Invalid username or password', 'خطأ في اسم المستخدم أو كلمة مرور', 'ongeldige gebruikersnaam of wachtwoord', 'Nom d\'utilisateur ou mot de passe invalide', 'ungültiger Benutzername oder Passwort', 'неправильное имя пользователя или пароль', 'usuario o contraseña invalido', 'Geçersiz kullanıcı adı veya şifre'),
(62, 'username_is_taken', 'Username is taken', 'اسم المستخدم ماخوذ', 'Gebruikersnaam is in gebruik', 'Le nom d\'utilisateur est pris', 'Benutzername ist vergeben', 'Имя пользователя занято', 'Nombre de usuario es tomado', 'Kullanıcı adı alınmış'),
(63, 'username_characters_length', 'Username must be between 5 / 32', 'يجب أن يكون اسم المستخدم بين 5/32', 'Gebruikersnaam moet tussen 5/32 zijn', 'Le nom d\'utilisateur doit être compris entre 5/32', 'Der Benutzername muss zwischen 5 und 32 liegen', 'Имя пользователя должно быть между 5/32', 'El nombre de usuario debe estar entre 5/32', 'Kullanıcı adı 5/32 arasında olmalıdır'),
(64, 'username_invalid_characters', 'Invalid username characters', 'أحرف اسم المستخدم غير صالحة', 'Ongeldige gebruikersnaamtekens', 'Nom d\'utilisateur invalide', 'Ungültige Zeichen für den Benutzernamen', 'Неверные символы имени пользователя', 'Caracteres de usuario inválidos', 'Geçersiz kullanıcı adı karakterleri'),
(66, 'password_not_match', 'Password not match', 'كلمة السر ليست جيدة', 'Wachtwoord komt niet overeen', 'Le mot de passe ne correspond pas', 'Passwort stimmt nicht überein', 'Пароль не совпадает', 'La contraseña no coincide', 'Şifre eşleşmiyor'),
(67, 'password_is_short', 'Password is too short', 'كلمة المرور قصيرة جدا', 'Wachtwoord is te kort', 'Le mot de passe est trop court', 'Das Passwort ist zu kurz', 'Пароль слишком короткий', 'La contraseña es demasiado corta', 'Şifre çok kısa'),
(68, 'reCaptcha_error', 'Please Check the re-captcha.', 'يرجى التحقق من إعادة captcha.', 'Controleer de re-captcha.', 'S\'il vous plaît vérifier le re-captcha.', 'Bitte überprüfen Sie das Captcha erneut.', 'Пожалуйста, проверьте повторно код проверки.', 'Por favor revise el re-captcha.', 'Lütfen re-captcha\'yı kontrol edin.'),
(69, 'terms_accept', 'Please agree to the Terms of use & Privacy Policy', 'يرجى الموافقة على شروط الاستخدام وسياسة الخصوصية', 'Ga akkoord met de gebruiksvoorwaarden en het privacybeleid', 'Veuillez accepter les conditions d\'utilisation et la politique de confidentialité', 'Bitte stimmen Sie den Nutzungsbedingungen und Datenschutzbestimmungen zu', 'Пожалуйста, согласитесь с условиями использования и политикой конфиденциальности', 'Por favor acepte los Términos de uso y la Política de privacidad.', 'Lütfen kullanım şartlarını ve gizlilik politikasını kabul edin.'),
(70, 'successfully_joined_desc', 'Registration successful! We have sent you an email, Please check your inbox/spam to verify your account.', 'التسجيل ناجح! لقد أرسلنا إليك بريدًا إلكترونيًا ، يرجى التحقق من البريد الوارد / الرسائل غير المرغوب فيها للتحقق من حسابك.', 'Registratie gelukt! We hebben je een e-mail gestuurd. Controleer je inbox / spam om je account te verifiëren.', 'Inscription réussi! Nous vous avons envoyé un courrier électronique. Veuillez vérifier votre boîte de réception / spam pour vérifier votre compte.', 'Registrierung erfolgreich! Wir haben Ihnen eine E-Mail gesendet. Bitte überprüfen Sie Ihren Posteingang / Spam, um Ihr Konto zu bestätigen.', 'Регистрация прошла успешно! Мы отправили вам письмо, пожалуйста, проверьте свой почтовый ящик / спам, чтобы подтвердить свой аккаунт.', '¡Registro exitoso! Le hemos enviado un correo electrónico. Verifique su bandeja de entrada / correo no deseado para verificar su cuenta.', 'Kayıt başarılı! Size bir e-posta gönderdik, hesabınızı doğrulamak için lütfen gelen kutunuzu / spam’nizi kontrol edin.'),
(71, 'please_wait', 'Please wait..', 'ارجوك انتظر..', 'Even geduld aub..', 'S\'il vous plaît, attendez..', 'Warten Sie mal..', 'Пожалуйста, подождите..', 'Por favor espera..', 'Lütfen bekle..'),
(72, 'log_out', 'Log Out', 'الخروج', 'Uitloggen', 'Connectez - Out', 'Ausloggen', 'Выйти', 'Cerrar sesión', 'Çıkış Yap'),
(73, 'settings', 'Settings', 'الإعدادات', 'instellingen', 'Réglages', 'die Einstellungen', 'настройки', 'Ajustes', 'Ayarlar'),
(74, 'email_exists', 'This e-mail is already in use', 'هذا البريد استخدم من قبل', 'Deze email is al in gebruik', 'Cet e-mail est déjà utilisée', 'Diese E-Mail-Adresse wird schon verwendet', 'Этот электронный адрес уже используется', 'Este correo electrónico ya está en uso', 'Bu e-posta zaten kullanılıyor'),
(76, 'you_already_loggedin', 'You already logged in', 'لقد قمت بتسجيل الدخول بالفعل', 'Je bent al ingelogd', 'Vous êtes déjà connecté', 'Sie haben sich bereits angemeldet', 'Вы уже вошли в систему', 'Ya has iniciado sesión', 'Zaten giriş yaptınız'),
(77, 'successfully_resend_desc', 'Confirmation email sent successful! Please check your inbox/spam to verify your account.', 'تم إرسال رسالة التأكيد بنجاح! يرجى التحقق من بريدك الوارد / البريد المزعج للتحقق من حسابك.', 'Bevestigingsmail succesvol verzonden! Controleer uw inbox / spam om uw account te verifiëren.', 'Email de confirmation envoyé avec succès! Veuillez vérifier votre boîte de réception / spam pour vérifier votre compte.', 'Bestätigungsemail erfolgreich versendet! Bitte überprüfen Sie Ihren Posteingang / Spam, um Ihr Konto zu bestätigen.', 'Письмо с подтверждением отправлено успешно! Пожалуйста, проверьте свой почтовый ящик / спам, чтобы подтвердить свой аккаунт.', 'Correo electrónico de confirmación enviado correctamente! Por favor revise su bandeja de entrada / spam para verificar su cuenta.', 'Onay e-postası başarıyla gönderildi! Lütfen hesabınızı doğrulamak için gelen kutunuzu / spam’ınızı kontrol edin.'),
(78, 'error_while_send_confirmation_email', 'An error occurred while sending the confirmation email', 'حدث خطأ أثناء إرسال رسالة التأكيد الإلكترونية', 'Er is een fout opgetreden tijdens het verzenden van de bevestigingsmail', 'Une erreur s\'est produite lors de l\'envoi du courrier électronique de confirmation', 'Beim Senden der Bestätigungs-E-Mail ist ein Fehler aufgetreten', 'При отправке письма с подтверждением произошла ошибка', 'Se produjo un error al enviar el correo electrónico de confirmación.', 'Onay e-postası gönderilirken bir hata oluştu'),
(79, 'invalid_request', 'Invalid request', 'طلب غير صالح', 'Ongeldig verzoek', 'Requête invalide', 'Ungültige Anfrage', 'Неверный запрос', 'Solicitud no válida', 'Geçersiz istek'),
(80, 'password_reset', 'Password Reset', 'إعادة ضبط كلمة المرور', 'Wachtwoord reset', 'Réinitialiser le mot de passe', 'Passwort zurücksetzen', 'Восстановление пароля', 'Restablecimiento de contraseña', 'Parola sıfırlama'),
(81, 'enter_new_password', 'Enter new password to proceed.', 'أدخل كلمة المرور الجديدة للمتابعة.', 'Voer een nieuw wachtwoord in om door te gaan.', 'Entrez un nouveau mot de passe pour continuer.', 'Geben Sie ein neues Passwort ein, um fortzufahren.', 'Введите новый пароль, чтобы продолжить.', 'Introduzca la nueva contraseña para continuar.', 'Devam etmek için yeni şifre girin.'),
(82, 'new_password', 'New password', 'كلمة السر الجديدة', 'Nieuw paswoord', 'Nouveau mot de passe', 'Neues Kennwort', 'Новый пароль', 'Nueva contraseña', 'Yeni Şifre'),
(83, 'confirm_new_password', 'Confirm new password', 'تأكيد كلمة المرور الجديدة', 'Bevestig nieuw wachtwoord', 'Confirmer le nouveau mot de passe', 'Bestätige neues Passwort', 'Подтвердите новый пароль', 'Confirmar nueva contraseña', 'Yeni şifreyi onayla'),
(84, 'reset', 'Reset', 'إعادة تعيين', 'Reset', 'Réinitialiser', 'Zurücksetzen', 'Сброс', 'Reiniciar', 'Reset'),
(85, 'change_password', 'Change Password', 'غير كلمة السر', 'Wachtwoord wijzigen', 'Changer le mot de passe', 'Ändere das Passwort', 'Изменить пароль', 'Cambia la contraseña', 'Şifre değiştir'),
(86, 'email_code_not_found', 'You click on the invalid link, try again later.', 'انقر على الرابط غير الصالح ، أعد المحاولة لاحقًا.', 'U klikt op de ongeldige link en probeert het later opnieuw.', 'Vous cliquez sur le lien non valide, réessayez plus tard.', 'Sie klicken auf den ungültigen Link und versuchen es später erneut.', 'Вы нажимаете на недействительную ссылку, попробуйте позже.', 'Haz clic en el enlace inválido, inténtalo de nuevo más tarde.', 'Geçersiz bağlantıya tıklayın, daha sonra tekrar deneyin.'),
(87, 'no_user_found_with_this_data', 'There is no user found with this data, please try again.', 'لا يوجد مستخدم لديه هذه البيانات ، يرجى المحاولة مرة أخرى.', 'Er is geen gebruiker gevonden met deze gegevens. Probeer het opnieuw.', 'Aucun utilisateur n\'a été trouvé avec ces données. Veuillez réessayer.', 'Es wurde kein Benutzer mit diesen Daten gefunden. Bitte versuchen Sie es erneut.', 'Пользователь с этими данными не найден, пожалуйста, попробуйте еще раз.', 'No se ha encontrado ningún usuario con estos datos, inténtalo de nuevo.', 'Bu verilerle hiçbir kullanıcı bulunamadı, lütfen tekrar deneyin.'),
(88, 'avatar', 'Avatar', 'الصورة الرمزية', 'avatar', 'Avatar', 'Benutzerbild', 'Аватар', 'Avatar', 'Avatar'),
(89, 'info', 'Info', 'معلومات', 'info', 'Info', 'Info', 'Информация', 'Información', 'Bilgi'),
(90, 'add_profile_picture', 'Add Profile Picture', 'إضافة صورة الملف الشخصي', 'Profielfoto toevoegen', 'Ajouter une photo de profil', 'Profilbild hinzufügen', 'Добавить аватарку', 'Añadir foto de perfil', 'Profil Resmi Ekle'),
(91, 'show_unique_personality', 'Show your unique personality and style.', 'أظهر شخصيتك الفريدة وأسلوبك.', 'Toon uw unieke persoonlijkheid en stijl.', 'Montrez votre personnalité et votre style uniques.', 'Zeigen Sie Ihre einzigartige Persönlichkeit und Ihren Stil.', 'Покажите свою уникальную индивидуальность и стиль.', 'Muestra tu personalidad y estilo únicos.', 'Eşsiz kişiliğinizi ve tarzınızı gösterin.'),
(92, 'next', 'Next', 'التالى', 'volgende', 'Suivant', 'Nächster', 'следующий', 'Siguiente', 'Sonraki'),
(94, 'about_you', 'About You', 'حولك', 'Over jou', 'Au propos de vous', 'Über dich', 'О вас', 'Acerca de ti', 'Senin hakkında'),
(95, 'share_your_information', 'Share your information with our community.', 'شارك معلوماتك مع مجتمعنا.', 'Deel uw informatie met onze community.', 'Partagez vos informations avec notre communauté.', 'Teilen Sie Ihre Informationen mit unserer Community.', 'Поделитесь своей информацией с нашим сообществом.', 'Comparte tu información con nuestra comunidad.', 'Bilgilerinizi topluluğumuzla paylaşın.'),
(96, 'male', 'Male', 'الذكر', 'Mannetje', 'Mâle', 'Männlich', 'мужчина', 'Masculino', 'Erkek'),
(97, 'female', 'Female', 'إناثا', 'Vrouw', 'Femelle', 'Weiblich', 'женский', 'Hembra', 'Kadın'),
(98, 'finish', 'Finish', 'إنهاء', 'Af hebben', 'terminer', 'Fertig', 'Конец', 'Terminar', 'Bitiş'),
(99, 'profile', 'Profile', 'الملف الشخصي', 'Profiel', 'Profil', 'Profil', 'Профиль', 'Perfil', 'Profil'),
(100, 'avatar_uploaded_successfully', 'Your avatar uploaded successfully ', 'تم تحميل الصورة الرمزية بنجاح', 'Je avatar is succesvol geüpload', 'Votre avatar a été téléchargé avec succès', 'Ihr Avatar wurde erfolgreich hochgeladen', 'Ваш аватар успешно загружен', 'Tu avatar subido exitosamente', 'Avatarın başarıyla yüklendi'),
(102, 'setting_updated', 'Settings successfully updated!', 'تم تحديث الإعدادات بنجاح!', 'Instellingen succesvol bijgewerkt!', 'Paramètres mis à jour avec succès!', 'Einstellungen erfolgreich aktualisiert!', 'Настройки успешно обновлены!', 'Configuraciones exitosamente actualizadas!', 'Ayarlar başarıyla güncellendi!'),
(103, 'invalid_type_image', 'Invalid Type, you must select image file.', 'نوع غير صالح ، يجب تحديد ملف الصورة.', 'Ongeldig type, moet u een afbeeldingsbestand selecteren.', 'Type non valide, vous devez sélectionner un fichier image.', 'Ungültiger Typ, Sie müssen die Bilddatei auswählen.', 'Неверный тип, вы должны выбрать файл изображения.', 'Tipo no válido, debe seleccionar el archivo de imagen.', 'Geçersiz Tür, görüntü dosyasını seçmelisiniz.'),
(104, 'img_size_not_greater', 'Image Size should not be greater than', 'يجب ألا يكون حجم الصورة أكبر من', 'Beeldgrootte mag niet groter zijn dan', 'La taille de l\'image ne doit pas être supérieure à', 'Die Bildgröße sollte nicht größer als sein', 'Размер изображения не должен быть больше чем', 'El tamaño de la imagen no debe ser mayor que', 'Resim Boyutu daha büyük olmamalıdır'),
(105, 'edit_profile', 'Edit Profile', 'تعديل الملف الشخصي', 'Bewerk profiel', 'Editer le profil', 'Profil bearbeiten', 'Редактировать профиль', 'Editar perfil', 'Profili Düzenle'),
(106, 'search', 'Search', 'بحث', 'Zoeken', 'Chercher', 'Suche', 'Поиск', 'Buscar', 'Arama'),
(107, 'advanced_search', 'Advanced Search', 'البحث المتقدم', 'geavanceerd zoeken', 'Recherche Avancée', 'Erweiterte Suche', 'Расширенный поиск', 'Búsqueda Avanzada', 'gelişmiş Arama'),
(108, 'search_questions_and_users', 'Search questions and users...', 'البحث عن الأسئلة والمستخدمين ...', 'Zoekvragen en gebruikers ...', 'Rechercher des questions et des utilisateurs ...', 'Fragen und Benutzer suchen ...', 'Поиск вопросов и пользователей ...', 'Buscar preguntas y usuarios ...', 'Soruları ve kullanıcıları arayın ...'),
(109, 'questions', 'Questions', 'الأسئلة', 'vragen', 'Des questions', 'Fragen', 'Вопросы', 'Preguntas', 'Sorular'),
(110, 'users', 'Users', 'المستخدمين', 'gebruikers', 'Utilisateurs', 'Benutzer', 'пользователей', 'Usuarios', 'Kullanıcılar'),
(111, 'report_post', 'Report Post', 'تقرير المشاركة', 'Rapportpost', 'Signaler un message', 'Beitrag melden', 'Пожаловаться на сообщение', 'Reportar publicacion', 'Gönderiyi bildir'),
(112, 'trending', 'Trending', 'الشائع', 'richten', 'Tendances', 'tendieren', 'иметь тенденцию', 'Tendencias', 'Akım'),
(114, 'user_profile', 'User Profile', 'ملف تعريفي للمستخدم', 'Gebruikersprofiel', 'Profil de l\'utilisateur', 'Benutzerprofil', 'Профиль пользователя', 'Perfil del usuario', 'Kullanıcı profili'),
(115, 'general', 'General', 'جنرال لواء', 'Algemeen', 'Général', 'Allgemeines', 'генеральный', 'General', 'Genel'),
(117, 'notifications', 'Notifications', 'إخطارات', 'meldingen', 'Les notifications', 'Benachrichtigungen', 'Уведомления', 'Notificaciones', 'Bildirimler'),
(118, 'account', 'Account', 'الحساب', 'Account', 'Compte', 'Konto', 'учетная запись', 'Cuenta', 'hesap'),
(119, 'blocked_users', 'Blocked Users', 'مستخدمين محجوبين', 'Geblokkeerde gebruikers', 'Utilisateurs bloqués', 'Blockierte Benutzer', 'Заблокированные пользователи', 'Usuarios bloqueados', 'Engellenmiş kullanıcılar'),
(121, 'delete_account', 'Delete Account', 'حذف الحساب', 'Account verwijderen', 'Supprimer le compte', 'Konto löschen', 'Удалить аккаунт', 'Borrar cuenta', 'Hesabı sil'),
(122, 'unbloack', 'Unblock', 'رفع الحظر', 'deblokkeren', 'Débloquer', 'Blockierung aufheben', 'открыть', 'Desatascar', 'engeli kaldırmak'),
(123, 'change', 'Change', 'يتغيرون', 'Verandering', 'Changement', 'Veränderung', '+ Изменить', 'Cambio', 'Değişiklik'),
(124, 'current_password', 'Current Password', 'كلمة المرور الحالي', 'huidig ​​wachtwoord', 'Mot de passe actuel', 'derzeitiges Passwort', 'Текущий пароль', 'contraseña actual', 'Şimdiki Şifre'),
(127, 'delete_warn1', 'Are you sure you want to delete your account? All content including published questions, will be permanetly removed!', 'هل انت متأكد انك تريد حذف حسابك؟ سيتم إزالة جميع المحتويات بما في ذلك الأسئلة المنشورة نهائيًا!', 'Weet je zeker dat je je account wilt verwijderen? Alle inhoud inclusief gepubliceerde vragen, zal permanent worden verwijderd!', 'Êtes-vous sûr de vouloir supprimer votre compte? Tout le contenu, y compris les questions publiées, sera définitivement supprimé!', 'Möchten Sie Ihr Konto wirklich löschen? Alle Inhalte einschließlich veröffentlichter Fragen werden permanent entfernt!', 'Вы уверены, что хотите удалить свой аккаунт? Весь контент, включая опубликованные вопросы, будет окончательно удален!', '¿Estás seguro de que quieres eliminar tu cuenta? ¡Todo el contenido, incluidas las preguntas publicadas, se eliminará permanentemente!', 'Hesabınızı silmek istediğinizden emin misiniz? Yayınlanan sorular da dahil olmak üzere tüm içerikler kalıcı olarak kaldırılacak!'),
(128, 'delete_warn2', 'Nobody will be able to find or visit your page.', 'لن يتمكن أحد من العثور على صفحة الخاصة بك أو زيارتها.', 'Niemand zal uw pagina kunnen vinden of bezoeken.', 'Personne ne pourra trouver ou visiter votre page.', 'Niemand kann Ihre Seite finden oder besuchen.', 'Никто не сможет найти или посетить вашу страницу.', 'Nadie podrá encontrar o visitar tu página de.', 'sayfanızı kimse bulamaz veya ziyaret edemez.'),
(129, 'delete', 'Delete', 'حذف', 'Verwijder', 'Effacer', 'Löschen', 'удалять', 'Borrar', 'silmek'),
(130, 'save', 'Save', 'حفظ', 'Opslaan', 'sauvegarder', 'sparen', 'Сохранить', 'Salvar', 'Kayıt etmek'),
(133, 'email', 'Email', 'البريد الإلكتروني', 'E-mail', 'Email', 'Email', 'Эл. адрес', 'Email', 'E-posta'),
(134, 'birth_date', 'Birth date', 'تاريخ الولادة', 'Geboortedatum', 'Date de naissance', 'Geburtstag', 'Дата рождения', 'Fecha de nacimiento', 'Doğum günü'),
(135, 'about_me', 'About Me', 'عني', 'Over mij', 'À propos de moi', 'Über mich', 'Обо мне', 'Sobre mi', 'Benim hakkımda'),
(136, 'location', 'Location', 'موقعك', 'Plaats', 'Emplacement', 'Ort', 'Место нахождения', 'Ubicación', 'yer'),
(137, 'website', 'Website', 'موقع الكتروني', 'Website', 'Site Internet', 'Webseite', 'Веб-сайт', 'Sitio web', 'Web sitesi'),
(138, 'instagram', 'Instagram', 'إينستاجرام', 'Instagram', 'Instagram', 'Instagram', 'Instagram', 'Instagram', 'Instagram'),
(139, 'current_password_dont_match', 'Current password doesn\'t match.', 'كلمة المرور الحالية غير متطابقة.', 'Huidig ​​wachtwoord komt niet overeen.', 'Le mot de passe actuel ne correspond pas.', 'Das aktuelle Passwort stimmt nicht überein.', 'Текущий пароль не совпадает.', 'La contraseña actual no coincide.', 'Mevcut şifre eşleşmiyor.'),
(140, 'new_password_dont_match', 'New password doesn\'t match.', 'كلمة المرور الجديدة غير متطابقة.', 'Nieuw wachtwoord komt niet overeen.', 'Le nouveau mot de passe ne correspond pas.', 'Neues Passwort stimmt nicht überein', 'Новый пароль не совпадает.', 'La nueva contraseña no coincide.', 'Yeni şifre eşleşmiyor.'),
(141, 'email_me_when', 'Email me when', 'البريد الالكتروني لي عندما', 'E-mail mij wanneer', 'Envoyez-moi quand', 'Mailen Sie mir wann', 'Напишите мне, когда', 'Envíame un correo electrónico cuando', 'Bana ne zaman e-posta gönder'),
(142, 'someone_answered_my_questions', 'Someone answered my questions', 'أجاب شخص ما أسئلتي', 'Iemand heeft mijn vragen beantwoord', 'Quelqu\'un a répondu à mes questions', 'Jemand hat meine Fragen beantwortet', 'Кто-то ответил на мои вопросы', 'Alguien contestó mis preguntas', 'Birisi sorularıma cevap verdi'),
(143, 'someone_visited_my_profile', 'Someone visited my profile', 'زار شخص ما ملفي الشخصي', 'Iemand heeft mijn profiel bezocht', 'Quelqu\'un a visité mon profil', 'Jemand hat mein Profil besucht', 'Кто-то посетил мой профиль', 'Alguien visitó mi perfil', 'Birisi profilimi ziyaret etti'),
(144, 'your_account_was_deleted', 'Your account was deleted', 'تم حذف حسابك', 'Je account is verwijderd', 'Votre compte a été supprimé', 'Ihr Konto wurde gelöscht', 'Ваш аккаунт был удален', 'Tu cuenta fue eliminada', 'Hesabınız silindi'),
(145, 'admin_panel', 'Admin Panel', 'لوحة الادارة', 'Administratie Paneel', 'panneau d\'administration', 'Administrationsmenü', 'Панель администратора', 'Panel de administrador', 'Admin Paneli'),
(146, '404_title', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!', 'Looks like you\'re lost in space!'),
(147, '404_desc', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.', 'The page you were looking for doesn\'t exist.'),
(148, 'image_upload_error', 'Image upload error', 'خطأ في تحميل الصورة', 'Fout bij uploaden van afbeelding', 'Erreur de téléchargement d\'image', 'Fehler beim Hochladen der Bilder', 'Ошибка загрузки изображения', 'Error de carga de la imagen', 'Resim yükleme hatası'),
(149, 'year', 'year', 'عام', 'jaar', 'année', 'Jahr', 'год', 'año', 'yıl'),
(150, 'month', 'month', 'شهر', 'maand', 'mois', 'Monat', 'месяц', 'mes', 'ay'),
(151, 'day', 'day', 'يوم', 'dag', 'journée', 'Tag', 'день', 'día', 'gün'),
(152, 'hour', 'hour', 'ساعة', 'uur', 'heure', 'Stunde', 'час', 'hora', 'saat'),
(153, 'minute', 'minute', 'اللحظة', 'minuut', 'minute', 'Minute', 'минут', 'minuto', 'dakika'),
(154, 'second', 'second', 'ثانيا', 'tweede', 'seconde', 'zweite', 'второй', 'segundo', 'ikinci'),
(155, 'years', 'years', 'سنوات', 'jaar', 'années', 'Jahre', 'лет', 'años', 'yıl'),
(156, 'months', 'months', 'الشهور', 'maanden', 'mois', 'Monate', 'месяцы', 'meses', 'ay'),
(157, 'days', 'days', 'أيام', 'dagen', 'journées', 'Tage', 'дней', 'dias', 'günler'),
(158, 'hours', 'hours', 'ساعات', 'uur', 'heures', 'Std', 'часов', 'horas', 'saatler'),
(159, 'minutes', 'minutes', 'الدقائق', 'notulen', 'minutes', 'Protokoll', 'минут', 'minutos', 'dakika'),
(160, 'seconds', 'seconds', 'ثواني', 'seconden', 'secondes', 'Sekunden', 'секунд', 'segundos', 'saniye'),
(161, 'time_ago', 'ago', 'منذ', 'geleden', 'depuis', 'vor', 'тому назад', 'hace', 'önce'),
(162, 'no_notifications', 'no_notifications', 'لا إشعارات', 'geen notificaties', 'aucune notification', 'keine Benachrichtigungen', 'no_notifications', 'no Notificaciones', 'no_notifications'),
(163, 'you_do_not_have_any_notifications', 'You do not have any notifications', 'ليس لديك أي إخطارات', 'U hebt geen meldingen', 'Vous n\'avez aucune notification', 'Sie haben keine Benachrichtigungen', 'У вас нет никаких уведомлений', 'No tienes ninguna notificación.', 'Hiçbir bildiriminiz yok'),
(164, 'notification_answer_it', 'Answer It', 'الإجابة عليه', 'Antwoord Het', 'Répondez', 'Antworte es', 'Ответить', 'Contestarlo', 'Cevapla'),
(165, 'notification_asked_you_question', 'asked you a Question.', 'سألتك سؤال.', 'vroeg je een vraag.', 'vous a posé une question.', 'habe dir eine Frage gestellt.', 'задал вам вопрос.', 'te hice una pregunta.', 'sana bir soru sordum.'),
(166, 'notification_answered_your_question', 'answered your question.', 'أجاب على سؤالك.', '.', 'répondu à votre question.', 'beantwortete deine Frage', 'ответил на ваш вопрос.', 'Respondió su pregunta.', 'sorunuzu cevapladı'),
(167, 'notification_view_answer', 'View Answer', 'اعرض الإجابة', 'Bekijk antwoord', 'Voir la réponse', 'Antwort anzeigen', 'Посмотреть ответ', 'Ver respuesta', 'Cevabı Görüntüle'),
(168, 'located_in', 'Located in', 'يقع في', 'Gevestigd in', 'Situé dans', 'Gelegen in', 'Находится в', 'Situado en', 'Konumlanmış'),
(169, 'follow', 'Follow', 'إتبع', 'Volgen', 'Suivre', 'Folgen', 'следить', 'Seguir', 'Takip et'),
(170, 'following', 'Following', 'التالية', 'Volgend op', 'Suivant', 'Folgenden', 'Следующий', 'Siguiendo', 'Takip etme'),
(171, 'notification_followed_u', 'Followed you', 'تبعتك', 'Je gevolgd', 'Je t\'ai suivi', 'Dir gefolgt', 'Следовал за вами', 'Te siguió', 'Seni takip'),
(172, 'notification_unfollowed_u', 'UnFollowed you', 'الغيت متابعتك', 'UnFollowed you', 'Ne vous suit plus', 'Hat dir entfolgt', 'Отписался от вас', 'Dejó de seguirte', 'Tavsiye edilmedi'),
(173, 'followers', 'Followers', 'متابعون', 'Volgers', 'Suiveurs', 'Anhänger', 'Читают', 'Seguidores', 'İzleyiciler'),
(174, 'show_more', 'Show more', 'أظهر المزيد', 'Laat meer zien', 'Montre plus', 'Zeig mehr', 'Показать больше', 'Mostrar más', 'Daha fazla göster'),
(175, 'no_followers_yet', 'No followers yet', 'لا يوجد متابعين بعد', 'Nog geen volgers', 'Pas encore d\'adeptes', 'Noch keine Follower', 'Нет последователей еще', 'No hay seguidores todavía', 'Henüz takipçisi yok'),
(176, 'not_following_any_user', 'Not following any user', 'عدم متابعة أي مستخدم', 'Geen enkele gebruiker volgen', 'Ne suit aucun utilisateur', 'Folgt keinem Benutzer', 'Не подписан ни на одного пользователя', 'No siguiendo a ningún usuario', 'Herhangi bir kullanıcıyı takip etmemek'),
(177, 'no_more_followers', 'No more followers', 'لا مزيد من المتابعين', 'Geen volgers meer', 'Pas plus d\'adeptes', 'Keine Anhänger mehr', 'Нет больше последователей', 'No mas seguidores', 'Artık takipçisi yok'),
(178, 'no_more_following_user', 'No more following user', 'لا مزيد من متابعة المستخدم', 'Geen volgende gebruiker meer', 'Pas plus d\'utilisateur suivant', 'Kein weiterer Benutzer mehr', 'Нет больше следующего пользователя', 'No más usuario siguiente', 'Artık kullanıcı yok'),
(179, 'country', 'Country', 'بلد', 'land', 'Pays', 'Land', 'Страна', 'País', 'ülke'),
(180, 'people_you_may_know', 'People you may know', 'قد تكون تعرف الناس', 'Mensen die u misschien kent', 'Les gens que vous connaissez peut-être', 'Leute die Sie vielleicht kennen', 'Люди, которых вы, возможно, знаете', 'Gente que pueda conocer', 'Tanıyor Olabileceğiniz İnsanlar'),
(181, 'no_more_users_to_show', 'No more users to show', 'لا مزيد من المستخدمين للعرض', 'Geen gebruikers meer om te laten zien', 'Pas plus d\'utilisateurs à montrer', 'Keine weiteren Benutzer zum Anzeigen', 'Нет больше пользователей, чтобы показать', 'No hay más usuarios para mostrar', 'Gösterilecek başka kullanıcı yok'),
(182, 'question', 'Question', 'سؤال', 'Vraag', 'Question', 'Frage', 'Вопрос', 'Pregunta', 'Soru'),
(183, 'photo_poll', 'Photo Poll', 'استفتاء صور', 'Foto peiling', 'Sondage photo', 'Fotoumfrage', 'Фото опрос', 'Encuesta de fotos', 'Fotoğraf Anketi'),
(184, 'what_s_going_on__ask_anything..', 'What&#039;s going on? Ask anything..', 'ماذا يحدث هنا؟ اسأل أي شيء ..', 'Wat gebeurd er? Vraag iets..', 'Que se passe-t-il? Demandez n\'importe quoi ..', 'Was ist los? Frag irgendwas..', 'В чем дело? Спроси что-нибудь ..', '¿Que esta pasando? Pregunta cualquier cosa..', 'Neler oluyor? Dilediğini sor..'),
(185, 'ask_anonymously', 'Ask anonymously', 'اسأل متخفيا', 'vraag anoniem', 'demander anonymement', 'Anonym Fragen', 'спросить анонимно', 'Pregunta anónimamente', 'Anonim olarak sor'),
(186, 'create_photo_poll', 'Create photo poll', 'إنشاء استفتاء صور', 'Maak een foto-enquête', 'Créer un sondage photo', 'Fotoumfrage erstellen', 'Создать фото опрос', 'Crear encuesta de fotos', 'Fotoğraf anketi oluştur'),
(187, 'choice_1', 'Choice 1', 'الاختيار 1', 'Keuze 1', 'Choix 1', 'Wahl 1', 'Выбор 1', 'Elección 1', 'Seçim 1'),
(188, 'click_to_upload_your_image', 'Click to upload your image', 'انقر لتحميل صورتك', 'Klik om uw afbeelding te uploaden', 'Cliquez pour télécharger votre image', 'Klicken Sie hier, um Ihr Bild hochzuladen', 'Нажмите, чтобы загрузить свое изображение', 'Haz click para subir tu imagen.', 'Resminizi yüklemek için tıklayın'),
(189, 'choice_2', 'Choice 2', 'الاختيار 2', 'Keuze 2', 'Choix 2', 'Wahl 2', 'Выбор 2', 'Elección 2', 'Seçim 2'),
(190, 'cancel', 'Cancel', 'إلغاء', 'annuleren', 'Annuler', 'Stornieren', 'отменить', 'Cancelar', 'İptal etmek'),
(191, 'you_haven___t_posted_any_questions_yet', 'You haven&#039;t posted any questions yet', 'لم تنشر أي أسئلة حتى الآن', 'Je hebt nog geen vragen geplaatst', 'Vous n\'avez pas encore posté de questions', 'Sie haben noch keine Fragen gestellt', 'У вас еще нет вопросов', 'Aún no has publicado ninguna pregunta.', 'Henüz bir soru göndermediniz'),
(192, 'done', 'Done.', 'فعله.', 'Gedaan.', 'Terminé.', 'Erledigt.', 'Готово.', 'Hecho.', 'Bitti.'),
(193, 'you_have_to_select_two_image.', 'You have to select two image.', 'يجب عليك اختيار صورتين.', 'U moet twee afbeeldingen selecteren.', 'Vous devez sélectionner deux images.', 'Sie müssen zwei Bilder auswählen.', 'Вы должны выбрать два изображения.', 'Tienes que seleccionar dos imágenes.', 'İki resim seçmelisin.'),
(194, 'ask', 'Ask', 'يطلب', 'Vragen', 'Demander', 'Fragen', 'Просить', 'Pedir', 'Sor'),
(195, 'notification_profile_visit_u', 'visited your profile.', 'زار ملفك الشخصي.', 'heb je profiel bezocht.', 'a visité votre profil.', 'besuchte dein Profil.', 'посетил ваш профиль.', 'visitó tu perfil.', 'Profilini ziyaret etti.'),
(196, 'answer_it', 'Answer It', 'الإجابة عليه', 'Antwoord Het', 'Répondez', 'Antworte es', 'Ответить', 'Contestarlo', 'Cevapla'),
(197, 'write_your_answer_here.', 'Write your answer here.', 'اكتب إجابتك هنا.', 'Schrijf hier je antwoord.', 'Écrivez votre réponse ici.', 'Schreibe deine Antwort hier.', 'Напишите здесь ваш ответ.', 'Escribe tu respuesta aquí.', 'Cevabınızı buraya yazın.'),
(198, 'answer', 'Answer', 'إجابة', 'Antwoord', 'Réponse', 'Antworten', 'Ответ', 'Responder', 'Cevap'),
(199, 'anonymous', 'Anonymous', 'مجهول', 'Anoniem', 'Anonyme', 'Anonym', 'анонимное', 'Anónimo', 'Anonim'),
(200, 'share', 'Share', 'شارك', 'Delen', 'Partager', 'Aktie', 'Поделиться', 'Compartir', 'Pay'),
(201, 'it___s_not_recommended_to_make_a_poll_with_photos_of_your_friends_without_their_consent._we_will_remove_such_a_poll__if_it___s_reported.', 'It&#039;s not recommended to make a poll with photos of your friends without their consent. We will remove such a poll, if it&#039;s reported.', 'لا يُنصح بإجراء استطلاع رأي يحتوي على صور لأصدقائك دون موافقتهم. سنقوم بإزالة مثل هذا الاستطلاع ، إذا تم الإبلاغ عنه.', 'Het is niet aan te raden om een ​​peiling uit te voeren met foto\'s van je vrienden zonder hun toestemming. We zullen een dergelijke peiling verwijderen als deze is gemeld.', 'Il n\'est pas recommandé de faire un sondage avec les photos de vos amis sans leur consentement. Nous allons supprimer un tel sondage, s\'il est rapporté.', 'Es wird nicht empfohlen, eine Umfrage mit Fotos Ihrer Freunde ohne deren Zustimmung durchzuführen. Wir werden eine solche Umfrage entfernen, wenn sie gemeldet wird.', 'Не рекомендуется делать опрос с фотографиями ваших друзей без их согласия. Мы удалим такой опрос, если он будет опубликован.', 'No se recomienda hacer una encuesta con fotos de tus amigos sin su consentimiento. Vamos a eliminar tal encuesta, si se informa.', 'Arkadaşlarınızın fotoğraflarıyla izinsiz olarak anket yapmak önerilmez. Bildirildiği takdirde böyle bir anketi kaldıracağız.'),
(202, 'edit', 'Edit', 'تصحيح', 'Bewerk', 'modifier', 'Bearbeiten', 'редактировать', 'Editar', 'Düzenle'),
(203, 'report', 'Report', 'أبلغ عن', 'Verslag doen van', 'rapport', 'Bericht', 'отчет', 'Informe', 'Rapor'),
(204, 'delete_question', 'Delete question', 'حذف السؤال', 'Vraag verwijderen', 'Supprimer la question', 'Frage löschen', 'Удалить вопрос', 'Eliminar pregunta', 'Soruyu sil'),
(205, 'are_you_sure_you_want_to_continue__this_action_can_t_be_undo', 'Are you sure you want to continue? this action can&#039;t be undo', 'هل أنت متأكد من أنك تريد المتابعة؟ لا يمكن التراجع عن هذا الإجراء', 'Weet je zeker dat je door wilt gaan? deze actie kan niet ongedaan worden gemaakt', 'Es-tu sur de vouloir continuer? cette action ne peut pas être annulée', 'Sind Sie sicher, dass Sie fortfahren möchten? Diese Aktion kann nicht rückgängig gemacht werden', 'Вы уверены что хотите продолжить? это действие не может быть отменено', 'Estás seguro de que quieres continuar? esta acción no se puede deshacer', 'Devam etmek istediğine emin misin? bu işlem geri alınamaz'),
(206, 'close', 'CLOSE', 'أغلق', 'DICHTBIJ', 'FERMER', 'SCHLIESSEN', 'БЛИЗКО', 'CERRAR', 'KAPAT'),
(207, 'question_deleted_successfully', 'Question deleted successfully', 'تم حذف السؤال بنجاح', 'Vraag is met succes verwijderd', 'Question supprimée avec succès', 'Frage erfolgreich gelöscht', 'Вопрос успешно удален', 'Pregunta eliminada exitosamente', 'Soru başarıyla silindi'),
(208, 'error_while_delete_question', 'An error occurred while deleting the question.', 'حدث خطأ أثناء حذف السؤال.', 'Er is een fout opgetreden bij het verwijderen van de vraag.', 'Une erreur s\'est produite lors de la suppression de la question.', 'Beim Löschen der Frage ist ein Fehler aufgetreten.', 'Произошла ошибка при удалении вопроса.', 'Se ha producido un error al eliminar la pregunta.', 'Soruyu silerken bir hata oluştu.'),
(209, 'edit_question', 'Edit question', 'تحرير السؤال', 'Bewerk vraag', 'Modifier la question', 'Frage bearbeiten', 'Редактировать вопрос', 'Editar pregunta', 'Soruyu düzenle'),
(210, 'question_updated_successfully', 'Question updated successfully.', 'تم تحديث السؤال بنجاح.', 'Vraag is succesvol bijgewerkt.', 'Question mise à jour avec succès.', 'Frage erfolgreich aktualisiert.', 'Вопрос успешно обновлен.', 'Pregunta actualizada con éxito.', 'Soru başarıyla güncellendi.'),
(211, 'load_more_questions', 'Load more questions', 'تحميل المزيد من الأسئلة', 'Laad meer vragen', 'Charger plus de questions', 'Laden Sie weitere Fragen', 'Загрузить больше вопросов', 'Cargar más preguntas', 'Daha fazla soru yükle'),
(212, 'no_more_questions', 'No more questions', 'لا مزيد من الاسئلة', 'Geen vragen meer', 'Pas plus de questions', 'Keine weiteren Fragen', 'Больше нет вопросов', 'No mas preguntas', 'Daha fazla soru yok'),
(213, 'report_question', 'Report question', 'السؤال عن السؤال', 'Vraag melden', 'Signaler une question', 'Frage melden', 'Сообщить о вопросе', 'Pregunta de informe', 'Soruyu bildir'),
(214, 'write_here_the_reason_of_reporting.', 'Write here the reason of reporting.', 'اكتب هنا سبب الإبلاغ.', 'Schrijf hier de reden van rapportage.', 'Écrivez ici la raison du signalement.', 'Schreiben Sie hier den Grund der Meldung.', 'Напишите здесь причину сообщения.', 'Escriba aquí el motivo de la presentación de informes.', 'Raporlama nedenini buraya yazın.'),
(215, 'delete_report', 'Delete report', 'حذف التقرير', 'Verwijder rapport', 'Supprimer le rapport', 'Bericht löschen', 'Удалить отчет', 'Eliminar informe', 'Raporu sil'),
(216, 'unreport', 'Unreport', 'Unreport', 'Unreport', 'Déclarer', 'Bericht nicht melden', 'Unreport', 'Desinformar', 'Bildir'),
(217, 'report_deleted_successfully', 'Report deleted successfully', 'تم حذف التقرير بنجاح', 'Rapport is succesvol verwijderd', 'Rapport supprimé avec succès', 'Bericht erfolgreich gelöscht', 'Отчет успешно удален', 'Informe eliminado con éxito', 'Rapor başarıyla silindi'),
(218, 'question_likes', 'Question likes', 'سؤال يحب', 'Vraag leuk', 'Question aime', 'Frage mag', 'Вопрос лайков', 'Pregunta le gusta', 'Soru sever'),
(219, 'you_already_shared_this_post', 'You already shared this post.', 'لقد شاركت هذه المشاركة بالفعل.', 'Je hebt dit bericht al gedeeld.', 'Vous avez déjà partagé ce post.', 'Sie haben diesen Beitrag bereits geteilt.', 'Вы уже поделились этим постом.', 'Ya has compartido esta publicación.', 'Bu yayını zaten paylaştınız.'),
(220, 'question_shared_successfully', 'Question was successfully added to your timeline!\r\n\r\n', 'تمت إضافة سؤال بنجاح إلى المخطط الزمني الخاص بك!', 'Vraag is toegevoegd aan je tijdlijn!', 'La question a été ajoutée avec succès à votre timeline!', 'Die Frage wurde erfolgreich zu Ihrer Timeline hinzugefügt!', 'Вопрос был успешно добавлен в ваш график!', 'La pregunta se agregó exitosamente a tu línea de tiempo!', 'Soru, zaman çizelgenize başarıyla eklendi!'),
(221, 'shared', 'shared', 'مشترك', 'gedeelde', 'partagé', 'geteilt', 'общий', 'compartido', 'paylaşılan'),
(222, 'question.', 'question.', 'سؤال.', 'vraag.', 'question.', 'Frage.', 'вопрос.', 'pregunta.', 'soru.'),
(223, 'like_your_question', 'liked your question', 'أحب سؤالك', 'beviel je vraag', 'aimé votre question', 'mochte deine Frage', 'понравился твой вопрос', 'me gustó tu pregunta', 'sorunuzu beğendim'),
(224, 'share_your_question', 'shared your question', 'شارك سؤالك', 'deelde je vraag', 'partagé votre question', 'hat deine Frage geteilt', 'поделился своим вопросом', 'compartió tu pregunta', 'sorunuzu paylaştı'),
(225, 'answered', 'answered', 'أجاب', 'antwoordde', 'répondu', 'antwortete', 'ответил', 'contestado', 'cevap'),
(226, 'open_in_new_tab', 'Open in new tab', 'فتح في علامة تبويب جديدة', 'Openen in nieuw tabblad', 'Ouvrir dans un nouvel onglet', 'In neuem Tab öffnen', 'Открыть в новой вкладке', 'Abrir en una pestaña nueva', 'Yeni sekmede aç'),
(227, 'share_question', 'Share question', 'حصة السؤال', 'Vraag delen', 'Partager la question', 'Frage teilen', 'Поделитесь вопросом', 'Compartir pregunta', 'Soruyu paylaş'),
(228, 'share_to_timeline', 'Share to timeline', 'شارك في الجدول الزمني', 'Delen op tijdlijn', 'Partager sur la timeline', 'Teilen Sie mit der Zeitleiste', 'Поделиться с графиком', 'Compartir en la línea de tiempo', 'Zaman çizelgesine paylaş'),
(229, 'copy_link', 'Copy link', 'انسخ الرابط', 'Kopieer link', 'Copier le lien', 'Link kopieren', 'Копировать ссылку', 'Copiar link', 'Bağlantıyı kopyala'),
(230, 'click_on_link_to_copy', 'Click on link to Copy', 'انقر على رابط للنسخ', 'Klik op de link om te kopiëren', 'Cliquez sur le lien pour copier', 'Klicken Sie auf den Link zum Kopieren', 'Нажмите на ссылку, чтобы скопировать', 'Haga clic en el enlace para copiar', 'Kopyalamak için linke tıklayın'),
(231, 'link_copied.', 'Link copied.', 'رابط المنسوخ.', 'Link gekopieerd.', 'Lien copié.', 'Link kopiert', 'Ссылка скопирована.', 'Enlace copiado.', 'Bağlantı kopyalandı.'),
(232, 'trending_questions', 'Trending Questions', 'الأسئلة الشائعة', 'Trending vragen', 'Questions tendances', 'Aktuelle Fragen', 'Актуальные вопросы', 'Preguntas de tendencias', 'Popüler Sorular'),
(233, 'notification_view_question', 'View question', 'عرض السؤال', 'Bekijk vraag', 'Voir la question', 'Frage anzeigen', 'Посмотреть вопрос', 'Ver pregunta', 'Soruyu görüntüle'),
(234, 'someone_liked_my_questions', 'Someone liked my questions', 'شخص ما أحب أسئلتي', 'Iemand vond mijn vragen leuk', 'Quelqu\'un a aimé mes questions', 'Jemand mochte meine Fragen', 'Кому-то понравились мои вопросы', 'A alguien le gustaron mis preguntas', 'Birisi sorularımı beğendi'),
(235, 'someone_shared_my_questions', 'Someone shared my questions', 'شارك شخص ما أسئلتي', 'Iemand heeft mijn vragen gedeeld', 'Quelqu\'un a partagé mes questions', 'Jemand hat meine Fragen geteilt', 'Кто-то поделился моими вопросами', 'Alguien ha compartido mis preguntas', 'Birisi sorularımı paylaştı'),
(236, 'mention_post', 'mentioned you in a question.', 'ذكرك في سؤال.', 'heeft je in een vraag genoemd.', 'vous a mentionné dans une question.', 'hat dich in einer Frage erwähnt.', 'упомянул вас в вопросе.', 'te mencioné en una pregunta.', 'bir soruda sizden bahsetti.'),
(237, 'mention_your_question_replay', 'mentioned you in a reply.', 'ذكرت لك في الرد.', 'heeft je in een antwoord genoemd.', 'vous a mentionné dans une réponse.', 'hat Sie in einer Antwort erwähnt.', 'упомянул вас в ответе.', 'te mencioné en una respuesta.', 'bir cevapta sizden bahsetti.');
INSERT INTO `langs` (`id`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(238, 'mention_your_question_answer', 'mentioned you in a answer.', 'ذكرك في إجابة.', 'heeft je in een antwoord genoemd.', 'vous a mentionné dans une réponse.', 'hat dich in einer Antwort erwähnt.', 'упомянул вас в ответе.', 'Te mencioné en una respuesta.', 'Bir cevapta sizden bahsetti.'),
(239, 'please_log_in_to_ask_answer_share_and_like__', 'Please log in to ask,answer,share and like !', 'يرجى تسجيل الدخول لطرح والإجابة والمشاركة ومثل!', 'Log in om te vragen, te beantwoorden, te delen en leuk te vinden!', 'Veuillez vous connecter pour demander, répondre, partager et aimer!', 'Bitte melden Sie sich an, um zu fragen, zu antworten, zu teilen und zu mögen!', 'Пожалуйста, войдите, чтобы задать вопрос, ответить, поделиться и лайк!', 'Por favor inicie sesión para preguntar, responder, compartir y gustar!', 'Sormak, cevaplamak, paylaşmak ve beğenmek için lütfen giriş yapın!'),
(240, 'no_results_found_for_your_query.', 'No results found for your query.', 'لا توجد نتائج للاستعلام.', 'Geen resultaten gevonden voor uw zoekopdracht.', 'Aucun résultat n\'a été trouvé pour votre recherche.', 'Keine Ergebnisse für Ihre Anfrage gefunden.', 'Результатов по вашему запросу не найдено.', 'No se encontraron resultados para su consulta.', 'Terimi için sonuç bulunamadı.'),
(241, 'write_your_replay_here.', 'Write your replay here.', 'اكتب ردك هنا.', 'Schrijf hier je herhaling.', 'Ecrivez votre replay ici.', 'Schreiben Sie Ihre Wiederholung hier.', 'Напишите свой реплей здесь.', 'Escribe tu repetición aquí.', 'Replay\'inizi buraya yazın.'),
(242, 'replay', 'Reply', 'الرد', 'Antwoord', 'Répondre', 'Antworten', 'Ответить', 'Respuesta', 'cevap'),
(243, 'replay_it', 'Reply', 'الرد', 'Antwoord', 'Répondre', 'Antworten', 'Ответить', 'Respuesta', 'cevap'),
(244, 'notification_view_replay', 'view reply', 'شاهد الرد', 'bekijk antwoord', 'voir la réponse', 'Antwort anzeigen', 'посмотреть ответ', 'ver respuesta', 'cevabı görüntüle'),
(245, 'replay_your_question', 'replied to your answer', 'رد على إجابتك', 'antwoordde op uw antwoord', 'répondu à votre réponse', 'antwortete auf deine Antwort', 'ответил на ваш ответ', 'respondió a tu respuesta', 'cevabınıza cevap verdi'),
(246, 'no_replies_yet.', 'No replies yet.', 'لا توجد ردود بعد.', 'Nog geen antwoorden.', 'Aucune réponse pour le moment.', 'Noch keine Antworten', 'Ответов пока нет.', 'No hay respuestas todavía.', 'Henüz bir cevap yok.'),
(247, 'no_replies_to_show.', 'No replies to show.', 'لا توجد ردود لعرضها.', 'Geen antwoorden te zien.', 'Aucune réponse à afficher.', 'Keine Antworten zu zeigen.', 'Нет ответов, чтобы показать.', 'No hay respuestas para mostrar.', 'Gösterilecek cevap yok.'),
(248, 'like_your_replay', 'liked your reply', 'أعجبني ردك', 'vond je antwoord leuk', 'aimé votre réponse', 'mochte deine Antwort', 'понравился твой ответ', 'me gustó tu respuesta', 'cevabını beğendim'),
(249, 'public', 'Public', 'عامة', 'Openbaar', 'Publique', 'Öffentlichkeit', 'общественного', 'Público', 'halka açık'),
(250, 'private', 'Private', 'نشر', 'Privaat', 'Privé', 'Privatgelände', 'Частный', 'Privado', 'Özel'),
(251, 'user_ask', 'asked you a question', 'سألتك سؤال', 'stelde je een vraag', 'vous a posé une question', 'habe dir eine Frage gestellt', 'задал вам вопрос', 'te hice una pregunta', 'sana bir soru sordum'),
(252, 'asked', 'asked', 'طلبت', 'gevraagd', 'a demandé', 'fragte', 'спросил', 'preguntó', 'diye sordu'),
(253, 'someone', 'Someone', 'شخصا ما', 'Iemand', 'Quelqu\'un', 'Jemand', 'Кто то', 'Alguien', 'Birisi'),
(254, 'no_question_found.', 'No question found.', 'لم يتم العثور على سؤال.', 'Geen vraag gevonden', 'Aucune question trouvée.', 'Keine Frage gefunden', 'Вопрос не найден.', 'No se encontró ninguna pregunta.', 'Hiçbir soru bulunamadı.'),
(255, 'no_users_found.', 'No users found.', 'لم يتم العثور على أي مستخدم.', 'Geen gebruikers gevonden.', 'Aucun utilisateur trouvé.', 'Keine Benutzer gefunden.', 'Пользователи не найдены.', 'No se encontraron usuarios.', 'Kullanıcı bulunamadı.'),
(256, 'recent_searches', 'Recent searches', 'عمليات البحث الأخيرة', 'Recente zoekopdrachten', 'Recherches récentes', 'Letzte Suchanfragen', 'Недавние поиски', 'Búsquedas recientes', 'Son aramalar'),
(257, 'clear', 'Clear', 'واضح', 'Duidelijk', 'Clair', 'klar', 'Очистить', 'Claro', 'Açık'),
(258, 'trending_searches', 'Trending searches', 'عمليات البحث الرائجة', 'Populaire zoekopdrachten', 'Tendances de recherche', 'Trend-Suchanfragen', 'Тенденции поиска', 'Búsquedas de tendencias', 'Popüler aramalar'),
(259, 'not_recent_search_found', 'Not recent search found', 'لم يتم العثور على بحث حديث', 'Geen recente zoekopdracht gevonden', 'Recherche récente non trouvée', 'Nicht letzte Suche gefunden', 'Не найден недавний поиск', 'No se ha encontrado una búsqueda reciente.', 'Son arama bulunamadı'),
(260, 'not_trending_search_found', 'Not trending search found', 'لم يتم العثور على نتائج البحث', 'Geen trending zoekopdracht gevonden', 'Pas de recherche trouvée', 'Trendsuche nicht gefunden', 'Не найдено тенденций поиска', 'No hay tendencias de búsqueda encontradas', 'Trend araması bulunamadı'),
(261, 'load_more_users', 'Load more users', 'تحميل المزيد من المستخدمين', 'Meer gebruikers laden', 'Charger plus d\'utilisateurs', 'Laden Sie mehr Benutzer', 'Загрузить больше пользователей', 'Cargar más usuarios', 'Daha fazla kullanıcı yükle'),
(262, 'no_more_users', 'No more users', 'لا مزيد من المستخدمين', 'Geen gebruikers meer', 'Pas plus d\'utilisateurs', 'Keine weiteren Benutzer', 'Нет больше пользователей', 'No mas usuarios', 'Başka kullanıcı yok'),
(263, 'advertising', 'Advertising', 'إعلان', 'Advertising', 'La publicité', 'Werbung', 'реклама', 'Publicidad', 'reklâm'),
(264, 'create_new', 'Create New', 'خلق جديد إبداع جديد', 'Maak nieuw', 'Créer un nouveau', 'Erstelle neu', 'Создать новый', 'Crear nuevo', 'Yeni oluşturmak'),
(265, 'wallet', 'Wallet', 'محفظة نقود', 'Portemonnee', 'Portefeuille', 'Brieftasche', 'Бумажник', 'Billetera', 'Cüzdan'),
(266, 'id', 'id', 'هوية شخصية', 'ID kaart', 'identifiant', 'Ich würde', 'Я бы', 'carné de identidad', 'İD'),
(267, 'company', 'company', 'شركة', 'bedrijf', 'entreprise', 'Unternehmen', 'Компания', 'empresa', 'şirket'),
(268, 'bidding', 'bidding', 'مزايدة', 'bod', 'enchère', 'Bieten', 'торги', 'ofertas', 'teklif verme'),
(269, 'clicks', 'clicks', 'نقرات', 'clicks', 'clics', 'Klicks', 'щелчки', 'clics', 'tıklama'),
(270, 'views', 'views', 'الآراء', 'keer bekeken', 'vues', 'Ansichten', 'Просмотры', 'puntos de vista', 'görünümler'),
(271, 'status', 'status', 'الحالة', 'staat', 'statut', 'Status', 'статус', 'estado', 'durum'),
(272, 'action', 'action', 'عمل', 'actie', 'action', 'Aktion', 'действие', 'acción', 'aksiyon'),
(273, 'no_ads_found', 'No ads found', 'لم يتم العثور على إعلانات', 'Geen advertenties gevonden', 'Aucune annonce trouvée', 'Keine Anzeigen gefunden', 'Объявления не найдены', 'No se encontraron anuncios', 'Hiçbir reklam bulunamadı'),
(274, 'enter_a_link_to_your_site', 'Enter a link to your site', 'أدخل رابطًا إلى موقعك', 'Voer een link naar uw site in', 'Entrez un lien vers votre site', 'Geben Sie einen Link zu Ihrer Site ein', 'Введите ссылку на ваш сайт', 'Introduce un enlace a tu sitio', 'Sitenize bir link girin'),
(275, 'title', 'Title', 'عنوان', 'Titel', 'Titre', 'Titel', 'заглавие', 'Título', 'Başlık'),
(276, 'description', 'Description', 'وصف', 'Omschrijving', 'La description', 'Beschreibung', 'Описание', 'Descripción', 'Açıklama'),
(277, 'media', 'Media', 'وسائل الإعلام', 'Media', 'Médias', 'Medien', 'Средства массовой информации', 'Medios de comunicación', 'medya'),
(278, 'no_file_chosen', 'No file chosen', 'لم تقم باختيار ملف', 'Geen bestand gekozen', 'Aucun fichier choisi', 'Keine Datei ausgewählt', 'Файл не выбран', 'Ningún archivo elegido', 'Dosya seçili değil'),
(279, 'audience', 'Audience', 'جمهور', 'Publiek', 'Public', 'Publikum', 'Аудитория', 'Audiencia', 'seyirci'),
(280, 'gender', 'Gender', 'جنس', 'Geslacht', 'Le sexe', 'Geschlecht', 'Пол', 'Género', 'Cinsiyet'),
(281, 'all', 'All', 'الكل', 'Allemaal', 'Tout', 'Alles', 'Все', 'Todos', 'Herşey'),
(282, 'estimated_reach', 'Estimated reach', 'الوصول المقدر', 'Geschat bereik', 'Portée estimée', 'Geschätzte Reichweite', 'Расчетный охват', 'Alcance estimado', 'Tahmini erişim'),
(283, 'publish', 'Publish', 'نشر', 'Publiceren', 'Publier', 'Veröffentlichen', 'Публиковать', 'Publicar', 'Yayınla'),
(284, 'url', 'Url', 'رابط', 'url', 'URL', 'URL', 'Веб-сайт', 'Url', 'URL'),
(285, 'choose_file', 'Choose File', 'اختر ملف', 'Kies bestand', 'Choisir le fichier', 'Datei wählen', 'Выберите файл', 'Elija el archivo', 'Dosya seçin'),
(286, 'pay_per_click', 'Pay Per Click (${{PRICE}})', 'الدفع مقابل النقر ($ {{PRICE}})', 'Betaal per klik ($ {{PRICE}})', 'Pay Per Click ($ {{PRICE}})', 'Pay Per Click ($ {{PRICE}})', 'Оплата за клик ($ {{PRICE}})', 'Pago por clic ($ {{PRICE}})', 'Tıklama Başına Ödeme ($ {{PRICE}})'),
(287, 'pay_per_imprssion', 'Pay Per Impression (${{PRICE}})', 'الدفع مقابل الظهور ($ {{PRICE}})', 'Betaal per vertoning ($ {{PRICE}})', 'Pay Per Impression ($ {{PRICE}})', 'Pay Per Impression ($ {{PRICE}})', 'Оплата за показ ($ {{PRICE}})', 'Pago por impresión ($ {{PRICE}})', 'Gösterim Başına Ödeme ($ {{PRICE}})'),
(288, 'your_current_wallet_balance_is__0__please_top_up_your_wallet_to_continue.', 'Your current wallet balance is: 0, please top up your wallet to continue.', 'رصيد المحفظة الحالي هو: 0 ، يرجى تعبئة محفظتك للمتابعة.', 'Je huidige walletsaldo is: 0, vul je portemonnee bij om door te gaan.', 'Votre solde de portefeuille actuel est égal à: 0, veuillez recharger votre portefeuille pour continuer.', 'Ihr aktuelles Guthabenkonto ist: 0, bitte füllen Sie Ihr Guthaben auf, um fortzufahren.', 'Ваш текущий баланс кошелька: 0, пополните свой кошелек, чтобы продолжить.', 'El saldo actual de su billetera es: 0, por favor recargue su billetera para continuar.', 'Geçerli cüzdan bakiyeniz: 0, lütfen devam etmek için cüzdanınızı doldurun.'),
(289, 'top_up', 'Top up', 'فوق حتى', 'Herladen', 'Recharger', 'Auffüllen', 'Пополнить', 'Completar', 'Tamam'),
(290, 'replenishment_notif', 'Your balance has been replenished by', 'رصيدك تم تجديده بواسطة', 'Je saldo is aangevuld door', 'Votre solde a été réapprovisionné par', 'Ihr Guthaben wurde von aufgestockt', 'Ваш баланс был пополнен', 'Su saldo ha sido repuesto por', 'Bakiyeniz tarafından doldurulan'),
(291, 'my_balance', 'My balance', 'رصيدي', 'Mijn balans', 'Mon solde', 'Mein Gleichgewicht', 'Мой баланс', 'Mi balance', 'Benim dengem'),
(292, 'replenish_my_balance', 'Replenish my balance', 'تجديد رصيدي', 'Vul mijn balans aan', 'Reconstituer mon solde', 'Fülle mein Gleichgewicht auf', 'Пополнить мой баланс', 'Reponer mi equilibrio', 'Bakiyemi doldur'),
(293, 'continue', 'Continue', 'استمر', 'Doorgaan met', 'Continuer', 'Fortsetzen', 'Продолжить', 'Continuar', 'Devam et'),
(294, 'ad_added', 'Your ad has been successfully added!', 'تمت إضافة إعلانك بنجاح!', 'Uw advertentie is succesvol toegevoegd!', 'Votre annonce a été ajoutée avec succès!', 'Ihre Anzeige wurde erfolgreich hinzugefügt!', 'Ваше объявление было успешно добавлено!', 'Su anuncio ha sido añadido con éxito!', 'Reklamınız başarıyla eklendi!'),
(295, 'file_too_big', 'File size error: The file exceeds allowed the limit ({file_size}) and can not be uploaded.', 'خطأ في حجم الملف: تجاوز الملف المسموح به ({file_size}) ولا يمكن تحميله.', 'Fout bestandsgrootte: het bestand overschrijdt de toegestane limiet ({file_size}) en kan niet worden geüpload.', 'Erreur de taille de fichier: le fichier dépasse la limite autorisée ({taille_fichier}) et ne peut pas être téléchargé.', 'Dateigrößenfehler: Die Datei überschreitet den Grenzwert ({file_size}) und kann nicht hochgeladen werden.', 'Ошибка размера файла: файл превышает допустимый предел ({file_size}) и не может быть загружен.', 'Error de tamaño de archivo: el archivo supera el límite permitido ({file_size}) y no se puede cargar.', 'Dosya boyutu hatası: Dosya sınırı aşıyor ({file_size}) ve limit yüklenemiyor.'),
(296, 'select_valid_vid', 'Media file is invalid. Please select a valid video', 'ملف الوسائط غير صالح. يرجى تحديد فيديو صالح', 'Mediabestand is ongeldig. Selecteer een geldige video', 'Le fichier multimédia n\'est pas valide. Veuillez sélectionner une vidéo valide', 'Mediendatei ist ungültig. Bitte wähle ein gültiges Video', 'Медиа-файл недействителен. Пожалуйста, выберите действительное видео', 'El archivo de medios no es válido. Por favor seleccione un video válido', 'Medya dosyası geçersiz. Lütfen geçerli bir video seçin'),
(297, 'select_valid_img', 'Media file is invalid. Please select a valid image', 'ملف الوسائط غير صالح. يرجى اختيار صورة صالحة', 'Mediabestand is ongeldig. Selecteer een geldige afbeelding', 'Le fichier multimédia n\'est pas valide. Veuillez sélectionner une image valide', 'Mediendatei ist ungültig. Bitte wählen Sie ein gültiges Bild aus', 'Медиа-файл недействителен. Пожалуйста, выберите действительное изображение', 'El archivo de medios no es válido. Por favor seleccione una imagen válida', 'Medya dosyası geçersiz. Lütfen geçerli bir resim seçin'),
(298, 'select_valid_img_vid', 'Media file is invalid. Please select a valid image or video', 'ملف الوسائط غير صالح. يرجى تحديد صورة أو فيديو صالح', 'Mediabestand is ongeldig. Selecteer een geldige afbeelding of video', 'Le fichier multimédia n\'est pas valide. Veuillez sélectionner une image ou une vidéo valide', 'Mediendatei ist ungültig. Bitte wählen Sie ein gültiges Bild oder Video', 'Медиа-файл недействителен. Пожалуйста, выберите действительное изображение или видео', 'El archivo de medios no es válido. Por favor seleccione una imagen o video válido', 'Medya dosyası geçersiz. Lütfen geçerli bir resim veya video seçin'),
(299, 'enter_valid_title', 'Please enter a valid title', 'يرجى إدخال عنوان صالح', 'Voer een geldige titel in', 'S\'il vous plaît entrer un titre valide', 'Bitte geben Sie einen gültigen Titel ein', 'Пожалуйста, введите правильный заголовок', 'Por favor ingrese un título válido', 'Lütfen geçerli bir başlık girin'),
(300, 'enter_valid_url', 'Please enter a valid link!', 'الرجاء إدخال رابط صالح!', 'Voer alstublieft een geldige link in!', 'S\'il vous plaît entrer un lien valide!', 'Bitte geben Sie einen gültigen Link ein!', 'Пожалуйста, введите действительную ссылку!', 'Por favor, introduzca un enlace válido!', 'Lütfen geçerli bir link giriniz!'),
(301, 'invalid_company_name', 'Please enter a valid company name!', 'الرجاء إدخال اسم شركة صالح!', 'Voer alstublieft een geldige bedrijfsnaam in!', 'S\'il vous plaît entrer un nom de société valide!', 'Bitte geben Sie einen gültigen Firmennamen ein!', 'Пожалуйста, введите правильное название компании!', 'Por favor, introduzca un nombre de empresa válido!', 'Lütfen geçerli bir firma adı giriniz!'),
(302, 'active', 'Active', 'نشيط', 'Actief', 'actif', 'Aktiv', 'активный', 'Activo', 'Aktif'),
(303, 'edit_ads', 'Edit ads', 'تحرير الإعلانات', 'Bewerk advertenties', 'Modifier des annonces', 'Anzeigen bearbeiten', 'Редактировать объявления', 'Editar anuncios', 'Reklamları düzenle'),
(304, 'back_to_your_ads.', 'Back to your ads.', 'رجوع إلى إعلاناتك.', 'Terug naar uw advertenties.', 'Retour à vos annonces.', 'Zurück zu Ihren Anzeigen', 'Вернуться к вашим объявлениям.', 'Volver a sus anuncios.', 'Reklamlarınıza dönün.'),
(305, 'ad_saved', 'Your ad has been successfully saved!', 'تم حفظ إعلانك بنجاح!', 'Uw advertentie is succesvol opgeslagen!', 'Votre annonce a été enregistrée avec succès!', 'Ihre Anzeige wurde erfolgreich gespeichert!', 'Ваше объявление было успешно сохранено!', 'Su anuncio ha sido guardado con éxito!', 'Reklamınız başarıyla kaydedildi!'),
(307, 'confirm_delete_ad', 'Are you sure you want to delete this ad', 'هل أنت متأكد أنك تريد حذف هذا الإعلان', 'Weet je zeker dat je deze advertentie wilt verwijderen?', 'Êtes-vous sûr de vouloir supprimer cette annonce?', 'Möchten Sie diese Anzeige wirklich löschen?', 'Вы уверены, что хотите удалить эту рекламу', 'Estás seguro de que quieres eliminar esta publicidad', 'Bu reklamı silmek istediğinize emin misiniz?'),
(308, 'delete_ad', 'Delete ad', 'حذف الإعلان', 'Advertentie verwijderen', 'Supprimer l\'annonce', 'Anzeige löschen', 'Удалить объявление', 'Eliminar anuncio', 'Reklamı sil'),
(310, 'not_active', 'Not Active', 'غير نشيط', 'Niet actief', 'Pas actif', 'Nicht aktiv', 'Не активен', 'No activo', 'Aktif değil'),
(314, 'sponsored', 'sponsored', 'برعاية', 'gesponsorde', 'sponsorisé', 'gesponsert', 'спонсируемый', 'patrocinado', 'sponsorlu'),
(315, 'this', 'this', 'هذه', 'deze', 'ce', 'diese', 'этот', 'esta', 'bu'),
(316, 'reply', 'Reply', 'الرد', 'Antwoord', 'Répondre', 'Antworten', 'Ответить', 'Respuesta', 'cevap'),
(317, 'write_your_reply_here.', 'Write your reply here.', 'اكتب ردك هنا.', 'Schrijf hier uw antwoord.', 'Écrivez votre réponse ici.', 'Schreiben Sie Ihre Antwort hier.', 'Напишите свой ответ здесь.', 'Escribe tu respuesta aquí.', 'Cevabınızı buraya yazın.'),
(318, 'night_mode', 'Night mode', 'وضع الليل', 'Nachtstand', 'Mode nuit', 'Nacht-Modus', 'Ночной режим', 'Modo nocturno', 'Gece modu'),
(319, 'day_mode', 'Day mode', 'وضع اليوم', 'Dagmodus', 'Mode jour', 'Tagesmodus', 'Дневной режим', 'Modo día', 'Gün modu'),
(323, 'hide', 'hide', 'إخفاء', 'verbergen', 'cacher', 'verbergen', 'скрывать', 'esconder', 'saklamak'),
(324, 'spend', 'Spend', 'أنفق', 'Besteden', 'Dépenser', 'Verbringen', 'Проводить', 'Gastar', 'harcamak'),
(325, 'promote_question', 'Promote question', 'تعزيز السؤال', 'Vraag promoten', 'Promouvoir la question', 'Frage fördern', 'Продвигать вопрос', 'Promover pregunta', 'Soruyu yükseltin'),
(326, 'promote', 'Promote', 'ترويج', 'Promoten', 'Promouvoir', 'Fördern', 'содействовать', 'Promover', 'Desteklemek'),
(329, 'your_balance_is__', 'Your balance is', 'رصيدك هو', 'Je saldo is', 'Votre solde est', 'Ihr Gleichgewicht ist', 'Ваш баланс', 'Su balance es', 'Bakiyeniz'),
(330, 'your_question_will_be_promoted_for_24_hours_with', 'Your question will be promoted for 24 hours for', 'سيتم الترويج لسؤالك لمدة 24 ساعة', 'Uw vraag zal voor 24 uur worden gepromoot', 'Votre question sera promue pendant 24 heures pour', 'Ihre Frage wird 24 Stunden lang befördert', 'Ваш вопрос будет продвигаться в течение 24 часов для', 'Su pregunta será promovida por 24 horas para', 'Sorunuz 24 saat boyunca tanıtılacak'),
(331, 'unpromote', 'UnPromote', 'إلغاء الترويج', 'UnPromote', 'Annuler la promotion', 'UnPromote', 'UnPromote', 'No promover', 'UnPromote'),
(332, 'question_promoted_successfully', 'The question promoted  successfully', 'تم الترويج للسؤال بنجاح', 'De vraag is met succes gepromoot', 'La question promue avec succès', 'Die Frage wurde erfolgreich gefördert', 'Вопрос успешно продвигается', 'La pregunta promovida con éxito.', 'Soru başarıyla desteklendi'),
(333, 'promoted_for_24_hours', 'Promoted for 24 hours', 'رقيت لمدة 24 ساعة', '24 uur gepromoot', 'Promu pendant 24 heures', 'Befördert für 24 Stunden', 'Повышен в течение 24 часов', 'Promocionado por 24 horas.', '24 saat terfi'),
(334, 'question_unpromoted_successfully', 'The question unpromoted  successfully', 'السؤال unpromoted بنجاح', 'De vraag is niet succesvol gesteund', 'La question mal choisie', 'Die Frage wurde erfolgreich befürwortet', 'Вопрос не продвигается успешно', 'La pregunta no promueve exitosamente.', 'Soru başarılı bir şekilde yanıtlanmadı'),
(335, 'pay_using_paypal', 'Pay using PayPal', 'ادفع باستخدام PayPal', 'Betaal met PayPal', 'Payer avec PayPal', 'Zahlen Sie mit PayPal', 'Оплатить с помощью PayPal', 'Pagar usando paypal', 'PayPal ile ödeme yap'),
(336, 'vote', 'Vote', 'تصويت', 'Stemmen', 'Vote', 'Abstimmung', 'Голос', 'Votar', 'Oy'),
(337, 'question_voted_successfully', 'Question voted successfully', 'تم التصويت بنجاح', 'Vraag met succes goedgekeurd', 'Question votée avec succès', 'Frage erfolgreich gewählt', 'Вопрос проголосовал успешно', 'Pregunta votada con éxito', 'Soru başarıyla oy kullandı'),
(338, 'your_balance_is', 'Your balance is', 'رصيدك هو', 'Je saldo is', 'Votre solde est', 'Ihr Gleichgewicht ist', 'Ваш баланс', 'Su balance es', 'Bakiyeniz'),
(339, 'your_question_will_be_promoted_for_24_hours_for', 'Your question will be promoted for 24 hours for', 'سيتم الترويج لسؤالك لمدة 24 ساعة', 'Uw vraag zal voor 24 uur worden gepromoot', 'Votre question sera promue pendant 24 heures pour', 'Ihre Frage wird 24 Stunden lang befördert', 'Ваш вопрос будет продвигаться в течение 24 часов для', 'Su pregunta será promovida por 24 horas para', 'Sorunuz 24 saat boyunca tanıtılacak'),
(340, 'like', 'Like', 'مثل', 'Net zoals', 'Comme', 'Mögen', 'подобно', 'Me gusta', 'Sevmek'),
(341, 'liked', 'Liked', 'احب', 'vond', 'Aimé', 'Gefallen', 'Понравилось', 'Gustó', 'sevilen'),
(342, 'votes', 'Votes', 'الأصوات', 'stemmen', 'Votes', 'Abstimmungen', 'Голосов', 'Votos', 'oy'),
(343, 'your_question_was_successfully_posted', 'Your question was successfully posted', 'تم نشر سؤالك بنجاح', 'Uw vraag is succesvol geplaatst', 'Votre question a été postée avec succès', 'Ihre Frage wurde erfolgreich veröffentlicht', 'Ваш вопрос был успешно отправлен', 'Tu pregunta fue publicada con éxito.', 'Sorunuz başarıyla gönderildi'),
(344, 'your_report_was_successfully_posted', 'Your reported was successfully posted', 'تم نشر تقريرك بنجاح', 'Uw melding is succesvol geplaatst', 'Votre rapport a été posté avec succès', 'Ihre Meldung wurde erfolgreich veröffentlicht', 'Ваше сообщение было успешно опубликовано', 'Su informe fue publicado con éxito', 'Raporunuz başarıyla gönderildi'),
(345, 'your_answer_was_successfully_posted', 'Your answer was successfully posted', 'تم نشر إجابتك بنجاح', 'Uw antwoord is succesvol geplaatst', 'Votre réponse a été postée avec succès', 'Ihre Antwort wurde erfolgreich veröffentlicht', 'Ваш ответ был успешно опубликован', 'Tu respuesta fue publicada con éxito.', 'Cevabınız başarıyla gönderildi'),
(346, 'your_post_is_now_being_promoted', 'Your post is now being promoted', 'يتم الآن ترقية مشاركتك', 'Je bericht wordt nu gepromoveerd', 'Votre message est maintenant promu', 'Ihr Beitrag wird jetzt beworben', 'Ваш пост сейчас продвигается', 'Tu publicación ahora está siendo promovida.', 'Yayınınız şimdi tanıtılıyor'),
(347, 'character_limit_exceeded', 'Post character limit exceeded, allowed {{num}} characters', 'تم تجاوز حد عدد الأحرف المسموح به ، سمحت لعدد {{num}} من الأحرف', 'Limiet berichtteken overschreden, toegestane {{num}} tekens', 'Post limite de caractères dépassée, {{num}} caractères autorisés', 'Postzeichenlimit überschritten, erlaubte {{num}} Zeichen', 'Превышено ограничение на количество символов в сообщении', 'Se excedió el límite de caracteres de la publicación, caracteres {{num}} permitidos', 'Yayınlanan karakter sınırı aşıldı, {{num}} karaktere izin verildi'),
(348, 'limit_exceeded', 'Limit exceeded, please try again later.', 'تم تجاوز الحد ، يرجى المحاولة مرة أخرى في وقت لاحق.', 'Limiet overschreden, probeer het later opnieuw.', 'Limite dépassée, veuillez réessayer ultérieurement.', 'Limit überschritten, bitte versuchen Sie es später erneut.', 'Превышен лимит, повторите попытку позже.', 'Límite excedido, por favor intente de nuevo más tarde.', 'Sınır aşıldı, lütfen daha sonra tekrar deneyin.'),
(349, 'post_limit_exceeded', 'Post Limit exceeded, please try again later.', 'تم تجاوز حد النشر ، يرجى المحاولة مرة أخرى في وقت لاحق.', 'Postlimiet overschreden, probeer het later opnieuw.', 'Post Limit dépassé, veuillez réessayer ultérieurement.', 'Post Limit überschritten, bitte versuchen Sie es später erneut.', 'Превышен лимит сообщений, повторите попытку позже.', 'Se excedió el límite de publicación, inténtalo de nuevo más tarde.', 'Gönderi Sınırı aşıldı, lütfen daha sonra tekrar deneyin.'),
(350, 'answered_an_anonymous_question.', 'answered an anonymous question.', 'أجاب على سؤال مجهول.', 'antwoordde een anonieme vraag.', 'a répondu à une question anonyme.', 'beantwortete eine anonyme Frage.', 'ответил на анонимный вопрос.', 'Respondió una pregunta anónima.', 'isimsiz bir soruyu cevapladı.'),
(351, 'near_by_questions', 'Near by questions', 'بالقرب من الأسئلة', 'Dichtbij door vragen', 'Questions à proximité', 'In der Nähe von Fragen', 'Рядом с вопросами', 'Cerca de preguntas', 'Soruların yanında'),
(352, 'haven___t_posted_any_questions_yet', 'haven’t posted any questions yet', 'لم تنشر أي أسئلة حتى الآن', 'heb nog geen vragen geplaatst', 'n\'a pas encore posté de questions', 'hat noch keine Fragen gestellt', 'еще не отвечал ни на какие вопросы', 'no ha publicado ninguna pregunta todavía', 'henüz bir soru göndermediniz'),
(353, 'there_is_no_near_by_questions_yet', 'There is no near by questions yet', 'لا يوجد بالقرب من الأسئلة حتى الآن', 'Er zijn nog geen vragen in de buurt', 'Il n\'y a pas encore de question à proximité', 'Es gibt noch keine nahen Fragen', 'Еще нет вопросов рядом', 'No hay preguntas cercanas todavía.', 'Henüz soruların yaklaştığı bir şey yok.'),
(354, 'there_is_no_trending_questions_yet', 'There is no trending questions yet', 'لا توجد أسئلة شائعة حتى الآن', 'Er zijn nog geen trending-vragen', 'Il n\'y a pas encore de questions sur les tendances', 'Es gibt noch keine aktuellen Fragen', 'Там нет трендовых вопросов еще', 'Aún no hay preguntas de tendencias.', 'Henüz bir trend soru yok'),
(355, 'likes', 'likes', 'الإعجابات', 'sympathieën', 'aime', 'Likes', 'нравится', 'gustos', 'seviyor'),
(356, 'please_login_to_ask__answer__share__like__and_vote.', 'Please login to ask, answer, share, like, and vote.', 'الرجاء تسجيل الدخول للسؤال والإجابة والمشاركة والإبداء والتصويت.', 'Meld u aan om te vragen, te beantwoorden, te delen, leuk te vinden en te stemmen.', 'Veuillez vous connecter pour demander, répondre, partager, aimer et voter.', 'Bitte loggen Sie sich ein, um zu fragen, zu antworten, zu teilen, zu wählen und abzustimmen.', 'Пожалуйста, войдите, чтобы спрашивать, отвечать, делиться, ставить лайки и голосовать.', 'Por favor inicie sesión para preguntar, responder, compartir, \"me gusta\" y votar.', 'Sormak, cevaplamak, paylaşmak, beğenmek ve oy vermek için lütfen giriş yapın.'),
(357, 'created_a_new_poll.', 'created a new poll.', 'إنشاء استطلاع جديد.', 'heeft een nieuwe peiling gemaakt.', 'créé un nouveau sondage.', 'eine neue Umfrage erstellt.', 'создал новый опрос.', 'creó una nueva encuesta.', 'yeni bir anket oluşturdu.'),
(358, 'ago', 'ago', 'منذ', 'geleden', 'depuis', 'vor', 'тому назад', 'hace', 'önce'),
(359, 'from_now', 'from now', 'من الان', 'vanaf nu', 'à partir de maintenant', 'in', 'отныне', 'desde ahora', 'şu andan itibaren'),
(360, 'any_moment_now', 'any moment now', 'في اي لحظة الان', 'elk moment nu', 'n\'importe quel moment maintenant', 'jeden Moment jetzt', 'в любой момент', 'cualquier momento ahora', 'her an şimdi'),
(361, 'just_now', 'Just now', 'في هذة اللحظة', 'Net nu', 'Juste maintenant', 'Gerade jetzt', 'Прямо сейчас', 'Justo ahora', 'Şu anda'),
(362, 'about_a_minute_ago', 'about a minute ago', 'منذ دقيقة واحدة', 'ongeveer een minuut geleden', 'Il y a environ une minute', 'vor ungefähr einer Minute', 'около минуты назад', 'hace alrededor de un minuto', 'yaklaşık bir dakika önce'),
(363, '_d_minutes_ago', '%d minutes ago', 'قبل %d دقيقة', '%d minuten geleden', 'il y a %d minutes', 'vor %d Minuten', '%d минут назад', 'Hace %d minutos', '%d dakika önce'),
(364, 'about_an_hour_ago', 'about an hour ago', 'منذ ساعة تقريبا', 'ongeveer een uur geleden', 'il y a à peu près une heure', 'vor ungefähr einer Stunde', 'около часа назад', 'Hace aproximadamente una hora', 'yaklaşık bir saat önce'),
(365, '_d_hours_ago', '%d hours ago', 'قبل %d ساعة', '%d uur geleden', 'il y a %d heures', 'Vor %d Stunden', '%d часов назад', 'Hace %d horas', '%d saat önce'),
(366, 'a_day_ago', 'a day ago', 'قبل يوم', 'een dag geleden', 'il y a un jour', 'vor einem Tag', 'день назад', 'Hace un día', 'bir gün önce'),
(367, '_d_days_ago', '%d days ago', 'قبل %d يومًا', '%d dagen geleden', 'il y a %d jours', 'vor %d Tagen', '%d дней назад', 'Hace %d días', '%d gün önce'),
(368, 'about_a_month_ago', 'about a month ago', 'منذ شهر تقريبا', 'ongeveer een maand geleden', 'il y a environ un mois', 'vor ungefähr einem Monat', 'Около месяца назад', 'Hace más o menos un mes', 'yaklaşık bir ay önce'),
(369, '_d_months_ago', '%d months ago', 'قبل %d شهر', '%d maanden geleden', 'il y a %d mois', 'vor %d Monaten', '%d месяцев назад', 'Hace %d meses', '%d ay önce'),
(370, 'about_a_year_ago', 'about a year ago', 'قبل نحو سنة', 'ongeveer een jaar geleden', 'il y a un an à peu près', 'vor etwa einem Jahr', 'около года назад', 'Hace un año', 'yaklaşık bir yıl önce'),
(371, '_d_years_ago', '%d years ago', 'قبل %d سنة', '%d jaar geleden', 'il y a %d années', 'Vor %d Jahren', '%d лет назад', 'hace %d años', '%d yıl önce'),
(372, 'website_url', 'website Url', 'رابط الموقع', 'Website URL', 'URL de site web', 'Webadresse', 'ссылка на сайт', 'URL del sitio web', 'Web Sitesi URL\'si'),
(373, 'timeline', 'timeline', 'الجدول الزمني', 'tijdlijn', 'chronologie', 'Zeitleiste', 'график', 'línea de tiempo', 'zaman çizelgesi'),
(374, 'vs', 'vs', 'ضد', 'vs', 'contre', 'vs', 'против', 'vs', 'vs'),
(375, '__user_gender', '{{USER}} gender', '{{USER}} الجنس', '{{USER}} geslacht', '{{USER}} genre', '{{USER}} Geschlecht', '{{USER}} пол', '{{USUARIO}} género', '{{USER}} cinsiyet'),
(376, 'reply_updated_successfully', 'Reply updated successfully', 'تم تحديث الرد بنجاح', 'Antwoord is succesvol bijgewerkt', 'Réponse mise à jour avec succès', 'Antwort erfolgreich aktualisiert', 'Ответ успешно обновлен', 'Respuesta actualizada exitosamente', 'Yanıt başarıyla güncellendi'),
(377, 'answer_updated_successfully', 'Answer updated successfully', 'تم تحديث الرد بنجاح', 'Antwoord is succesvol bijgewerkt', 'Réponse mise à jour avec succès', 'Antwort erfolgreich aktualisiert', 'Ответ успешно обновлен', 'Respuesta actualizada con éxito', 'Yanıt başarıyla güncellendi'),
(378, 'messages', 'Messages', 'الرسائل', 'Berichten', 'messages', 'Mitteilungen', 'Сообщения', 'Mensajes', 'Mesajlar'),
(379, 'wowonder', 'WoWonder', 'رائع', 'WoWonder', 'WoWonder', 'WoWonder', 'WoWonder', 'WoWonder', 'WoWonder'),
(380, 'vkontakte', 'VKontakte', 'فكونتاكتي', 'VKontakte', 'VKontakte', 'VKontakte', 'В Контакте', 'VKontakte', 'VKontakte'),
(381, 'select_gender', 'Select gender', 'حدد نوع الجنس', 'Select Gender', 'Sélectionnez le sexe', 'Wähle Geschlecht', 'Выберите пол', 'Seleccione género', 'Cinsiyet seç'),
(382, 'select', 'Select', 'تحديد', 'Selecteer', 'Sélectionner', 'Wählen', 'Выбрать', 'Seleccione', 'seçmek'),
(383, 'select__gender', 'Select gender', 'حدد نوع الجنس', 'Selecteer geslacht', 'Sélectionnez le sexe', 'Wähle Geschlecht', 'Выберите пол', 'Seleccione género', 'Cinsiyet seç'),
(384, 'paypal', 'PayPal', 'باي بال', '', 'Pay Pal', 'PayPal', 'PayPal', 'PayPal', 'PayPal'),
(385, 'bank_transfer', 'Bank Transfer', 'التحويل المصرفي', 'Overschrijving', 'Virement', 'Banküberweisung', 'Банковский перевод', 'Transferencia bancaria', 'Banka transferi'),
(386, 'credit_card', 'Credit Card', 'بطاقة ائتمان', 'Kredietkaart', 'Carte de crédit', 'Kreditkarte', 'Кредитная карта', 'Tarjeta de crédito', 'Kredi kartı'),
(387, 'name', 'Name', 'اسم', 'Naam', 'Nom', 'Name', 'название', 'Nombre', 'ad'),
(388, 'card_number', 'Card Number', 'رقم البطاقة', 'Kaartnummer', 'Numéro de carte', 'Kartennummer', 'Номер карты', 'Número de tarjeta', 'Kart numarası'),
(389, 'pay', 'Pay', 'دفع', 'Betalen', 'Payer', 'Zahlen', 'Оплатить', 'Pagar', 'Ödemek'),
(390, 'upload', 'Upload', '', 'Uploaden', 'Télécharger', 'Hochladen', 'Загрузить', 'Subir', 'Yükleme'),
(391, 'browse_to_upload', 'Browse to upload', 'تصفح للتحميل', '', 'Parcourir pour télécharger', 'Zum Hochladen navigieren', 'Просмотрите, чтобы загрузить', 'Navegar para subir', 'Yüklemek için göz atın'),
(392, 'replenish', 'Replenish', 'جدد', 'Bijvullen', 'Remplir', 'Auffüllen', 'Восполнение', 'Reponer', 'Yenileyici'),
(393, 'amount', 'Amount', '', 'Bedrag', 'Montant', 'Menge', 'Количество', 'Cantidad', 'Miktar'),
(394, 'confirmation', 'Confirmation', 'التأكيد', 'Bevestiging', 'Confirmation', 'Bestätigung', 'подтверждение', 'Confirmación', 'Onayla'),
(395, 'deleted', 'Deleted', 'تم الحذف', 'Verwijderd', 'Supprimé', 'Gelöscht', 'Исключен', 'Eliminado', 'silindi'),
(396, 'currency', 'Currency', 'عملة', 'Valuta', 'Devise', 'Währung', 'валюта', 'Moneda', 'Para birimi'),
(397, 'rent', 'Rent', '', '', 'Location', 'Miete', 'Арендная плата', 'Alquilar', 'Kira'),
(398, 'subscribe', 'Subscribe', '', 'Abonneren', 'Souscrire', 'Abonnieren', 'Подписывайся', 'Suscribir', 'Abone ol'),
(399, 'choose_payment_method', 'Choose Payment Method', 'اختر وسيلة الدفع', 'Kies betalingsmethode', 'Choisissez le mode de paiement', 'Zahlungsart auswählen', 'Выберите способ оплаты', 'Elige el método de pago', 'Ödeme yöntemini seçin'),
(400, 'error', 'Error', 'خطأ', 'Fout', 'Erreur', 'Error', 'ошибка', 'Error', 'Hata'),
(401, 'checkout_text', 'checkout text', 'نص الخروج', 'afreken tekst', 'texte de paiement', 'Checkout-Text', 'текст оформления заказа', 'texto de pago', 'ödeme metni'),
(402, 'address', 'address', 'عنوان', 'adres', '', 'Adresse', 'адрес', 'habla a', 'adres'),
(403, 'city', 'city', 'مدينة', 'stad', '', 'Stadt', 'город', 'ciudad', 'Kent'),
(404, 'state', 'state', 'حالة', 'staat', 'Etat', 'Zustand', 'штат', 'estado', 'durum'),
(405, 'zip', 'zip', '', 'zip', 'Zip *: français', 'Postleitzahl', 'застежка-молния', 'Código Postal', 'zip'),
(406, 'phone_number', 'Phone Number', 'رقم الهاتف', 'Telefoonnummer', 'Numéro de téléphone', 'Telefonnummer', 'Телефонный номер', 'Número de teléfono', 'Telefon numarası'),
(407, 'no', 'No', '', 'Nee', 'Non', 'Nein', 'нет', 'No', 'Hayır'),
(408, 'yes', 'Yes', 'نعم', 'Ja', 'Oui', 'Ja', 'да', 'si', 'Evet'),
(409, 'no_messages_were_found__please_choose_a_channel_to_chat.', 'No messages were found, please choose a user to chat.', 'لم يتم العثور على رسائل ، يرجى اختيار مستخدم للدردشة.', 'Er zijn geen berichten gevonden. Kies een gebruiker om te chatten.', 'Aucun message n\'a été trouvé, veuillez choisir un utilisateur pour discuter.', 'Es wurden keine Nachrichten gefunden. Bitte wählen Sie einen Benutzer zum Chatten aus.', 'Сообщения не найдены, выберите пользователя для чата.', 'No se encontraron mensajes, elija un usuario para chatear.', 'Mesaj bulunamadı, lütfen sohbet etmek için bir kullanıcı seçin.'),
(410, 'no_users_found', 'No users found', 'لم يتم العثور على مستخدمين', 'Geen gebruikers gevonden', '', 'Keine Benutzer gefunden', 'Пользователи не найдены', 'No se encontraron usuarios', 'Kullanıcı bulunamadı'),
(411, 'chat', 'Chat', 'دردشة', 'Chat', 'Bavarder', 'Plaudern', 'Чат', 'Charla', 'Sohbet'),
(412, 'load_more_messages', 'Load more messages', 'تحميل المزيد من الرسائل', 'Laad meer berichten', 'Charger plus de messages', 'Laden Sie weitere Nachrichten', 'Загрузить больше сообщений', 'Cargar más mensajes', 'Daha fazla mesaj yükle'),
(413, 'write_message', 'Write message', 'اكتب رسالة', 'Schrijf een bericht', 'Écrire un message', 'Nachricht schreiben', 'Напиши сообщение', 'Escribe un mensaje', 'Mesaj Yaz'),
(414, 'are_you_sure_you_want_delete_chat', 'Are you sure you want to delete this chat?', 'هل أنت متأكد أنك تريد حذف هذه الدردشة؟', 'Weet u zeker dat u deze chat wilt verwijderen?', 'Voulez-vous vraiment supprimer ce chat?', 'Möchten Sie diesen Chat wirklich löschen?', 'Вы уверены, что хотите удалить этот чат?', '¿Estás seguro de que deseas eliminar este chat?', 'Bu sohbeti silmek istediğinizden emin misiniz?'),
(415, 'no_messages_were_found__say_hi_', 'No messages were found, say Hi!', 'لم يتم العثور على رسائل ، قل مرحباً!', '', 'Aucun message n\'a été trouvé, dites Salut!', 'Es wurden keine Nachrichten gefunden, sagen Sie Hallo!', 'Сообщений не найдено. Передайте привет!', 'No se encontraron mensajes, di ¡Hola!', 'Mesaj bulunamadı, Merhaba deyin!'),
(416, 'please_check_the_details', 'Please check the details', 'يرجى التحقق من التفاصيل', 'Controleer de details', 'Veuillez vérifier les détails', 'Bitte überprüfen Sie die Details', 'Пожалуйста, проверьте детали', 'Por favor revise los detalles', 'Lütfen ayrıntıları kontrol edin'),
(417, 'confirming_your_payment__please_wait..', 'Confirming your payment, please wait..', 'لتأكيد الدفع ، يرجى الانتظار ..', 'Uw betaling wordt bevestigd, even geduld ...', 'Confirmation de votre paiement, veuillez patienter.', 'Bestätigen Sie Ihre Zahlung, bitte warten Sie ..', 'Подтверждение платежа, подождите ..', 'Confirmando su pago, espere ...', 'Ödemenizi onaylıyoruz, lütfen bekleyin ..'),
(418, 'payment_declined__please_try_again_later.', 'Payment declined, please try again later.', 'تم رفض الدفع ، يرجى المحاولة مرة أخرى في وقت لاحق.', 'Betaling geweigerd. Probeer het later opnieuw.', 'Paiement refusé, veuillez réessayer plus tard.', 'Zahlung abgelehnt, bitte versuchen Sie es später erneut.', 'Платеж отклонен, повторите попытку позже.', 'Pago rechazado. Vuelva a intentarlo más tarde.', 'Ödeme reddedildi, lütfen daha sonra tekrar deneyin.'),
(419, 'verification', 'Verification', 'التحقق', 'Verificatie', 'Vérification', 'Überprüfung', 'верификация', 'Verificación', 'Doğrulama'),
(420, 'upload_id', 'Upload ID', '', 'Upload-ID', '', 'ID hochladen', 'ID загрузки', 'Cargar ID', 'ID yükle'),
(421, 'select_id', 'Select ID', 'حدد معرف', 'Selecteer ID', 'Sélectionnez ID', 'ID auswählen', 'Выберите ID', 'Seleccionar ID', 'Kimlik seçin'),
(422, 'message', 'Message', 'رسالة', 'Bericht', 'Message', 'Botschaft', 'Сообщение', 'Mensaje', 'İleti'),
(423, 'submit_request', 'Submit Request', 'تقديم الطلب', 'Aanvraag indienen', 'Envoyer la demande', 'Anfrage einreichen', 'Отправить запрос', 'Enviar peticion', 'İstek gönderin'),
(424, 'file_is_too_big', 'The file is too big, please try another one.', 'الملف كبير جدًا ، يرجى تجربة ملف آخر.', 'Het bestand is te groot, probeer een ander bestand.', 'Le fichier est trop volumineux, veuillez en essayer un autre.', 'Die Datei ist zu groß, bitte versuchen Sie es mit einer anderen.', 'Файл слишком большой, попробуйте другой.', 'El archivo es demasiado grande, pruebe con otro.', 'Dosya çok büyük, lütfen başka bir tane deneyin.'),
(425, 'u_are_verified', 'you are now verified.', 'تم التحقق منك الآن.', 'je bent nu geverifieerd.', 'vous êtes maintenant vérifié.', 'Sie sind jetzt verifiziert.', 'теперь вы проверены.', 'ahora está verificado.', 'şimdi doğrulandınız.'),
(426, 'verif_request_received', 'Verification request received.', '', 'Verificatieverzoek ontvangen.', 'Demande de vérification reçue.', 'Bestätigungsanfrage erhalten.', 'Запрос на подтверждение получен.', 'Se recibió la solicitud de verificación.', 'Doğrulama talebi alındı.'),
(427, 'inactive', 'inactive', 'غير نشط', 'inactief', '', 'inaktiv', 'неактивный', 'inactivo', 'pasif'),
(428, 'pro_mbr', 'Pro Member', 'عضو محترف', 'Pro-lid', '', 'Pro Mitglied', 'Член профи', 'Miembro Pro', 'Pro Üye'),
(429, 'free_mbr', 'Free Member', 'عضو مجاني', '', 'Membre gratuit', 'Freies Mitglied', 'Бесплатный член', 'miembro gratuito', 'Ücretsiz Üye'),
(430, 'type', 'Type', 'نوع', '', 'Type', 'Art', 'Тип', 'Tipo', 'tip'),
(431, 'user', 'User', '', 'Gebruiker', 'Utilisateur', 'Benutzer', 'пользователь', 'Usuario', 'kullanıcı'),
(432, 'admin', 'Admin', 'مشرف', 'beheerder', 'Administrateur', 'Administrator', 'Администратор', 'Administración', 'yönetim'),
(433, 'verified', 'Verified', 'تم التحقق', 'Geverifieerd', 'Vérifié', 'Verifiziert', 'Проверенный', 'Verificado', 'Doğrulanmış'),
(434, 'not_verified', 'Not verified', 'لم يتم التحقق منها', 'niet geverifieerd', 'non vérifié', 'Nicht verifiziert', 'не подтверждено', 'No verificado', 'Doğrulanmadı'),
(435, 'user_upload_limit', 'User Upload Limit', 'حد تحميل المستخدم', 'Limiet gebruikersupload', 'Limite de téléchargement utilisateur', 'Benutzer-Upload-Limit', 'Лимит загрузки пользователей', 'Límite de carga del usuario', 'Kullanıcı Yükleme Sınırı'),
(436, 'check_out_text', 'Check Out Text', 'سحب النص', 'Bekijk tekst', 'Extraire le texte', 'Text auschecken', 'Проверить текст', 'Ver texto', 'Metni Kontrol Et'),
(437, 'chose_payment_method', 'Chose Payment Method', 'اختر وسيلة الدفع', 'Kies betalingsmethode', 'Choisissez le mode de paiement', 'Zahlungsart auswählen', 'Выберите способ оплаты', 'Elige el método de pago', 'Ödeme yöntemini seçin'),
(438, 'are_you_sure_you_want_to_delete_the_chat', 'Are you sure you want to delete this chat?', 'هل أنت متأكد أنك تريد حذف هذه الدردشة؟', 'Weet u zeker dat u deze chat wilt verwijderen?', 'Voulez-vous vraiment supprimer ce chat?', 'Möchten Sie diesen Chat wirklich löschen?', 'Вы уверены, что хотите удалить этот чат?', '¿Estás seguro de que deseas eliminar este chat?', 'Bu sohbeti silmek istediğinizden emin misiniz?'),
(439, 'verif_sent', 'Your verification request has been sent.', 'تم إرسال طلب التحقق الخاص بك.', 'Uw verificatieverzoek is verzonden.', 'Votre demande de vérification a été envoyée.', 'Ihre Bestätigungsanfrage wurde gesendet.', 'Ваш запрос на подтверждение отправлен.', 'Su solicitud de verificación ha sido enviada.', 'Doğrulama talebiniz gönderildi.'),
(440, 'upload_your_id', 'Upload Your ID', 'قم بتحميل المعرف الخاص بك', 'Upload uw ID', 'Téléchargez votre identifiant', 'Laden Sie Ihre ID hoch', 'Загрузите свой идентификатор', 'Cargue su ID', 'Kimliğinizi Yükleyin'),
(441, 'select_file', 'Select File', 'حدد ملف', 'Selecteer bestand', 'Choisir le dossier', 'Datei aussuchen', 'Выбрать файл', 'Seleccione Archivo', 'Dosya Seç'),
(442, 'submit_your_request', 'Submit Your Request', 'أرسل طلبك', 'Dien uw verzoek in', 'Soumettez votre demande', 'Senden Sie Ihre Anfrage', 'Отправьте свой запрос', 'Envíe su solicitud', 'Talebinizi Gönderin'),
(443, 'verification_request_is_still_pending', 'Your Verification Request Is Still Pending', 'طلب التحقق الخاص بك لا يزال معلقًا', 'Uw verificatieverzoek is nog in behandeling', 'Votre demande de vérification est toujours en attente', 'Ihre Bestätigungsanfrage steht noch aus', 'Ваш запрос на подтверждение еще не принят', 'Su solicitud de verificación aún está pendiente', 'Doğrulama İsteğiniz Hala Beklemede'),
(444, 'bank_payment_request_sent', 'Your bank payment request has been sent.', 'تم إرسال طلب الدفع المصرفي الخاص بك.', 'Uw betalingsverzoek is verzonden.', 'Votre demande de paiement bancaire a été envoyée.', 'Ihre Bankzahlungsanforderung wurde gesendet.', 'Ваш запрос на банковский платеж отправлен.', 'Su solicitud de pago bancaria ha sido enviada.', 'Banka ödeme talebiniz gönderildi.'),
(445, 'select_your_id', 'Select Your ID', 'حدد المعرف الخاص بك', 'Selecteer uw ID', 'Sélectionnez votre identifiant', 'Wählen Sie Ihre ID', 'Выберите свой ID', 'Seleccione su ID', 'Kimliğinizi Seçin'),
(446, 'two-factor_authentication', 'Two-factor authentication', '', 'Twee-factor-authenticatie', 'Authentification à deux facteurs', 'Zwei-Faktor-Authentifizierung', 'Двухфакторная аутентификация', 'Autenticación de dos factores', 'İki faktörlü kimlik doğrulama'),
(447, 'phone', 'Phone', 'هاتف', 'Telefoon', 'Téléphone', 'Telefon', 'Телефон', 'Teléfono', 'Telefon'),
(448, 'enable', 'Enable', 'ممكن', 'Inschakelen', 'Activer', 'Aktivieren', 'включить', 'Habilitar', 'etkinleştirme'),
(449, 'disable', 'Disable', 'تعطيل', '', '', 'Deaktivieren', 'Отключить', 'Inhabilitar', 'Devre Dışı'),
(450, 'phone_number_should_be_as_this_format___90..', 'Phone number should be as this format: +90..', '', 'Telefoonnummer moet de volgende notatie hebben: +90 ..', 'Le numéro de téléphone doit être au format suivant: +90 ..', 'Die Telefonnummer sollte das folgende Format haben: +90 ..', 'Номер телефона должен быть в следующем формате: +90 ..', 'El número de teléfono debe tener este formato: +90 ..', 'Telefon numarası şu biçimde olmalıdır: +90 ..'),
(451, 'settings_successfully_updated_', 'Settings successfully updated!', 'تم تحديث الإعدادات بنجاح!', '', '', 'Einstellungen erfolgreich aktualisiert!', 'Настройки успешно обновлены!', '¡Configuración actualizada correctamente!', 'Ayarlar başarıyla güncellendi!'),
(452, 'please_check_your_details.', 'Please check your details.', 'الرجاء مراجعة التفاصيل الخاصة بك.', 'Check alsjeblieft je gegevens.', 'S\'il vous plaît vérifier vos informations.', 'Bitte überprüfe deine Details.', 'Пожалуйста, проверьте свои данные.', 'Por favor comprueba tus detalles.', 'Lütfen bilgilerinizi kontrol edin.'),
(453, 'something_went_wrong__please_try_again_later.', 'Something went wrong, please try again later.', '', 'Er is iets misgegaan, probeer het later opnieuw.', '', 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.', 'Что-то пошло не так. Пожалуйста, повторите попытку позже.', 'Se produjo un error. Vuelve a intentarlo más tarde.', 'Bir şeyler yanlış oldu. Lütfen sonra tekrar deneyiniz.'),
(454, 'we_have_sent_you_an_email_with_the_confirmation_code.', 'We have sent you an email with the confirmation code.', 'لقد أرسلنا لك بريدًا إلكترونيًا يحتوي على رمز التأكيد.', 'We hebben je een e-mail gestuurd met de bevestigingscode.', 'Nous vous avons envoyé un email avec le code de confirmation.', 'Wir haben Ihnen eine E-Mail mit dem Bestätigungscode gesendet.', 'Мы отправили вам электронное письмо с кодом подтверждения.', 'Le hemos enviado un correo electrónico con el código de confirmación.', 'Size onay kodunu içeren bir e-posta gönderdik.'),
(455, 'a_confirmation_message_was_sent.', 'A confirmation message was sent.', 'تم إرسال رسالة تأكيد.', 'Er is een bevestigingsbericht verzonden.', '', 'Eine Bestätigungsnachricht wurde gesendet.', 'Было отправлено подтверждающее сообщение.', 'Se envió un mensaje de confirmación.', 'Bir onay mesajı gönderildi.'),
(456, 'we_have_sent_a_message_that_contains_the_confirmation_code_to_enable_two-factor_authentication.', 'We have sent a message that contains the confirmation code to enable Two-factor authentication.', 'لقد أرسلنا رسالة تحتوي على رمز التأكيد لتمكين المصادقة الثنائية.', 'We hebben een bericht gestuurd met de bevestigingscode om tweefactorauthenticatie mogelijk te maken.', 'Nous avons envoyé un message contenant le code de confirmation pour activer l\'authentification à deux facteurs.', 'Wir haben eine Nachricht gesendet, die den Bestätigungscode enthält, um die Zwei-Faktor-Authentifizierung zu aktivieren.', 'Мы отправили сообщение, содержащее код подтверждения для включения двухфакторной аутентификации.', 'Hemos enviado un mensaje que contiene el código de confirmación para habilitar la autenticación de dos factores.', 'İki faktörlü kimlik doğrulamayı etkinleştirmek için onay kodunu içeren bir mesaj gönderdik.'),
(457, 'confirmation_code', 'Confirmation code', 'تأكيد الكود', 'Bevestigingscode', 'Code de confirmation', 'Bestätigungscode', 'Код подтверждения', 'Código de confirmación', 'Onay kodu'),
(458, 'your_phone_number_has_been_successfully_verified.', 'Your phone number has been successfully verified.', 'تم التحقق من رقم هاتفك بنجاح.', 'Uw telefoonnummer is succesvol geverifieerd.', 'Votre numéro de téléphone a été vérifié avec succès.', 'Ihre Telefonnummer wurde erfolgreich überprüft.', 'Ваш номер телефона успешно подтвержден.', 'Su número de teléfono se ha verificado correctamente.', 'Telefon numaranız başarıyla doğrulandı.'),
(459, 'card_charged', 'Thank you, Your payment was successful.', 'شكرا لك ، دفعتك كانت ناجحة.', 'Bedankt. Uw betaling is gelukt.', 'Merci, votre paiement a été effectué avec succès.', 'Vielen Dank, Ihre Zahlung war erfolgreich.', 'Спасибо, ваш платеж прошел успешно.', 'Gracias, su pago se realizó correctamente.', 'Teşekkürler, ödemeniz başarılı oldu.'),
(460, 'custom_thumbnail', 'Custom Thumbnail', 'صورة مصغرة مخصصة', 'Aangepaste miniatuur', 'Miniature personnalisée', 'Benutzerdefinierte Miniaturansicht', 'Пользовательский эскиз', 'Miniatura personalizada', 'Özel Küçük Resim'),
(461, 'payment_declined', 'Payment declined', 'تم رفض الدفع', 'Betaling geweigerd', 'Paiement refusé', 'Zahlung abgelehnt', 'Платеж отклонен', 'Pago rechazado', 'Ödeme Reddedildi'),
(462, 'payment', 'Payment', 'دفع', 'Betaling', 'Paiement', 'Zahlung', 'Оплата', 'Pago', 'Ödeme'),
(463, 'payment_verification', 'Payment Verification', 'التحقق من الدفع', 'Betalingsverificatie', 'Vérification des paiements', 'Zahlungsüberprüfung', 'Подтверждение платежа', 'Verificación de pago', 'Ödeme Doğrulaması'),
(464, 'file_exceeds_upload_limit', 'File Exceeds Upload Limit', 'تجاوز الملف حد التحميل', 'Bestand overschrijdt uploadlimiet', 'Le fichier dépasse la limite de téléchargement', 'File Exceeds Upload Limit', 'Файл превышает лимит загрузки', 'El archivo supera el límite de carga', 'Dosya Yükleme Sınırını Aşıyor'),
(465, 'wallet_payment_recharge', 'Recharge Wallet', 'إعادة شحن المحفظة', 'Portemonnee opladen', 'Recharger le portefeuille', 'Recharge Wallet', 'Пополнить кошелек', 'Recargar billetera', 'Cüzdanı Şarj Et'),
(466, 'account_recharge', 'Account Recharge', 'إعادة شحن الحساب', 'Account opladen', 'Recharge de compte', 'Account Recharge', 'Пополнение счета', 'Recarga de cuenta', 'Hesap Yeniden Yükleme'),
(467, 'choose_a_payment_method.', 'Choose a payment method.', 'اختر طريقة الدفع.', 'Kies een betaal methode.', 'Choisissez une méthode de paiement.', 'Choose a payment method.', 'Выберите способ оплаты.', 'Elija un método de pago.', 'Bir ödeme yöntemi seçin.'),
(468, 'upload_receipt', 'Upload Receipt', 'تحميل الإيصال', 'Ontvangstbewijs uploaden', 'Télécharger le reçu', 'Upload Receipt', 'Квитанция о загрузке', 'Subir recibo', 'Makbuzu Yükle'),
(469, 'confirm', 'Confirm', 'تؤكد', 'Bevestigen', 'Confirmer', 'Confirm', 'Подтвердить', 'Confirmar', 'Onaylamak'),
(470, 'please_wait..', 'Please wait..', 'أرجو الإنتظار..', 'Even geduld aub..', 'S\'il vous plaît, attendez..', 'Please wait..', 'Пожалуйста, подождите..', 'Por favor espera..', 'Lütfen bekle..'),
(471, 'redirecting..', 'Redirecting..', 'إعادة توجيه..', 'Omleiden ...', 'Redirection ..', 'Redirecting..', 'Перенаправление ..', 'Redirigiendo ...', 'Yönlendiriliyor ..'),
(472, 'oops__an_error_found.', 'Oops, an error was found.', 'عفوًا ، تم العثور على خطأ.', 'Oeps, er is een fout gevonden.', 'Oups, une erreur a été trouvée.', 'Oops, an error was found.', 'К сожалению, обнаружена ошибка.', 'Vaya, se encontró un error.', 'Maalesef bir hata bulundu.');
INSERT INTO `langs` (`id`, `lang_key`, `english`, `arabic`, `dutch`, `french`, `german`, `russian`, `spanish`, `turkish`) VALUES
(473, 'verif_sentB', 'Your receipt has been uploaded successfully. We will get back to you soon.', 'تم تحميل إيصالك بنجاح. ', 'Uw bon is succesvol geüpload. ', 'Votre reçu a été téléchargé avec succès. ', 'Your receipt has been uploaded successfully. We will get back to you soon.', 'Ваша квитанция успешно загружена. ', 'Su recibo se ha subido correctamente. ', 'Makbuzunuz başarıyla yüklendi. '),
(474, 'error_found__please_try_again_later.', 'Error found, please try again later.', 'تم العثور على خطأ ، يرجى المحاولة مرة أخرى في وقت لاحق.', 'Fout gevonden, probeer het later opnieuw.', 'Erreur détectée, veuillez réessayer plus tard.', 'Error found, please try again later.', 'Обнаружена ошибка. Повторите попытку позже.', 'Se encontró un error. Vuelva a intentarlo más tarde.', 'Hata bulundu, lütfen daha sonra tekrar deneyin.'),
(475, 'return_back', 'Return back', 'رجوع', 'Terugkeren', 'Retour en arrière', 'Return back', 'Вернуться назад', 'Devolver', 'Geri dön');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL DEFAULT 0,
  `to_id` int(11) NOT NULL DEFAULT 0,
  `text` text NOT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `from_deleted` int(11) NOT NULL DEFAULT 0,
  `to_deleted` int(11) NOT NULL DEFAULT 0,
  `sent_push` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `notification_id` varchar(50) NOT NULL DEFAULT '',
  `type_two` varchar(32) NOT NULL DEFAULT '',
  `media` varchar(255) CHARACTER SET utf16 NOT NULL,
  `mediaFileName` varchar(200) CHARACTER SET utf16 NOT NULL,
  `mediaFileNames` varchar(200) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `notifier_id` int(11) NOT NULL DEFAULT 0,
  `recipient_id` int(11) NOT NULL DEFAULT 0,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `replay_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(20) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL,
  `url` varchar(3000) NOT NULL DEFAULT '',
  `seen` varchar(50) NOT NULL DEFAULT '0',
  `time` varchar(50) NOT NULL DEFAULT '0',
  `sent_push` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `amount` float NOT NULL DEFAULT 0,
  `type` varchar(15) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pro_plan` varchar(100) DEFAULT '',
  `info` varchar(100) DEFAULT '0',
  `via` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `ask_user_id` int(11) UNSIGNED DEFAULT 0,
  `ask_question_id` int(11) UNSIGNED DEFAULT 0,
  `shared_user_id` int(11) UNSIGNED DEFAULT 0,
  `shared_question_id` int(11) UNSIGNED DEFAULT 0,
  `replay_user_id` int(11) UNSIGNED DEFAULT 0,
  `replay_question_id` int(11) UNSIGNED DEFAULT 0,
  `is_anonymously` int(11) UNSIGNED DEFAULT 1,
  `question` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` mediumtext COLLATE utf8mb4_bin DEFAULT NULL,
  `type` varchar(50) COLLATE utf8mb4_bin DEFAULT 'question',
  `active` int(11) UNSIGNED DEFAULT 1,
  `public` int(11) UNSIGNED DEFAULT 1,
  `time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `promoted` int(11) UNSIGNED DEFAULT 0,
  `lat` varchar(200) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `lng` varchar(200) COLLATE utf8mb4_bin NOT NULL DEFAULT '0',
  `postLink` varchar(1000) COLLATE utf8mb4_bin DEFAULT NULL,
  `postLinkTitle` text COLLATE utf8mb4_bin DEFAULT NULL,
  `postLinkContent` varchar(400) COLLATE utf8mb4_bin DEFAULT NULL,
  `postLinkImage` varchar(1000) COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- --------------------------------------------------------

--
-- Table structure for table `questions_votes`
--

CREATE TABLE `questions_votes` (
  `id` int(11) UNSIGNED NOT NULL,
  `question_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `choice_id` varchar(100) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `vote_time` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `recent_search`
--

CREATE TABLE `recent_search` (
  `id` int(11) UNSIGNED NOT NULL,
  `keyword` varchar(255) DEFAULT '',
  `hits` int(11) UNSIGNED DEFAULT 0,
  `time` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `seen` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `platform` varchar(30) NOT NULL DEFAULT 'web',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `site_ads`
--

CREATE TABLE `site_ads` (
  `id` int(11) NOT NULL,
  `placement` varchar(50) NOT NULL DEFAULT '',
  `code` text DEFAULT NULL,
  `active` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `site_ads`
--

INSERT INTO `site_ads` (`id`, `placement`, `code`, `active`) VALUES
(1, 'header', '', 1),
(3, 'footer', '', 1),
(5, 'side_bar', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `type` varchar(400) NOT NULL,
  `text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `type`, `text`) VALUES
(1, 'terms', '&lt;h4&gt;1- Write your Terms Of Use here.&lt;/h4&gt;           &lt;br&gt;        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  &lt;br&gt;        &lt;br&gt;&lt;br&gt; &lt;br&gt;       &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;       Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(2, 'privacy-policy', '&lt;h4&gt;1- Write your Privacy Policy here.&lt;/h4&gt;           &lt;br&gt;       Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  &lt;br&gt;        &lt;br&gt;&lt;br&gt; &lt;br&gt;       &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;       Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'),
(3, 'about-us', '&lt;h4&gt;1- Write your About Us here.&lt;/h4&gt;           &lt;br&gt;       Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.  &lt;br&gt;        &lt;br&gt;&lt;br&gt; &lt;br&gt;       &lt;h4&gt;2- Random title&lt;/h4&gt; &lt;br&gt;       Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');

-- --------------------------------------------------------

--
-- Table structure for table `userads`
--

CREATE TABLE `userads` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(3000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `headline` varchar(200) NOT NULL DEFAULT '',
  `description` text DEFAULT NULL,
  `location` varchar(1000) NOT NULL DEFAULT 'us',
  `audience` longtext DEFAULT NULL,
  `ad_media` varchar(3000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `gender` varchar(15) CHARACTER SET utf8 COLLATE utf8_danish_ci NOT NULL DEFAULT 'all',
  `bidding` varchar(15) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `clicks` int(15) NOT NULL DEFAULT 0,
  `views` int(15) NOT NULL DEFAULT 0,
  `posted` varchar(15) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT 1,
  `appears` varchar(10) NOT NULL DEFAULT 'post',
  `user_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userads_data`
--

CREATE TABLE `userads_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `ad_id` int(11) NOT NULL DEFAULT 0,
  `clicks` int(15) NOT NULL DEFAULT 0,
  `views` int(15) NOT NULL DEFAULT 0,
  `spend` float UNSIGNED NOT NULL DEFAULT 0,
  `dt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `ip_address` varchar(150) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `first_name` varchar(50) NOT NULL DEFAULT '',
  `last_name` varchar(50) NOT NULL DEFAULT '',
  `gender` varchar(10) NOT NULL DEFAULT 'male',
  `email_code` varchar(50) NOT NULL DEFAULT '',
  `language` varchar(22) NOT NULL DEFAULT 'english',
  `avatar` varchar(100) NOT NULL DEFAULT 'upload/photos/d-avatar.jpg',
  `cover` varchar(100) NOT NULL DEFAULT 'upload/photos/d-cover.jpg',
  `country_id` int(11) NOT NULL DEFAULT 0,
  `about` text DEFAULT NULL,
  `location` varchar(250) DEFAULT '',
  `website` varchar(250) DEFAULT '',
  `google` varchar(50) NOT NULL DEFAULT '',
  `facebook` varchar(50) NOT NULL DEFAULT '',
  `twitter` varchar(50) NOT NULL DEFAULT '',
  `instagram` varchar(100) NOT NULL DEFAULT '',
  `active` int(11) NOT NULL DEFAULT 0,
  `admin` int(11) NOT NULL DEFAULT 0,
  `verified` int(11) NOT NULL DEFAULT 0,
  `last_active` int(11) NOT NULL DEFAULT 0,
  `last_follow_id` int(11) UNSIGNED DEFAULT 0,
  `registered` varchar(40) NOT NULL DEFAULT '0000/00',
  `startup` int(11) UNSIGNED DEFAULT 0,
  `birth_date` varchar(20) DEFAULT '',
  `notification_on_answered_question` int(11) UNSIGNED DEFAULT 1,
  `notification_on_visit_profile` int(11) UNSIGNED DEFAULT 1,
  `notification_on_like_question` int(11) UNSIGNED DEFAULT 1,
  `notification_on_shared_question` int(11) UNSIGNED DEFAULT 1,
  `wallet` varchar(20) NOT NULL DEFAULT '0.00',
  `src` varchar(50) NOT NULL DEFAULT 'site',
  `last_location_update` varchar(30) NOT NULL DEFAULT '0',
  `lat` varchar(200) NOT NULL DEFAULT '0',
  `lng` varchar(200) NOT NULL DEFAULT '0',
  `two_factor` int(11) NOT NULL DEFAULT 0,
  `new_email` varchar(255) DEFAULT NULL,
  `two_factor_verified` int(11) NOT NULL DEFAULT 0,
  `new_phone` varchar(32) DEFAULT NULL,
  `phone_number` varchar(32) DEFAULT NULL,
  `user_upload_limit` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

CREATE TABLE `verification_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(200) NOT NULL DEFAULT '',
  `message` text DEFAULT NULL,
  `media_file` text DEFAULT NULL,
  `time` varchar(100) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_invitations`
--
ALTER TABLE `admin_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`(255));

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `announcement_views`
--
ALTER TABLE `announcement_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `apps_sessions`
--
ALTER TABLE `apps_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `platform` (`platform`);

--
-- Indexes for table `bank_receipts`
--
ALTER TABLE `bank_receipts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banned`
--
ALTER TABLE `banned`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `value` (`value`(191));

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_pages`
--
ALTER TABLE `custom_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `follower_id` (`follower_id`);

--
-- Indexes for table `hashtags`
--
ALTER TABLE `hashtags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `last_trend_time` (`last_trend_time`),
  ADD KEY `trend_use_num` (`trend_use_num`),
  ADD KEY `tag` (`tag`(191)),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `invitation_links`
--
ALTER TABLE `invitation_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code` (`code`(255)),
  ADD KEY `invited_id` (`invited_id`),
  ADD KEY `time` (`time`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `keyword_search`
--
ALTER TABLE `keyword_search`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keyword` (`keyword`(191)),
  ADD KEY `hits` (`hits`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `langs`
--
ALTER TABLE `langs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang_key` (`lang_key`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `from_id` (`from_id`),
  ADD KEY `to_id` (`to_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `time` (`time`),
  ADD KEY `from_deleted` (`from_deleted`),
  ADD KEY `to_deleted` (`to_deleted`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `type` (`type`),
  ADD KEY `seen` (`seen`),
  ADD KEY `notifier_id` (`notifier_id`),
  ADD KEY `time` (`time`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `replay_id` (`replay_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ask_user_id` (`ask_user_id`),
  ADD KEY `active` (`active`),
  ADD KEY `ask_question_id` (`ask_question_id`),
  ADD KEY `shared_user_id` (`shared_user_id`),
  ADD KEY `shared_question_id` (`shared_question_id`),
  ADD KEY `replay_user_id` (`replay_user_id`),
  ADD KEY `replay_question_id` (`replay_question_id`),
  ADD KEY `type` (`type`),
  ADD KEY `public` (`public`),
  ADD KEY `time` (`time`),
  ADD KEY `lat` (`lat`),
  ADD KEY `lng` (`lng`),
  ADD KEY `promoted` (`promoted`);

--
-- Indexes for table `questions_votes`
--
ALTER TABLE `questions_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `choice_id` (`choice_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `recent_search`
--
ALTER TABLE `recent_search`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keyword` (`keyword`(191)),
  ADD KEY `hits` (`hits`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `question_id` (`question_id`) USING BTREE;

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `platform` (`platform`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `site_ads`
--
ALTER TABLE `site_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `placement` (`placement`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userads`
--
ALTER TABLE `userads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appears` (`appears`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `location` (`location`(255)),
  ADD KEY `gender` (`gender`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `userads_data`
--
ALTER TABLE `userads_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD UNIQUE KEY `email_2` (`email`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`),
  ADD KEY `password` (`password`),
  ADD KEY `last_active` (`last_active`),
  ADD KEY `admin` (`admin`),
  ADD KEY `active` (`active`),
  ADD KEY `registered` (`registered`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `verified` (`verified`),
  ADD KEY `lat` (`lat`),
  ADD KEY `lng` (`lng`);

--
-- Indexes for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_invitations`
--
ALTER TABLE `admin_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_views`
--
ALTER TABLE `announcement_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apps_sessions`
--
ALTER TABLE `apps_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_receipts`
--
ALTER TABLE `bank_receipts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banned`
--
ALTER TABLE `banned`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_pages`
--
ALTER TABLE `custom_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hashtags`
--
ALTER TABLE `hashtags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invitation_links`
--
ALTER TABLE `invitation_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keyword_search`
--
ALTER TABLE `keyword_search`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `langs`
--
ALTER TABLE `langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=476;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions_votes`
--
ALTER TABLE `questions_votes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recent_search`
--
ALTER TABLE `recent_search`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_ads`
--
ALTER TABLE `site_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userads`
--
ALTER TABLE `userads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userads_data`
--
ALTER TABLE `userads_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_requests`
--
ALTER TABLE `verification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
