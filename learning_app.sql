-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 23, 2026 lúc 03:11 PM
-- Phiên bản máy phục vụ: 8.0.45
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `learning_app`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`) VALUES
(4, 'tai', 'tai@gmail.com', '$2y$10$hEFESVK2BP9ki8GxMA3yO.mOW8Metp0VxEibHioatwlx1LqOJiRWy'),
(5, 'anhtai', 'nguyentai@gmail.com', '$2y$10$znXLf.Xl55QjiNUTxk/7rO5FGSrPQyYA2jKLlQYDQEVBhcAPNRwbi');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `answers`
--

CREATE TABLE `answers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_attempt_id` int NOT NULL,
  `question_id` int NOT NULL,
  `option_id` int DEFAULT NULL,
  `answer_text` text COLLATE utf8mb4_general_ci,
  `is_correct` tinyint(1) DEFAULT NULL,
  `answered_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Lập trình', 'lap-trinh', 'Lập trình web, C, C++, C#, Python, Java, PHP,', '2025-12-09 17:02:46'),
(2, 'Toán', 'toan', 'Toán Rời Rạc, Toán Cơ Sở, Xác Suất Thống Kê, Xử Lý Tín Hiệu Số, Toán Cao Cấp, An Toàn Thông Tin, Đại Số Tuyến Tính, Giải Tích, Vật Lý Đại Cương', '2025-12-10 14:56:34'),
(3, 'Lý Thuyết', 'ly-thuyet', 'Triết Học Mác_LêNin, Tư Tưởng Hồ Chính Minh, Kinh Tế Chính Trị Mác-LeeNin, Chủ Nghĩa Xã Hội Khoa Học, Lịch Sử Đảng Cộng Sản Việt Nam,...', '2025-12-10 14:59:38'),
(4, 'Thể Chất', 'the-chat', '48 Động Tác Thể Dục và các bài tập thể dục nhịp điệu', '2025-12-10 15:01:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `certificates`
--

CREATE TABLE `certificates` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `certificate_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `issued_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `lesson_id` int DEFAULT NULL,
  `comment_text` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `course_id`, `lesson_id`, `comment_text`, `created_at`) VALUES
(1, 17, NULL, 7, 'ui thầy dạy nhiệt tình quá', '2025-12-11 22:13:12'),
(3, 19, NULL, 8, '............', '2025-12-12 10:38:58'),
(4, 17, NULL, 8, '????', '2025-12-22 02:24:24'),
(5, 19, NULL, 9, 'hi', '2025-12-25 14:58:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied') COLLATE utf8mb4_general_ci DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `email`, `phone`, `message`, `created_at`, `status`) VALUES
(1, 'nguyễn', 'anh tài', 'hocvien1@gmail.com', '123456', 'xin chào admin', '2025-12-22 02:36:57', 'new'),
(2, 'hung', 'pham', 'hung@gmail.com', '090943', 'ok', '2026-05-23 12:37:19', 'new'),
(3, 'hung', 'pham', 'tai@gmail.com', '0954789758', 'ưeq', '2026-05-23 12:41:58', 'new'),
(4, 'hung', 'pham', 'admin@mayviet.net', '0954789758', 'hung', '2026-05-23 12:53:28', 'new');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL,
  `category_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','published','archived') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `teacher_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `courses`
--

INSERT INTO `courses` (`id`, `category_id`, `title`, `description`, `thumbnail`, `price`, `status`, `updated_at`, `slug`, `teacher_id`, `created_at`) VALUES
(1, 1, 'Lập trình C++', 'hướng dẫn học lập trình c++ cho người mới', 'uploads/courses/lap-trinh-c-1766683849.jpg', 0.00, 'published', '2025-12-25 17:30:49', 'lap-trinh-c', 23, '2025-10-01 17:30:58'),
(3, 1, 'Lập trình Web', 'lập trình web cho người mới', 'uploads/courses/lap-trinh-web-1766683822.jpg', 0.00, 'published', '2025-12-25 17:30:22', 'lap-trinh-web', 23, '2025-10-01 17:35:50'),
(8, 2, 'Toán Cơ Sở', 'toán cơ sở', 'uploads/courses/toan-co-so-1766683778.jpg', 1200000.00, 'published', '2025-12-25 17:29:38', 'toan-co-so', 23, '2025-12-10 14:52:29'),
(9, 1, 'Thị Giác Máy Tính', 'lập trình python', 'uploads/courses/thi-giac-may-tinh-1765980998.jpg', 1250000.00, 'published', '2025-12-17 14:16:38', 'thi-giac-may-tinh', 23, '2025-12-10 14:54:20'),
(10, 3, 'Tư Tưởng Hồ Chí Minh', '.......', 'uploads/courses/tu-tuong-ho-chi-minh-1766683719.jpg', 700000.00, 'published', '2025-12-25 17:28:39', 'tu-tuong-ho-chi-minh', 24, '2025-12-10 15:02:23'),
(11, 3, 'Lịch Sử Đảng Cộng Sản Việt Nam', '........', 'uploads/courses/lich-su-dang-cong-san-viet-nam-1766683686.jpg', 700000.00, 'published', '2025-12-25 17:28:06', 'lich-su-dang-cong-san-viet-nam', 24, '2025-12-10 15:03:01'),
(12, 1, 'Lập Trình PHP', 'lập trình php', 'uploads/courses/lap-trinh-php-1766683616.png', 1200000.00, 'published', '2025-12-25 17:26:56', 'lap-trinh-php', 25, '2025-12-10 15:34:38'),
(13, 1, 'Lập Trình Mạng', '.......', 'uploads/courses/lap-trinh-mang-1766683561.jpg', 700000.00, 'published', '2025-12-25 17:26:01', 'lap-trinh-mang', 25, '2025-12-10 15:35:15'),
(14, 2, 'Xử Lý Tín Hiệu Số', '.......', 'uploads/courses/xu-ly-tin-hieu-so-1766683502.png', 1200000.00, 'published', '2025-12-25 17:25:02', 'xu-ly-tin-hieu-so', 26, '2025-12-10 15:56:12'),
(15, 2, 'Xử Lý Ngôn Ngữ Tự Nhiên', '......', 'uploads/courses/xu-ly-ngon-ngu-tu-nhien-1766683439.jpg', 1200000.00, 'published', '2025-12-25 17:23:59', 'xu-ly-ngon-ngu-tu-nhien', 26, '2025-12-10 15:56:43'),
(16, 4, 'Thể Chất 1', '.......', 'uploads/courses/the-chat-1-1766683400.jpg', 0.00, 'published', '2025-12-25 17:23:20', 'the-chat-1', 27, '2025-12-10 16:05:31'),
(17, 1, 'Cấu trúc dữ liệu và giải thuật', '..', 'uploads/courses/cau-truc-du-lieu-va-giai-thuat-1766683345.png', 0.00, 'published', '2025-12-25 17:22:25', 'cau-truc-du-lieu-va-giai-thuat', 23, '2025-12-22 08:30:40'),
(18, 3, 'Âm nhạc', '', 'uploads/courses/am-nhac-1766683282.jpg', 0.00, 'published', '2025-12-25 17:21:22', 'am-nhac', 23, '2025-12-25 16:33:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `enrolled_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrolled_at`) VALUES
(1, 17, 16, '2025-12-11 21:26:38'),
(2, 17, 15, '2025-12-11 21:36:23'),
(3, 17, 14, '2025-12-11 22:01:14'),
(4, 17, 13, '2025-12-12 09:47:29'),
(5, 27, 16, '2025-12-12 10:25:02'),
(6, 22, 16, '2025-12-12 10:28:12'),
(7, 24, 11, '2025-12-12 10:36:01'),
(8, 24, 16, '2025-12-12 10:36:46'),
(9, 19, 16, '2025-12-12 10:38:41'),
(10, 27, 9, '2025-12-17 21:48:04'),
(11, 22, 17, '2025-12-22 10:26:46'),
(12, 22, 10, '2025-12-22 10:27:16'),
(13, 22, 11, '2025-12-22 10:32:46'),
(14, 17, 1, '2025-12-24 14:44:22'),
(15, 17, 18, '2025-12-25 16:35:05'),
(16, 17, 17, '2025-12-25 18:53:03'),
(17, 18, 15, '2025-12-25 21:59:55'),
(18, 29, 18, '2026-05-23 17:10:08'),
(19, 29, 17, '2026-05-23 17:18:07'),
(20, 13, 16, '2026-05-23 18:34:25'),
(21, 29, 16, '2026-05-23 18:54:50'),
(22, 29, 14, '2026-05-23 19:18:05'),
(23, 29, 11, '2026-05-23 19:23:36'),
(24, 29, 12, '2026-05-23 19:25:08'),
(25, 29, 15, '2026-05-23 19:26:02'),
(26, 29, 10, '2026-05-23 19:26:49'),
(27, 29, 9, '2026-05-23 19:28:03'),
(28, 29, 8, '2026-05-23 19:29:06'),
(29, 29, 13, '2026-05-23 19:32:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `score` float NOT NULL DEFAULT '0',
  `answers` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `exam_results`
--

INSERT INTO `exam_results` (`id`, `user_id`, `quiz_id`, `score`, `answers`, `created_at`) VALUES
(1, 18, 14, 6.67, '{\"208\":\"A\",\"209\":\"A\",\"210\":\"B\",\"211\":\"B\",\"213\":\"B\",\"214\":\"B\",\"216\":\"B\",\"219\":\"B\",\"221\":\"B\",\"224\":\"A\",\"226\":\"B\",\"228\":\"B\",\"233\":\"B\",\"237\":\"B\",\"241\":\"B\",\"242\":\"A\",\"243\":\"B\",\"245\":\"B\",\"250\":\"C\",\"251\":\"B\",\"255\":\"B\",\"257\":\"B\",\"259\":\"B\",\"262\":\"C\",\"263\":\"B\",\"264\":\"B\",\"266\":\"B\",\"267\":\"A\",\"268\":\"B\",\"269\":\"A\",\"271\":\"B\",\"272\":\"B\",\"273\":\"B\",\"275\":\"B\",\"278\":\"A\",\"279\":\"B\",\"280\":\"B\",\"281\":\"B\",\"284\":\"B\",\"287\":\"B\",\"288\":\"B\",\"289\":\"C\",\"291\":\"B\",\"292\":\"B\",\"293\":\"B\"}', '2025-12-25 15:01:01'),
(2, 18, 14, 6.67, '{\"208\":\"A\",\"209\":\"B\",\"210\":\"B\",\"211\":\"B\",\"213\":\"B\",\"214\":\"B\",\"216\":\"B\",\"219\":\"C\",\"221\":\"C\",\"224\":\"A\",\"226\":\"B\",\"228\":\"A\",\"233\":\"B\",\"237\":\"B\",\"241\":\"B\",\"242\":\"B\",\"243\":\"B\",\"245\":\"B\",\"250\":\"B\",\"251\":\"B\",\"255\":\"B\",\"257\":\"C\",\"259\":\"B\",\"262\":\"B\",\"263\":\"B\",\"264\":\"A\",\"266\":\"B\",\"267\":\"B\",\"268\":\"B\",\"269\":\"D\",\"271\":\"B\",\"272\":\"B\",\"273\":\"B\",\"275\":\"B\",\"278\":\"B\",\"279\":\"B\",\"280\":\"B\",\"281\":\"B\",\"284\":\"A\",\"287\":\"B\",\"288\":\"B\",\"289\":\"B\",\"291\":\"B\",\"292\":\"B\",\"293\":\"B\"}', '2025-12-25 15:17:59'),
(3, 18, 13, 0.25, '{\"99\":\"B\",\"100\":\"B\",\"101\":\"A\"}', '2025-12-25 16:02:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lessons`
--

CREATE TABLE `lessons` (
  `id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `video_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `audio_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_general_ci,
  `attachment` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `video_url`, `audio_url`, `content`, `attachment`, `created_at`) VALUES
(1, 1, 'Bài 1- Lập trình C++, cài đặt visual studio 2023 - lập trình C++ 2023 cho người mới', 'https://www.youtube.com/watch?v=5vLkWRF-dpE&list=PLPt6-BtUI22rZ-lB276VBY85mUNeIFJf5', NULL, 'test', 'uploads/materials/6937f9fa25f1f.pdf', '2025-12-09 17:29:14'),
(2, 10, 'Bài 1: Giới Thiệu về môn học', 'uploads/videos/1766654712_6440.mp4', NULL, '.....', 'uploads/materials/6939299471b16.docx', '2025-12-10 15:04:36'),
(3, 11, 'Bài 1: Giới thiệu về môn học', 'uploads/videos/1766654721_3763.mp4', NULL, '........', 'uploads/materials/693929ad562af.docx', '2025-12-10 15:05:01'),
(4, 12, 'bài 1: Giới Thiệu về môn học', 'uploads/videos/1766654729_4097.mp4', NULL, '....', 'uploads/materials/693930f98b70f.pdf', '2025-12-10 15:36:09'),
(5, 13, 'Bài 1: Giới thiệu về môn học', 'uploads/videos/1766654737_2954.mp4', NULL, '......', 'uploads/materials/69393116ef19c.pdf', '2025-12-10 15:36:38'),
(6, 14, 'Bài 1: Giới thiêu', 'uploads/videos/1766654746_2465.mp4', NULL, '......', 'uploads/materials/693935eb6517a.docx', '2025-12-10 15:57:15'),
(7, 15, 'Bài 1: Giới thiệu', 'uploads/videos/1766650463_1928.mp4', NULL, '...', 'uploads/materials/693936059e376.docx', '2025-12-10 15:57:41'),
(8, 16, 'Bài 1: Khởi động', 'https://www.youtube.com', NULL, '.....', 'uploads/materials/693b8ad714f25.mp4', '2025-12-10 16:06:11'),
(9, 16, 'Bài 2 - Nhảy', 'uploads/videos/1766654690_3405.mp4', NULL, '', 'uploads/materials/694848ff1e17c.mp4', '2025-12-22 02:22:39'),
(10, 16, 'Bài 3: Chạy', 'uploads/videos/1766650283_9064.mp4', NULL, '', 'uploads/materials/694cf1ab38f82.pdf', '2025-12-25 15:11:23'),
(11, 18, 'Orange 7!', '', 'uploads/audio/audio_1766655290_7110.mp3', '', 'uploads/materials/694d053a01706.pdf', '2025-12-25 16:34:50'),
(12, 18, 'Bài 2: Luyện viết thanh nhạc', '', '', '<p>xin ch&agrave;o c&aacute;c bạn, h&ocirc;m nay ch&uacute;ng ta sẽ học b&agrave;i 2. B&agrave;i 2 của ch&uacute;ng ta l&agrave; luyện viết thanh nhạc<a href=\"https://thanhnhac.vn/thuong_guitar/428/5-mau-luyen-thanh-co-ban.html\"><img alt=\"mẫu luyện thanh nhạc\" src=\"https://thanhnhac.vn/thuong_guitar/428/5-mau-luyen-thanh-co-ban.html\" style=\"border-style:solid; border-width:50px; height:20px; margin:0px 50px; width:25px\" /></a></p>', '', '2025-12-25 16:45:39'),
(13, 3, 'Demo Web', 'https://www.youtube.com/watch?v=bLXN8F8544A&list=RDbLXN8F8544A&start_radio=1', '', '<p>đ&acirc;y l&agrave; b&agrave;i kiểm tra</p>', '', '2026-05-23 17:37:13'),
(14, 13, 'Cisco', 'https://www.youtube.com/results?search_query=packet+tracer+build+a+simple+network+for+company', '', '<p>b&agrave;i giảng</p>', '', '2026-05-23 19:42:41'),
(15, 17, 'Cisco', 'https://www.youtube.com/results?search_query=packet+tracer+build+a+simple+network+for+company', '', '<p>demo</p>', '', '2026-05-23 19:43:05'),
(16, 13, 'netwwork', 'https://www.youtube.com/watch?v=mLG0m3qqpPE', '', '<p>network</p>', '', '2026-05-23 19:55:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lesson_materials`
--

CREATE TABLE `lesson_materials` (
  `id` int NOT NULL,
  `lesson_id` int DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uploaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `lesson_materials`
--

INSERT INTO `lesson_materials` (`id`, `lesson_id`, `file_path`, `file_name`, `uploaded_at`) VALUES
(1, 1, 'uploads/materials/1765276627_6937fbd323d17.docx', 'Video Hướng dẫn code C++ bài 1', '2025-12-09 17:37:07'),
(2, 3, 'uploads/materials/1765353941_693929d52018a.docx', 'Giới thiệu về môn học Lịch Sử Đảng Cộng Sản Việt Nam', '2025-12-10 15:05:41'),
(3, 2, 'uploads/materials/1765353975_693929f7876e3.docx', 'Giới thiệu về môn học Tư Tưởng Hồ Chí Minh', '2025-12-10 15:06:15'),
(4, 5, 'uploads/materials/1765355850_6939314ad4d6f.pdf', 'Lập trình Mạng', '2025-12-10 15:37:30'),
(5, 4, 'uploads/materials/1765355867_6939315bf056f.pdf', 'Lập trình PHP', '2025-12-10 15:37:47'),
(6, 7, 'uploads/materials/1765357106_6939363265ca0.docx', 'Slide giới thiệu môn học Xử Lý Ngôn Ngữ Tự Nhiên', '2025-12-10 15:58:26'),
(7, 6, 'uploads/materials/1765357128_69393648e3044.docx', 'Slide giới thiệu môn học Xử Lý Tín Hiệu Số', '2025-12-10 15:58:48'),
(8, 8, 'uploads/materials/1765357590_693938166fd0f.pdf', 'Thể chất 1', '2025-12-10 16:06:30'),
(9, 8, 'uploads/materials/1766344925_694848dd1f03b.mp4', 'Thể chất 2', '2025-12-22 02:22:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `sender_type` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL,
  `receiver_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `is_read` tinyint DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `priority` enum('normal','important') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'normal',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `sender_id`, `sender_type`, `receiver_id`, `title`, `message`, `is_read`, `created_at`, `priority`, `is_pinned`) VALUES
(6, 5, 'admin', 13, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(7, 5, 'admin', 17, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(8, 5, 'admin', 18, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(9, 5, 'admin', 19, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(10, 5, 'admin', 20, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(11, 5, 'admin', 22, 'Nghỉ lễ tết nguyên đán 2026', 'nghỉ 2 tuần', 0, '2025-12-21 20:42:30', 'normal', 0),
(12, 5, 'admin', 24, 'nghỉ lễ tết dương lịch 1/1/2026', '1 ngày', 0, '2025-12-21 20:43:02', 'normal', 0),
(13, 5, 'admin', 25, 'nghỉ lễ tết dương lịch 1/1/2026', '1 ngày', 0, '2025-12-21 20:43:02', 'normal', 0),
(14, 5, 'admin', 27, 'nghỉ lễ tết dương lịch 1/1/2026', '1 ngày', 1, '2025-12-21 20:43:02', 'normal', 0),
(15, 5, 'admin', 13, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:35', 'normal', 0),
(16, 5, 'admin', 17, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 1, '2025-12-21 20:53:35', 'normal', 0),
(17, 5, 'admin', 18, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:35', 'normal', 0),
(18, 5, 'admin', 19, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:35', 'normal', 0),
(19, 5, 'admin', 20, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:35', 'normal', 0),
(20, 5, 'admin', 22, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:35', 'normal', 0),
(21, 5, 'admin', 13, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0),
(22, 5, 'admin', 17, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0),
(23, 5, 'admin', 18, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0),
(24, 5, 'admin', 19, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0),
(25, 5, 'admin', 20, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0),
(26, 5, 'admin', 22, 'nghỉ cuối tuần để bảo trì hệ thống', 'nghỉ 1 ngày bảo trì hệ thống', 0, '2025-12-21 20:53:48', 'normal', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `options`
--

CREATE TABLE `options` (
  `id` int NOT NULL,
  `question_id` int DEFAULT NULL,
  `option_text` text COLLATE utf8mb4_general_ci,
  `is_correct` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'bank_transfer',
  `transaction_code` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `course_id`, `total_amount`, `payment_method`, `transaction_code`, `status`, `created_at`) VALUES
(1, 17, 16, 1500000.00, 'bank_transfer', NULL, 'completed', '2025-12-11 21:26:38'),
(2, 17, 15, 1200000.00, 'bank_transfer', 'tien2', 'completed', '2025-12-11 21:36:23'),
(3, 17, 14, 1200000.00, 'bank_transfer', 'tien1', 'completed', '2025-12-11 22:01:14'),
(4, 17, 13, 700000.00, 'bank_transfer', '', 'completed', '2025-12-12 09:47:29'),
(5, 27, 16, 1500000.00, 'bank_transfer', '', 'completed', '2025-12-12 10:25:02'),
(7, 22, 16, 1500000.00, 'bank_transfer', '', 'completed', '2025-12-12 10:28:12'),
(8, 24, 11, 700000.00, 'bank_transfer', '', 'completed', '2025-12-12 10:36:01'),
(9, 24, 16, 1500000.00, 'bank_transfer', '', 'completed', '2025-12-12 10:36:46'),
(10, 19, 16, 1500000.00, 'bank_transfer', '', 'completed', '2025-12-12 10:38:41'),
(11, 27, 9, 1250000.00, 'bank_transfer', '', 'completed', '2025-12-17 21:48:04'),
(13, 22, 17, 0.00, 'bank_transfer', '', 'completed', '2025-12-22 10:26:46'),
(14, 22, 10, 700000.00, 'bank_transfer', '', 'completed', '2025-12-22 10:27:16'),
(15, 22, 11, 700000.00, 'bank_transfer', '', 'completed', '2025-12-22 10:32:46'),
(17, 17, 1, 0.00, 'bank_transfer', NULL, 'completed', '2025-12-24 14:44:22'),
(18, 17, 18, 0.00, 'bank_transfer', NULL, 'completed', '2025-12-25 16:35:05'),
(19, 17, 17, 0.00, 'bank_transfer', NULL, 'completed', '2025-12-25 18:53:03'),
(20, 18, 15, 1200000.00, 'bank_transfer', NULL, 'completed', '2025-12-25 21:59:55'),
(21, 29, 18, 0.00, 'bank_transfer', NULL, 'completed', '2026-05-23 17:10:08'),
(22, 29, 17, 0.00, 'bank_transfer', NULL, 'completed', '2026-05-23 17:18:07'),
(23, 13, 16, 0.00, 'bank_transfer', NULL, 'completed', '2026-05-23 18:34:25'),
(24, 29, 16, 0.00, 'bank_transfer', NULL, 'completed', '2026-05-23 18:54:50'),
(25, 29, 14, 1200000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:18:05'),
(26, 29, 11, 700000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:23:36'),
(27, 29, 12, 1200000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:25:08'),
(28, 29, 15, 1200000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:26:02'),
(29, 29, 10, 700000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:26:49'),
(30, 29, 9, 1250000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:28:03'),
(31, 29, 8, 1200000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:29:06'),
(32, 29, 13, 700000.00, 'bank_transfer', NULL, 'completed', '2026-05-23 19:32:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `progress`
--

CREATE TABLE `progress` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `lesson_id` int DEFAULT NULL,
  `status` enum('not_started','in_progress','completed') COLLATE utf8mb4_general_ci DEFAULT 'not_started',
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions`
--

CREATE TABLE `questions` (
  `id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_a` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_b` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_c` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_d` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `level` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'easy',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `explanation`, `level`, `created_at`) VALUES
(99, 13, 'Tokenization trong NLP là gì?', 'Quá trình dịch văn bản', 'Quá trình chia văn bản thành các đơn vị nhỏ hơn (từ/ký tự)', 'Quá trình gán nhãn từ loại', 'Quá trình tóm tắt văn bản', 'B', 'Tokenization là bước tách văn bản thành các token (từ, cụm từ, ký tự).', 'easy', '2025-12-25 14:03:28'),
(100, 13, 'Stop words (từ dừng) là gì?', 'Những từ mang nhiều ý nghĩa nhất', 'Những từ xuất hiện ít nhất', 'Những từ xuất hiện ít nhất,Những từ phổ biến nhưng ít mang ý nghĩa phân loại (là, và, của...)', 'Những từ sai chính tả', 'C', '\"Stop words xuất hiện nhiều nhưng ít giá trị ngữ nghĩa đặc trưng (ví dụ: the, is, a).\"', 'easy', '2025-12-25 14:03:28'),
(101, 13, 'Stemming là quá trình gì?', 'Đưa từ về dạng gốc bằng cách cắt bỏ hậu tố', 'Chuyển văn bản thành vector', 'Phân tích cú pháp câu', 'Dịch máy', 'A', 'Stemming cắt bỏ phần đuôi từ để đưa về dạng gốc (ví dụ: playing -> play).', 'easy', '2025-12-25 14:03:28'),
(102, 13, 'Lemmatization khác Stemming ở điểm nào?', 'Nó nhanh hơn Stemming', 'Nó đưa từ về dạng nguyên thể có nghĩa trong từ điển', 'Nó chỉ dùng cho tiếng Anh', 'Nó không cần từ điển', 'B', 'Lemmatization sử dụng phân tích hình thái và từ điển để đưa từ về dạng gốc chính xác (lemma).', 'easy', '2025-12-25 14:03:28'),
(103, 13, 'Corpus trong NLP nghĩa là gì?', 'Một thuật toán máy học', 'Một tập hợp lớn các văn bản dữ liệu', 'Một thư viện Python', 'Một mô hình ngôn ngữ', 'B', 'Corpus (tập ngữ liệu) là tập hợp dữ liệu văn bản dùng để huấn luyện mô hình.', 'easy', '2025-12-25 14:03:28'),
(104, 13, 'Thư viện nào phổ biến nhất cho NLP cơ bản trong Python?', 'Pandas', 'NLTK', 'Matplotlib', 'OpenCV', 'B', 'NLTK (Natural Language Toolkit) là thư viện kinh điển cho NLP.', 'easy', '2025-12-25 14:03:28'),
(105, 13, 'Bag of Words (BoW) biểu diễn văn bản như thế nào?', 'Dựa trên thứ tự từ', 'Dựa trên tần suất xuất hiện của từ mà bỏ qua thứ tự', 'Dựa trên ngữ nghĩa của từ', 'Dựa trên cấu trúc ngữ pháp', 'B', 'BoW chỉ quan tâm từ đó xuất hiện bao nhiêu lần, không quan tâm vị trí.', 'medium', '2025-12-25 14:03:28'),
(106, 13, 'Nhược điểm lớn nhất của One-Hot Encoding cho từ vựng lớn là gì?', 'Tính toán chậm', 'Vector quá thưa (sparsity) và số chiều quá lớn', 'Không thể biểu diễn số', 'Dễ bị Overfitting', 'B', 'Với từ điển lớn, One-Hot tạo ra vector rất dài với hầu hết là số 0.', 'medium', '2025-12-25 14:03:28'),
(107, 13, 'TF-IDF là viết tắt của?', 'Term Frequency - Inverse Document Frequency', 'Total Frequency - Inverse Data Frequency', 'Term Frequency - Inverse Data Frequency', 'Total Frequency - Inverse Document Frequency', 'A', 'Đo lường tầm quan trọng của từ trong văn bản so với cả tập ngữ liệu.', 'medium', '2025-12-25 14:03:28'),
(109, 13, 'N-gram với N=2 được gọi là gì?', 'Unigram', 'Bigram', 'Trigram', 'Skip-gram', 'B', 'Bigram là chuỗi 2 từ liên tiếp.', 'easy', '2025-12-25 14:03:28'),
(110, 13, 'Word Embedding giải quyết vấn đề gì của One-Hot Encoding?', 'Giảm kích thước file', 'Giữ lại ý nghĩa ngữ nghĩa và giảm số chiều vector', 'Tăng tốc độ xử lý', 'Loại bỏ stop words', 'B', 'Word Embedding biểu diễn từ trong không gian vector dày (dense) và bảo toàn ngữ nghĩa.', 'medium', '2025-12-25 14:03:28'),
(111, 13, 'Word2Vec có hai kiến trúc chính là gì?', 'CBOW và Skip-gram', 'RNN và LSTM', 'Encoder và Decoder', 'CNN và RNN', 'A', 'CBOW dự đoán từ trung tâm từ ngữ cảnh, Skip-gram dự đoán ngữ cảnh từ từ trung tâm.', 'hard', '2025-12-25 14:03:28'),
(112, 13, 'Cosine Similarity dùng để làm gì trong NLP?', 'Đo độ dài văn bản', 'Đo mức độ tương đồng giữa hai vector văn bản', 'Đếm số từ', 'Sắp xếp từ điển', 'B', 'Đo góc giữa hai vector để xác định độ tương đồng về ngữ nghĩa.', 'medium', '2025-12-25 14:03:28'),
(113, 13, 'Trong Word2Vec, kết quả của phép toán: King - Man + Woman sấp xỉ bằng?', 'Emperor', 'Prince', 'Queen', 'Princess', 'C', '\"Đây là ví dụ kinh điển về tính chất đại số của Word Embedding.\"', 'hard', '2025-12-25 14:03:28'),
(114, 13, 'Sentiment Analysis là bài toán gì?', 'Phân tích cú pháp', 'Phân loại cảm xúc (Tích cực/Tiêu cực...)', 'Dịch máy', 'Tóm tắt văn bản', 'B', 'Xác định thái độ/cảm xúc của người viết.', 'easy', '2025-12-25 14:03:29'),
(115, 13, 'Naive Bayes thường được dùng cho tác vụ NLP nào?', 'Dịch máy', 'Phân loại văn bản (ví dụ: lọc spam)', 'Tóm tắt văn bản', 'Hỏi đáp', 'B', 'Naive Bayes rất hiệu quả trong phân loại văn bản dựa trên xác suất.', 'medium', '2025-12-25 14:03:29'),
(116, 13, 'Mô hình ẩn Markov (HMM) thường dùng cho bài toán nào trong NLP truyền thống?', 'POS Tagging (Gán nhãn từ loại)', 'Sentiment Analysis', 'Topic Modeling', 'Text Generation', 'A', 'HMM thường dùng để dự đoán chuỗi trạng thái ẩn (từ loại) từ chuỗi quan sát (từ).', 'hard', '2025-12-25 14:03:29'),
(117, 13, 'POS Tagging là viết tắt của?', 'Part-of-Speech Tagging', 'Position-of-Sentence Tagging', 'Post-Office Service', 'Processing-of-String', 'A', 'Gán nhãn từ loại (Danh từ, Động từ, Tính từ...) cho từng từ.', 'easy', '2025-12-25 14:03:29'),
(119, 13, 'Biểu diễn IOB (Inside-Outside-Beginning) dùng trong bài toán nào?', 'Sentiment Analysis', 'NER (Chunking)', 'Machine Translation', 'Text Summarization', 'B', 'Dùng để đánh dấu ranh giới của các thực thể trong chuỗi.', 'hard', '2025-12-25 14:03:29'),
(120, 13, 'RNN là viết tắt của?', 'Recursive Neural Network', 'Recurrent Neural Network', 'Random Neural Network', 'Rotational Neural Network', 'B', 'Mạng nơ-ron hồi quy.', 'easy', '2025-12-25 14:03:29'),
(121, 13, 'Tại sao RNN phù hợp với dữ liệu văn bản?', 'Nó xử lý dữ liệu dạng bảng tốt', 'Nó có khả năng xử lý dữ liệu chuỗi và ghi nhớ thông tin quá khứ', 'Nó xử lý ảnh tốt', 'Nó không cần huấn luyện', 'B', 'RNN có kết nối hồi quy cho phép thông tin truyền dọc theo chuỗi thời gian.', 'medium', '2025-12-25 14:03:29'),
(122, 13, 'Vấn đề lớn nhất của RNN truyền thống là gì?', 'Overfitting', 'Vanishing Gradient (Biến mất đạo hàm)', 'Underfitting', 'Tốn bộ nhớ', 'B', 'Khó học được các phụ thuộc xa do đạo hàm tiến về 0 khi lan truyền ngược qua nhiều bước.', 'hard', '2025-12-25 14:03:29'),
(123, 13, 'LSTM sinh ra để giải quyết vấn đề gì của RNN?', 'Tốc độ chậm', 'Vanishing Gradient', 'Khả năng xử lý ảnh', 'Độ chính xác thấp', 'B', 'LSTM dùng cơ chế cổng (gates) để duy trì thông tin trong thời gian dài.', 'medium', '2025-12-25 14:03:29'),
(125, 13, 'GRU khác LSTM như thế nào?', 'GRU phức tạp hơn', 'GRU không có cổng Output mà gộp thành cổng Update và Reset', 'GRU chậm hơn', 'GRU có 4 cổng', 'B', 'GRU là phiên bản đơn giản hóa của LSTM, thường huấn luyện nhanh hơn.', 'hard', '2025-12-25 14:03:29'),
(126, 13, 'Bidirectional RNN (Bi-RNN) có đặc điểm gì?', 'Chỉ đọc từ trái sang phải', 'Đọc dữ liệu theo cả hai chiều (trái sang phải và phải sang trái)', 'Chỉ đọc từ phải sang trái', 'Không dùng cho văn bản', 'B', 'Giúp mô hình nắm bắt ngữ cảnh từ cả quá khứ và tương lai.', 'hard', '2025-12-25 14:03:29'),
(127, 13, 'Seq2Seq (Sequence to Sequence) thường dùng cho?', 'Phân loại ảnh', 'Dịch máy và Chatbot', 'Hồi quy tuyến tính', 'Clustering', 'B', 'Ánh xạ một chuỗi đầu vào sang một chuỗi đầu ra (ví dụ: Tiếng Anh -> Tiếng Việt).', 'medium', '2025-12-25 14:03:29'),
(128, 13, 'Kiến trúc Encoder-Decoder là thành phần cốt lõi của?', 'Naive Bayes', 'Seq2Seq models', 'Decision Trees', 'K-Means', 'B', 'Encoder mã hóa đầu vào thành vector ngữ cảnh, Decoder giải mã thành chuỗi đích.', 'hard', '2025-12-25 14:03:29'),
(129, 13, 'Cơ chế Attention giải quyết vấn đề gì của Seq2Seq truyền thống?', 'Tốc độ huấn luyện', 'Vấn đề nút thắt cổ chai (bottleneck) khi dồn nén thông tin vào một vector cố định', 'Thiếu dữ liệu', 'Overfitting', 'B', 'Cho phép Decoder \'nhìn\' vào toàn bộ câu gốc thay vì chỉ vector ngữ cảnh cuối cùng.', 'medium', '2025-12-25 14:03:29'),
(131, 13, 'Transformer khác RNN ở điểm chính nào?', 'Dùng Convolution', 'Dùng cơ chế Self-Attention và xử lý song song thay vì tuần tự', 'Dùng ít tham số hơn', 'Chỉ dùng cho ảnh', 'B', 'Transformer bỏ qua hồi quy (recurrence) để tính toán song song toàn bộ câu.', 'hard', '2025-12-25 14:03:29'),
(132, 13, 'Mô hình BERT được huấn luyện dựa trên kiến trúc nào?', 'Decoder của Transformer', 'Encoder của Transformer', 'Cả Encoder và Decoder', 'RNN', 'B', 'BERT (Bidirectional Encoder Representations from Transformers) chỉ dùng phần Encoder.', 'hard', '2025-12-25 14:03:29'),
(133, 13, 'GPT (Generative Pre-trained Transformer) dùng kiến trúc nào?', 'Encoder của Transformer', 'Decoder của Transformer', 'CNN', 'LSTM', 'B', 'GPT dùng Decoder để sinh văn bản theo hướng trái sang phải (autoregressive).', 'hard', '2025-12-25 14:03:29'),
(134, 13, 'Fine-tuning trong NLP là gì?', 'Huấn luyện mô hình từ đầu', 'Lấy mô hình đã huấn luyện sẵn (Pre-trained) và huấn luyện thêm trên dữ liệu chuyên biệt', 'Chỉnh sửa code của mô hình', 'Tăng kích thước mô hình', 'B', 'Tinh chỉnh mô hình lớn vào tác vụ cụ thể giúp tiết kiệm thời gian và dữ liệu.', 'medium', '2025-12-25 14:03:29'),
(135, 13, 'Masked Language Modeling (MLM) là nhiệm vụ huấn luyện của?', 'GPT', 'BERT', 'Word2Vec', 'GloVe', 'B', 'BERT ẩn đi một số từ trong câu và cố gắng đoán chúng dựa trên ngữ cảnh 2 chiều.', 'hard', '2025-12-25 14:03:29'),
(136, 13, 'Nhiệm vụ Next Sentence Prediction (NSP) dùng để làm gì trong BERT?', 'Dự đoán từ tiếp theo', 'Dự đoán xem câu B có phải là câu nối tiếp câu A không', 'Dự đoán cảm xúc', 'Dịch câu', 'B', 'Giúp BERT hiểu mối quan hệ giữa các câu.', 'hard', '2025-12-25 14:03:29'),
(137, 13, 'BLEU score là chỉ số đánh giá cho tác vụ nào?', 'Phân loại văn bản', 'Dịch máy (Machine Translation)', 'NER', 'POS Tagging', 'B', 'So sánh độ trùng khớp n-gram giữa bản dịch máy và bản dịch tham khảo.', 'medium', '2025-12-25 14:03:29'),
(138, 13, 'ROUGE score thường dùng đánh giá tác vụ nào?', 'Dịch máy', 'Tóm tắt văn bản (Text Summarization)', 'Phân loại tin tức', 'Hỏi đáp', 'B', 'Đo độ phủ của bản tóm tắt máy so với bản tóm tắt mẫu.', 'medium', '2025-12-25 14:03:29'),
(139, 13, 'Perplexity (PPL) càng thấp thì mô hình ngôn ngữ càng...?', 'Tệ', 'Tốt', 'Không xác định', 'Chậm', 'B', 'Perplexity thấp nghĩa là mô hình ít \'bối rối\' hơn khi dự đoán từ tiếp theo.', 'hard', '2025-12-25 14:03:29'),
(141, 13, 'Dropout là kỹ thuật để?', 'Tăng tốc độ training', 'Giảm Overfitting', 'Tăng số lượng tham số', 'Xóa dữ liệu lỗi', 'B', 'Tắt ngẫu nhiên một số nơ-ron trong quá trình huấn luyện.', 'easy', '2025-12-25 14:03:29'),
(142, 13, 'Beam Search dùng để làm gì trong quá trình sinh văn bản?', 'Tìm từ có xác suất cao nhất (Greedy)', 'Tìm k chuỗi có xác suất cao nhất để chọn lựa', 'Tìm từ ngẫu nhiên', 'Xóa từ lặp', 'B', 'Mở rộng không gian tìm kiếm hơn so với Greedy Search để có câu văn tốt hơn.', 'hard', '2025-12-25 14:03:29'),
(143, 13, 'Temperature trong Softmax ảnh hưởng thế nào đến sinh văn bản?', 'Temp cao làm văn bản sáng tạo/ngẫu nhiên hơn', 'Temp cao làm văn bản lặp lại', 'Temp không ảnh hưởng', 'Temp thấp làm văn bản sai ngữ pháp', 'A', 'Temperature cao làm phẳng phân phối xác suất, tăng cơ hội cho các từ ít phổ biến.', 'medium', '2025-12-25 14:03:29'),
(144, 13, 'Chatbot dựa trên luật (Rule-based) hoạt động thế nào?', 'Dùng Deep Learning', 'Dựa trên các quy tắc if-else được định nghĩa trước', 'Tự học từ hội thoại', 'Dùng BERT', 'B', 'Hoạt động cứng nhắc theo kịch bản có sẵn.', 'easy', '2025-12-25 14:03:29'),
(145, 13, 'Zero-shot Learning là gì?', 'Mô hình cần 0 dữ liệu huấn luyện', 'Mô hình có thể thực hiện tác vụ mà chưa từng được huấn luyện trực tiếp trên tác vụ đó', 'Mô hình chạy trong 0 giây', 'Mô hình có 0 tham số', 'B', 'Ví dụ: Dùng GPT-3 để dịch dù chưa fine-tune cho dịch thuật.', 'hard', '2025-12-25 14:03:29'),
(146, 13, 'Hugging Face là gì trong cộng đồng NLP?', 'Một loại Emoji', 'Một nền tảng/thư viện chia sẻ các mô hình Transformer và Dataset', 'Một thuật toán', 'Một loại GPU', 'B', 'Nơi phổ biến nhất để tải và chia sẻ các mô hình NLP hiện đại.', 'medium', '2025-12-25 14:03:29'),
(147, 13, 'Tokenization BPE (Byte Pair Encoding) hoạt động thế nào?', 'Chia theo dấu cách', 'Gộp các cặp ký tự phổ biến nhất lại với nhau lặp đi lặp lại', 'Chia theo âm tiết', 'Chia ngẫu nhiên', 'B', 'Giúp xử lý từ hiếm (OOV) bằng cách chia chúng thành các subword.', 'hard', '2025-12-25 14:03:29'),
(148, 13, 'Regular Expression (Regex) dùng để làm gì?', 'Huấn luyện AI', 'Tìm kiếm và thao tác chuỗi theo mẫu ký tự', 'Vẽ biểu đồ', 'Lưu trữ dữ liệu', 'B', 'Công cụ mạnh mẽ để xử lý chuỗi văn bản thủ công.', 'easy', '2025-12-25 14:03:29'),
(151, 13, 'Topic Modeling (như LDA) là thuật toán thuộc loại nào?', 'Supervised Learning', 'Unsupervised Learning', 'Reinforcement Learning', 'Semi-supervised', 'B', 'Phân nhóm văn bản theo chủ đề mà không cần nhãn trước.', 'medium', '2025-12-25 14:03:29'),
(152, 13, 'Dữ liệu văn bản thường được coi là dữ liệu kiểu gì?', 'Có cấu trúc (Structured)', 'Phi cấu trúc (Unstructured)', 'Bán cấu trúc', 'Dữ liệu nhị phân', 'B', 'Văn bản tự do không có định dạng bảng cột cố định.', 'easy', '2025-12-25 14:03:29'),
(153, 13, 'Kỹ thuật Data Augmentation trong NLP có thể bao gồm?', 'Xoay ảnh', 'Thay thế từ đồng nghĩa (Synonym Replacement) hoặc Dịch ngược (Back-translation)', 'Thay đổi độ sáng', 'Cắt ghép âm thanh', 'B', 'Tạo ra dữ liệu văn bản mới từ dữ liệu cũ để tăng độ đa dạng.', 'hard', '2025-12-25 14:03:29'),
(155, 13, 'GLUE Benchmark là gì?', 'Một bộ dữ liệu huấn luyện', 'Một bộ tiêu chuẩn đánh giá khả năng hiểu ngôn ngữ tự nhiên của các mô hình', 'Một thư viện', 'Một loại keo dán', 'B', 'General Language Understanding Evaluation benchmark.', 'hard', '2025-12-25 14:03:29'),
(156, 13, 'Text Summarization dạng Abstractive (Trừu tượng) là gì?', 'Trích xuất nguyên văn các câu quan trọng', 'Viết lại tóm tắt bằng từ ngữ mới (giống con người)', 'Chỉ lấy từ đầu tiên', 'Chỉ lấy tiêu đề', 'B', 'Abstractive khó hơn Extractive vì cần khả năng sinh ngôn ngữ.', 'medium', '2025-12-25 14:03:29'),
(157, 13, 'Softmax function thường nằm ở đâu trong mô hình phân loại văn bản?', 'Lớp đầu vào', 'Lớp ẩn', 'Lớp đầu ra để tính xác suất', 'Lớp tích chập', 'C', 'Chuyển đổi vector đầu ra thành phân phối xác suất.', 'hard', '2025-12-25 14:03:29'),
(158, 13, 'Unicode là gì?', 'Một phần mềm gõ tiếng Việt', 'Bộ mã ký tự tiêu chuẩn quốc tế bao gồm hầu hết các ngôn ngữ', 'Một loại font chữ', 'Một ngôn ngữ lập trình', 'B', 'Giúp máy tính xử lý văn bản đa ngôn ngữ thống nhất.', 'easy', '2025-12-25 14:03:29'),
(159, 13, 'Teacher Forcing trong huấn luyện RNN là gì?', 'Bắt giáo viên dạy máy', 'Sử dụng đầu ra thực tế (ground truth) của bước trước làm đầu vào cho bước hiện tại thay vì dùng đầu ra dự đoán', 'Phạt mô hình nặng', 'Dùng dữ liệu giả', 'B', 'Giúp mô hình hội tụ nhanh hơn trong giai đoạn đầu.', 'hard', '2025-12-25 14:03:29'),
(160, 13, 'Batch Size ảnh hưởng thế nào đến huấn luyện?', 'Không ảnh hưởng', 'Batch size lớn tốn ít bộ nhớ hơn', 'Batch size quyết định số mẫu dữ liệu được xử lý trước khi cập nhật trọng số một lần', 'Batch size luôn phải là 1', 'C', 'Batch size lớn giúp gradient ổn định hơn nhưng tốn RAM.', 'medium', '2025-12-25 14:03:29'),
(161, 13, 'Transfer Learning giúp ích gì cho các ngôn ngữ ít tài nguyên (Low-resource languages)?', 'Không giúp gì', 'Có thể dùng kiến thức học từ ngôn ngữ giàu tài nguyên để áp dụng sang', 'Làm giảm độ chính xác', 'Chỉ dùng cho tiếng Anh', 'B', 'Ví dụ: Dùng Multilingual BERT để xử lý tiếng Việt.', 'hard', '2025-12-25 14:03:29'),
(163, 13, 'RAG (Retrieval-Augmented Generation) là gì?', 'Tạo văn bản ngẫu nhiên', 'Kết hợp mô hình sinh (Generative) với hệ thống truy vấn thông tin (Retrieval) để tăng độ chính xác', 'Chỉ là tìm kiếm Google', 'Mô hình chỉ biết tóm tắt', 'B', 'Giảm ảo giác (hallucination) bằng cách cung cấp ngữ cảnh thực tế cho LLM.', 'hard', '2025-12-25 14:03:29'),
(164, 13, 'Một văn bản có \'High Polarity\' trong Sentiment Analysis nghĩa là?', 'Rất dài', 'Cảm xúc rất rõ ràng (Rất tích cực hoặc Rất tiêu cực)', 'Không có cảm xúc', 'Nhiều từ lạ', 'B', 'Polarity đo độ cực của cảm xúc.', 'easy', '2025-12-25 14:03:29'),
(165, 13, 'F1-score là trung bình điều hòa của?', 'Accuracy và Loss', 'Precision và Recall', 'TP và TN', 'FP và FN', 'B', 'Dùng khi dữ liệu mất cân bằng (imbalanced data).', 'medium', '2025-12-25 14:03:29'),
(166, 13, 'Vanishing Gradient xảy ra khi dùng hàm kích hoạt nào nhiều lớp?', 'ReLU', 'Sigmoid hoặc Tanh', 'Linear', 'Leaky ReLU', 'B', 'Đạo hàm của Sigmoid nhỏ (<0.25) nên nhân nhiều lần sẽ về 0.', 'hard', '2025-12-25 14:03:29'),
(167, 13, 'ReLU (Rectified Linear Unit) giải quyết vấn đề gì?', 'Overfitting', 'Vanishing Gradient', 'Underfitting', 'Dữ liệu nhiễu', 'B', 'ReLU không bị bão hòa ở miền dương.', 'medium', '2025-12-25 14:03:29'),
(168, 13, 'Positional Encoding trong Transformer dùng để làm gì?', 'Mã hóa từ vựng', 'Cung cấp thông tin về vị trí/thứ tự của từ trong câu vì Transformer xử lý song song', 'Giảm nhiễu', 'Tăng tốc độ', 'B', 'Vì Transformer không có recurrence nên không tự biết thứ tự từ.', 'hard', '2025-12-25 14:03:29'),
(169, 13, 'ASR là viết tắt của?', 'Automatic Speech Recognition (Nhận dạng giọng nói)', 'Automatic System Reboot', 'Advanced Sound Recording', 'AI Speech Robot', 'A', 'Chuyển đổi giọng nói thành văn bản.', 'easy', '2025-12-25 14:03:29'),
(170, 13, 'TTS là viết tắt của?', 'Text To Speech', 'Time To Sleep', 'Text Transfer System', 'Talk To System', 'A', 'Chuyển đổi văn bản thành giọng nói.', 'medium', '2025-12-25 14:03:29'),
(172, 13, 'Ambiguity (Sự mơ hồ) trong NLP là gì?', 'Văn bản quá ngắn', 'Một từ/câu có thể hiểu theo nhiều nghĩa khác nhau', 'Văn bản sai chính tả', 'Máy tính bị lỗi', 'B', 'Ví dụ: \'Bank\' có thể là ngân hàng hoặc bờ sông (Lexical Ambiguity).', 'medium', '2025-12-25 14:03:29'),
(173, 13, 'Coreference Resolution là bài toán gì?', 'Dịch thuật', 'Xác định các từ cùng chỉ về một thực thể (ví dụ: \'Tom\' và \'anh ấy\')', 'Tóm tắt', 'Phân loại', 'B', 'Giải quyết mối quan hệ đại từ nhân xưng.', 'hard', '2025-12-25 14:03:29'),
(174, 13, 'Python string method nào dùng để chuyển chữ hoa thành chữ thường?', '.upper()', '.lower()', '.strip()', '.split()', 'B', 'Tiền xử lý cơ bản.', 'easy', '2025-12-25 14:03:29'),
(176, 13, 'Skip-gram hoạt động tốt hơn CBOW trong trường hợp nào?', 'Dữ liệu nhỏ và từ hiếm', 'Dữ liệu cực lớn', 'Từ phổ biến', 'Khi cần tốc độ nhanh', 'A', 'Skip-gram học ngữ cảnh chi tiết hơn.', 'hard', '2025-12-25 14:03:29'),
(179, 13, 'Prompt Engineering là gì?', 'Sửa lỗi code', 'Kỹ thuật thiết kế câu lệnh đầu vào để tối ưu hóa kết quả từ các mô hình ngôn ngữ lớn (LLM)', 'Thiết kế phần cứng', 'Lập trình web', 'B', 'Kỹ năng quan trọng khi làm việc với GPT, Claude...', 'hard', '2025-12-25 14:03:29'),
(180, 13, 'Hallucination (Ảo giác) trong AI là gì?', 'AI bị virus', 'AI tự tin đưa ra thông tin sai lệch hoặc bịa đặt', 'AI không trả lời được', 'AI chạy quá chậm', 'B', 'Vấn đề lớn của các LLM hiện nay.', 'medium', '2025-12-25 14:03:29'),
(181, 13, 'T5 (Text-to-Text Transfer Transformer) coi mọi bài toán NLP là?', 'Bài toán phân loại', 'Bài toán hồi quy', 'Bài toán chuyển đổi văn bản sang văn bản', 'Bài toán gom nhóm', 'C', 'Dịch, tóm tắt, phân loại đều đưa về dạng text-to-text.', 'hard', '2025-12-25 14:03:29'),
(182, 13, 'Cosine Similarity có giá trị nằm trong khoảng nào?', '0 đến 1', ' -1 đến 1', '0 đến 100', '-vc đến +vc', 'B', '1 là giống hệt, -1 là đối lập, 0 là trực giao.', 'medium', '2025-12-25 14:03:29'),
(185, 13, 'OCR là công nghệ gì?', 'Nhận dạng ký tự quang học (chuyển ảnh chữ thành text)', 'Nhận dạng giọng nói', 'Dịch máy', 'Tóm tắt', 'A', 'Optical Character Recognition.', 'easy', '2025-12-25 14:03:29'),
(188, 13, 'Subword Tokenization giải quyết vấn đề gì?', 'Tách câu sai', 'Vấn đề từ chưa biết (OOV - Out of Vocabulary)', 'Vấn đề ngữ pháp', 'Vấn đề chính tả', 'B', 'Giúp mô hình hiểu được từ ghép hoặc từ mới dựa trên các phần nhỏ hơn.', 'medium', '2025-12-25 14:03:29'),
(189, 13, 'Input của một mô hình NLP thường phải là?', 'Văn bản thô', 'Dữ liệu số (Vector/Tensor)', 'Hình ảnh', 'Âm thanh', 'B', 'Máy tính chỉ tính toán được trên số.', 'easy', '2025-12-25 14:03:29'),
(190, 13, 'LSTM có khả năng ghi nhớ tốt hơn RNN nhờ?', 'Nhiều lớp hơn', 'Trạng thái tế bào (Cell state) chạy xuyên suốt', 'Dùng GPU', 'Dữ liệu ít hơn', 'B', 'Cell state giống như băng chuyền thông tin.', 'hard', '2025-12-25 14:03:29'),
(192, 13, 'GloVe (Global Vectors) khác Word2Vec ở chỗ?', 'Dựa trên mạng nơ-ron', 'Dựa trên phân rã ma trận thống kê tần suất cùng xuất hiện toàn cục', 'GloVe là Deep Learning', 'GloVe xử lý ảnh', 'B', 'Kết hợp ưu điểm của BoW và Word2Vec.', 'hard', '2025-12-25 14:03:29'),
(193, 13, 'Padding trong xử lý batch văn bản dùng để làm gì?', 'Tăng dữ liệu', 'Đảm bảo các câu trong một batch có cùng độ dài (bằng cách thêm số 0)', 'Xóa từ thừa', 'Mã hóa từ', 'B', 'Để xếp chồng thành ma trận tính toán song song.', 'medium', '2025-12-25 14:03:29'),
(194, 13, 'Layer Normalization thường dùng trong Transformer thay vì Batch Normalization vì?', 'Nó nhanh hơn', 'Nó hoạt động tốt hơn với dữ liệu chuỗi có độ dài thay đổi và batch size nhỏ', 'Nó tốn ít RAM', 'Nó dễ cài đặt', 'B', 'Chuẩn hóa theo chiều đặc trưng của từng mẫu thay vì theo batch.', 'hard', '2025-12-25 14:03:29'),
(195, 13, 'AI Winter là thuật ngữ chỉ?', 'Mùa đông lạnh giá', 'Giai đoạn giảm sút sự quan tâm và đầu tư vào AI', 'Một thuật toán làm mát', 'Tên một mô hình', 'B', 'Giai đoạn thoái trào trước khi Deep Learning bùng nổ.', 'easy', '2025-12-25 14:03:29'),
(208, 14, 'N-gram với N=2 được gọi là gì?', 'Unigram', 'Bigram', 'Trigram', 'Skip-gram', 'B', 'Bigram là chuỗi 2 từ liên tiếp.', 'easy', '2025-12-25 14:55:21'),
(209, 14, 'Word Embedding giải quyết vấn đề gì của One-Hot Encoding?', 'Giảm kích thước file', 'Giữ lại ý nghĩa ngữ nghĩa và giảm số chiều vector', 'Tăng tốc độ xử lý', 'Loại bỏ stop words', 'B', 'Word Embedding biểu diễn từ trong không gian vector dày (dense) và bảo toàn ngữ nghĩa.', 'medium', '2025-12-25 14:55:21'),
(210, 14, 'Word2Vec có hai kiến trúc chính là gì?', 'CBOW và Skip-gram', 'RNN và LSTM', 'Encoder và Decoder', 'CNN và RNN', 'A', 'CBOW dự đoán từ trung tâm từ ngữ cảnh, Skip-gram dự đoán ngữ cảnh từ từ trung tâm.', 'hard', '2025-12-25 14:55:21'),
(211, 14, 'Cosine Similarity dùng để làm gì trong NLP?', 'Đo độ dài văn bản', 'Đo mức độ tương đồng giữa hai vector văn bản', 'Đếm số từ', 'Sắp xếp từ điển', 'B', 'Đo góc giữa hai vector để xác định độ tương đồng về ngữ nghĩa.', 'medium', '2025-12-25 14:55:21'),
(213, 14, 'Sentiment Analysis là bài toán gì?', 'Phân tích cú pháp', 'Phân loại cảm xúc (Tích cực/Tiêu cực...)', 'Dịch máy', 'Tóm tắt văn bản', 'B', 'Xác định thái độ/cảm xúc của người viết.', 'easy', '2025-12-25 14:55:21'),
(214, 14, 'Naive Bayes thường được dùng cho tác vụ NLP nào?', 'Dịch máy', 'Phân loại văn bản (ví dụ: lọc spam)', 'Tóm tắt văn bản', 'Hỏi đáp', 'B', 'Naive Bayes rất hiệu quả trong phân loại văn bản dựa trên xác suất.', 'medium', '2025-12-25 14:55:21'),
(216, 14, 'POS Tagging là viết tắt của?', 'Part-of-Speech Tagging', 'Position-of-Sentence Tagging', 'Post-Office Service', 'Processing-of-String', 'A', 'Gán nhãn từ loại (Danh từ, Động từ, Tính từ...) cho từng từ.', 'easy', '2025-12-25 14:55:21'),
(219, 14, 'RNN là viết tắt của?', 'Recursive Neural Network', 'Recurrent Neural Network', 'Random Neural Network', 'Rotational Neural Network', 'B', 'Mạng nơ-ron hồi quy.', 'easy', '2025-12-25 14:55:21'),
(221, 14, 'Vấn đề lớn nhất của RNN truyền thống là gì?', 'Overfitting', 'Vanishing Gradient (Biến mất đạo hàm)', 'Underfitting', 'Tốn bộ nhớ', 'B', 'Khó học được các phụ thuộc xa do đạo hàm tiến về 0 khi lan truyền ngược qua nhiều bước.', 'hard', '2025-12-25 14:55:21'),
(224, 14, 'GRU khác LSTM như thế nào?', 'GRU phức tạp hơn', 'GRU không có cổng Output mà gộp thành cổng Update và Reset', 'GRU chậm hơn', 'GRU có 4 cổng', 'B', 'GRU là phiên bản đơn giản hóa của LSTM, thường huấn luyện nhanh hơn.', 'hard', '2025-12-25 14:55:21'),
(226, 14, 'Seq2Seq (Sequence to Sequence) thường dùng cho?', 'Phân loại ảnh', 'Dịch máy và Chatbot', 'Hồi quy tuyến tính', 'Clustering', 'B', 'Ánh xạ một chuỗi đầu vào sang một chuỗi đầu ra (ví dụ: Tiếng Anh -> Tiếng Việt).', 'medium', '2025-12-25 14:55:21'),
(228, 14, 'Cơ chế Attention giải quyết vấn đề gì của Seq2Seq truyền thống?', 'Tốc độ huấn luyện', 'Vấn đề nút thắt cổ chai (bottleneck) khi dồn nén thông tin vào một vector cố định', 'Thiếu dữ liệu', 'Overfitting', 'B', 'Cho phép Decoder \'nhìn\' vào toàn bộ câu gốc thay vì chỉ vector ngữ cảnh cuối cùng.', 'medium', '2025-12-25 14:55:21'),
(233, 14, 'Fine-tuning trong NLP là gì?', 'Huấn luyện mô hình từ đầu', 'Lấy mô hình đã huấn luyện sẵn (Pre-trained) và huấn luyện thêm trên dữ liệu chuyên biệt', 'Chỉnh sửa code của mô hình', 'Tăng kích thước mô hình', 'B', 'Tinh chỉnh mô hình lớn vào tác vụ cụ thể giúp tiết kiệm thời gian và dữ liệu.', 'medium', '2025-12-25 14:55:21'),
(237, 14, 'ROUGE score thường dùng đánh giá tác vụ nào?', 'Dịch máy', 'Tóm tắt văn bản (Text Summarization)', 'Phân loại tin tức', 'Hỏi đáp', 'B', 'Đo độ phủ của bản tóm tắt máy so với bản tóm tắt mẫu.', 'medium', '2025-12-25 14:55:21'),
(241, 14, 'Beam Search dùng để làm gì trong quá trình sinh văn bản?', 'Tìm từ có xác suất cao nhất (Greedy)', 'Tìm k chuỗi có xác suất cao nhất để chọn lựa', 'Tìm từ ngẫu nhiên', 'Xóa từ lặp', 'B', 'Mở rộng không gian tìm kiếm hơn so với Greedy Search để có câu văn tốt hơn.', 'hard', '2025-12-25 14:55:21'),
(242, 14, 'Temperature trong Softmax ảnh hưởng thế nào đến sinh văn bản?', 'Temp cao làm văn bản sáng tạo/ngẫu nhiên hơn', 'Temp cao làm văn bản lặp lại', 'Temp không ảnh hưởng', 'Temp thấp làm văn bản sai ngữ pháp', 'A', 'Temperature cao làm phẳng phân phối xác suất, tăng cơ hội cho các từ ít phổ biến.', 'medium', '2025-12-25 14:55:21'),
(243, 14, 'Chatbot dựa trên luật (Rule-based) hoạt động thế nào?', 'Dùng Deep Learning', 'Dựa trên các quy tắc if-else được định nghĩa trước', 'Tự học từ hội thoại', 'Dùng BERT', 'B', 'Hoạt động cứng nhắc theo kịch bản có sẵn.', 'easy', '2025-12-25 14:55:21'),
(245, 14, 'Hugging Face là gì trong cộng đồng NLP?', 'Một loại Emoji', 'Một nền tảng/thư viện chia sẻ các mô hình Transformer và Dataset', 'Một thuật toán', 'Một loại GPU', 'B', 'Nơi phổ biến nhất để tải và chia sẻ các mô hình NLP hiện đại.', 'medium', '2025-12-25 14:55:21'),
(250, 14, 'Topic Modeling (như LDA) là thuật toán thuộc loại nào?', 'Supervised Learning', 'Unsupervised Learning', 'Reinforcement Learning', 'Semi-supervised', 'B', 'Phân nhóm văn bản theo chủ đề mà không cần nhãn trước.', 'medium', '2025-12-25 14:55:21'),
(251, 14, 'Dữ liệu văn bản thường được coi là dữ liệu kiểu gì?', 'Có cấu trúc (Structured)', 'Phi cấu trúc (Unstructured)', 'Bán cấu trúc', 'Dữ liệu nhị phân', 'B', 'Văn bản tự do không có định dạng bảng cột cố định.', 'easy', '2025-12-25 14:55:21'),
(255, 14, 'Text Summarization dạng Abstractive (Trừu tượng) là gì?', 'Trích xuất nguyên văn các câu quan trọng', 'Viết lại tóm tắt bằng từ ngữ mới (giống con người)', 'Chỉ lấy từ đầu tiên', 'Chỉ lấy tiêu đề', 'B', 'Abstractive khó hơn Extractive vì cần khả năng sinh ngôn ngữ.', 'medium', '2025-12-25 14:55:21'),
(257, 14, 'Unicode là gì?', 'Một phần mềm gõ tiếng Việt', 'Bộ mã ký tự tiêu chuẩn quốc tế bao gồm hầu hết các ngôn ngữ', 'Một loại font chữ', 'Một ngôn ngữ lập trình', 'B', 'Giúp máy tính xử lý văn bản đa ngôn ngữ thống nhất.', 'easy', '2025-12-25 14:55:21'),
(259, 14, 'Batch Size ảnh hưởng thế nào đến huấn luyện?', 'Không ảnh hưởng', 'Batch size lớn tốn ít bộ nhớ hơn', 'Batch size quyết định số mẫu dữ liệu được xử lý trước khi cập nhật trọng số một lần', 'Batch size luôn phải là 1', 'C', 'Batch size lớn giúp gradient ổn định hơn nhưng tốn RAM.', 'medium', '2025-12-25 14:55:21'),
(262, 14, 'RAG (Retrieval-Augmented Generation) là gì?', 'Tạo văn bản ngẫu nhiên', 'Kết hợp mô hình sinh (Generative) với hệ thống truy vấn thông tin (Retrieval) để tăng độ chính xác', 'Chỉ là tìm kiếm Google', 'Mô hình chỉ biết tóm tắt', 'B', 'Giảm ảo giác (hallucination) bằng cách cung cấp ngữ cảnh thực tế cho LLM.', 'hard', '2025-12-25 14:55:21'),
(263, 14, 'Một văn bản có \'High Polarity\' trong Sentiment Analysis nghĩa là?', 'Rất dài', 'Cảm xúc rất rõ ràng (Rất tích cực hoặc Rất tiêu cực)', 'Không có cảm xúc', 'Nhiều từ lạ', 'B', 'Polarity đo độ cực của cảm xúc.', 'easy', '2025-12-25 14:55:21'),
(264, 14, 'F1-score là trung bình điều hòa của?', 'Accuracy và Loss', 'Precision và Recall', 'TP và TN', 'FP và FN', 'B', 'Dùng khi dữ liệu mất cân bằng (imbalanced data).', 'medium', '2025-12-25 14:55:21'),
(266, 14, 'ReLU (Rectified Linear Unit) giải quyết vấn đề gì?', 'Overfitting', 'Vanishing Gradient', 'Underfitting', 'Dữ liệu nhiễu', 'B', 'ReLU không bị bão hòa ở miền dương.', 'medium', '2025-12-25 14:55:21'),
(267, 14, 'Positional Encoding trong Transformer dùng để làm gì?', 'Mã hóa từ vựng', 'Cung cấp thông tin về vị trí/thứ tự của từ trong câu vì Transformer xử lý song song', 'Giảm nhiễu', 'Tăng tốc độ', 'B', 'Vì Transformer không có recurrence nên không tự biết thứ tự từ.', 'hard', '2025-12-25 14:55:21'),
(268, 14, 'ASR là viết tắt của?', 'Automatic Speech Recognition (Nhận dạng giọng nói)', 'Automatic System Reboot', 'Advanced Sound Recording', 'AI Speech Robot', 'A', 'Chuyển đổi giọng nói thành văn bản.', 'easy', '2025-12-25 14:55:21'),
(269, 14, 'TTS là viết tắt của?', 'Text To Speech', 'Time To Sleep', 'Text Transfer System', 'Talk To System', 'A', 'Chuyển đổi văn bản thành giọng nói.', 'medium', '2025-12-25 14:55:21'),
(271, 14, 'Ambiguity (Sự mơ hồ) trong NLP là gì?', 'Văn bản quá ngắn', 'Một từ/câu có thể hiểu theo nhiều nghĩa khác nhau', 'Văn bản sai chính tả', 'Máy tính bị lỗi', 'B', 'Ví dụ: \'Bank\' có thể là ngân hàng hoặc bờ sông (Lexical Ambiguity).', 'medium', '2025-12-25 14:55:21'),
(272, 14, 'Coreference Resolution là bài toán gì?', 'Dịch thuật', 'Xác định các từ cùng chỉ về một thực thể (ví dụ: \'Tom\' và \'anh ấy\')', 'Tóm tắt', 'Phân loại', 'B', 'Giải quyết mối quan hệ đại từ nhân xưng.', 'hard', '2025-12-25 14:55:21'),
(273, 14, 'Python string method nào dùng để chuyển chữ hoa thành chữ thường?', '.upper()', '.lower()', '.strip()', '.split()', 'B', 'Tiền xử lý cơ bản.', 'easy', '2025-12-25 14:55:21'),
(275, 14, 'Skip-gram hoạt động tốt hơn CBOW trong trường hợp nào?', 'Dữ liệu nhỏ và từ hiếm', 'Dữ liệu cực lớn', 'Từ phổ biến', 'Khi cần tốc độ nhanh', 'A', 'Skip-gram học ngữ cảnh chi tiết hơn.', 'hard', '2025-12-25 14:55:21'),
(278, 14, 'Prompt Engineering là gì?', 'Sửa lỗi code', 'Kỹ thuật thiết kế câu lệnh đầu vào để tối ưu hóa kết quả từ các mô hình ngôn ngữ lớn (LLM)', 'Thiết kế phần cứng', 'Lập trình web', 'B', 'Kỹ năng quan trọng khi làm việc với GPT, Claude...', 'hard', '2025-12-25 14:55:21'),
(279, 14, 'Hallucination (Ảo giác) trong AI là gì?', 'AI bị virus', 'AI tự tin đưa ra thông tin sai lệch hoặc bịa đặt', 'AI không trả lời được', 'AI chạy quá chậm', 'B', 'Vấn đề lớn của các LLM hiện nay.', 'medium', '2025-12-25 14:55:21'),
(280, 14, 'T5 (Text-to-Text Transfer Transformer) coi mọi bài toán NLP là?', 'Bài toán phân loại', 'Bài toán hồi quy', 'Bài toán chuyển đổi văn bản sang văn bản', 'Bài toán gom nhóm', 'C', 'Dịch, tóm tắt, phân loại đều đưa về dạng text-to-text.', 'hard', '2025-12-25 14:55:21'),
(281, 14, 'Cosine Similarity có giá trị nằm trong khoảng nào?', '0 đến 1', ' -1 đến 1', '0 đến 100', '-vc đến +vc', 'B', '1 là giống hệt, -1 là đối lập, 0 là trực giao.', 'medium', '2025-12-25 14:55:21'),
(284, 14, 'OCR là công nghệ gì?', 'Nhận dạng ký tự quang học (chuyển ảnh chữ thành text)', 'Nhận dạng giọng nói', 'Dịch máy', 'Tóm tắt', 'A', 'Optical Character Recognition.', 'easy', '2025-12-25 14:55:21'),
(287, 14, 'Subword Tokenization giải quyết vấn đề gì?', 'Tách câu sai', 'Vấn đề từ chưa biết (OOV - Out of Vocabulary)', 'Vấn đề ngữ pháp', 'Vấn đề chính tả', 'B', 'Giúp mô hình hiểu được từ ghép hoặc từ mới dựa trên các phần nhỏ hơn.', 'medium', '2025-12-25 14:55:21'),
(288, 14, 'Input của một mô hình NLP thường phải là?', 'Văn bản thô', 'Dữ liệu số (Vector/Tensor)', 'Hình ảnh', 'Âm thanh', 'B', 'Máy tính chỉ tính toán được trên số.', 'easy', '2025-12-25 14:55:21'),
(289, 14, 'LSTM có khả năng ghi nhớ tốt hơn RNN nhờ?', 'Nhiều lớp hơn', 'Trạng thái tế bào (Cell state) chạy xuyên suốt', 'Dùng GPU', 'Dữ liệu ít hơn', 'B', 'Cell state giống như băng chuyền thông tin.', 'hard', '2025-12-25 14:55:21'),
(291, 14, 'GloVe (Global Vectors) khác Word2Vec ở chỗ?', 'Dựa trên mạng nơ-ron', 'Dựa trên phân rã ma trận thống kê tần suất cùng xuất hiện toàn cục', 'GloVe là Deep Learning', 'GloVe xử lý ảnh', 'B', 'Kết hợp ưu điểm của BoW và Word2Vec.', 'hard', '2025-12-25 14:55:21'),
(292, 14, 'Padding trong xử lý batch văn bản dùng để làm gì?', 'Tăng dữ liệu', 'Đảm bảo các câu trong một batch có cùng độ dài (bằng cách thêm số 0)', 'Xóa từ thừa', 'Mã hóa từ', 'B', 'Để xếp chồng thành ma trận tính toán song song.', 'medium', '2025-12-25 14:55:21'),
(293, 14, 'Layer Normalization thường dùng trong Transformer thay vì Batch Normalization vì?', 'Nó nhanh hơn', 'Nó hoạt động tốt hơn với dữ liệu chuỗi có độ dài thay đổi và batch size nhỏ', 'Nó tốn ít RAM', 'Nó dễ cài đặt', 'B', 'Chuẩn hóa theo chiều đặc trưng của từng mẫu thay vì theo batch.', 'hard', '2025-12-25 14:55:21'),
(295, 15, 'Cấu trúc dữ liệu là gì?', 'Cách lưu file', 'Cách tổ chức dữ liệu trong bộ nhớ', 'Cách nhập dữ liệu', 'Cách xuất dữ liệu', 'B', 'CTDL tập trung vào cách tổ chức và quản lý dữ liệu để xử lý hiệu quả.', 'easy', '2025-12-25 15:09:02'),
(296, 15, 'Big-O dùng để làm gì?', 'Đo dung lượng RAM', 'Đo độ phức tạp thuật toán', 'Đo thời gian thực tế', 'Đo số dòng code', 'B', 'Big-O mô tả tốc độ tăng của thời gian hoặc bộ nhớ theo kích thước input.', 'easy', '2025-12-25 15:09:02'),
(297, 15, 'O(1) nghĩa là gì?', 'Phụ thuộc dữ liệu', 'Chạy chậm', 'Thời gian không đổi', 'Luôn nhanh nhất', 'C', 'Thời gian thực thi không phụ thuộc số lượng phần tử.', 'easy', '2025-12-25 15:09:02'),
(298, 15, 'Thuật toán nào có độ phức tạp O(n^2)?', 'Bubble Sort', 'Binary Search', 'Merge Sort', 'Quick Sort', 'A', 'Bubble Sort dùng hai vòng lặp lồng nhau.', 'easy', '2025-12-25 15:09:02'),
(299, 15, 'Độ phức tạp nào là tốt nhất?', 'O(n^2)', 'O(n log n)', 'O(2^n)', 'O(n!)', 'B', 'O(n log n) hiệu quả hơn nhiều so với các lựa chọn còn lại.', 'easy', '2025-12-25 15:09:02'),
(300, 15, 'Ưu điểm lớn nhất của mảng là gì?', 'Thêm nhanh', 'Xóa nhanh', 'Truy cập nhanh', 'Không tốn bộ nhớ', 'C', 'Mảng cho phép truy cập trực tiếp qua chỉ số.', 'easy', '2025-12-25 15:09:02'),
(301, 15, 'Nhược điểm của mảng?', 'Truy cập chậm', 'Kích thước cố định', 'Tốn thời gian duyệt', 'Không sắp xếp được', 'B', 'Mảng tĩnh không thể thay đổi kích thước.', 'easy', '2025-12-25 15:09:02'),
(302, 15, 'Danh sách liên kết khác mảng ở điểm nào?', 'Lưu liên tiếp', 'Truy cập ngẫu nhiên', 'Có con trỏ', 'Tốn ít bộ nhớ', 'C', 'Các node liên kết với nhau bằng con trỏ.', 'easy', '2025-12-25 15:09:02'),
(303, 15, 'Thao tác nào nhanh ở DSLK?', 'Truy cập', 'Tìm kiếm', 'Thêm/xóa đầu', 'Sắp xếp', 'C', 'Không cần dồn phần tử như mảng.', 'easy', '2025-12-25 15:09:02'),
(304, 15, 'DSLK đơn có bao nhiêu con trỏ?', '0', '1', '2', '3', 'B', 'Mỗi node trỏ tới node kế tiếp.', 'easy', '2025-12-25 15:09:02'),
(305, 15, 'Stack hoạt động theo nguyên lý?', 'FIFO', 'LIFO', 'Random', 'Priority', 'B', 'Vào sau ra trước.', 'easy', '2025-12-25 15:09:02'),
(306, 15, 'Queue hoạt động theo nguyên lý?', 'LIFO', 'LILO', 'FIFO', 'FILO', 'C', 'Vào trước ra trước.', 'easy', '2025-12-25 15:09:02'),
(307, 15, 'Push là thao tác của cấu trúc nào?', 'Queue', 'Stack', 'Tree', 'Graph', 'B', 'Push dùng để thêm phần tử vào stack.', 'easy', '2025-12-25 15:09:02'),
(308, 15, 'Ứng dụng của stack?', 'BFS', 'DFS', 'Infix sang Postfix', 'Hashing', 'C', 'Stack thường dùng xử lý biểu thức.', 'easy', '2025-12-25 15:09:02'),
(309, 15, 'Queue thường dùng trong thuật toán nào?', 'DFS', 'Đệ quy', 'BFS', 'Sắp xếp', 'C', 'BFS sử dụng hàng đợi.', 'easy', '2025-12-25 15:09:02'),
(310, 15, 'Cây nhị phân có tối đa bao nhiêu con?', '1', '2', '3', 'Không giới hạn', 'B', 'Mỗi node có tối đa 2 con.', 'medium', '2025-12-25 15:09:02'),
(311, 15, 'Duyệt NLR là kiểu nào?', 'Inorder', 'Postorder', 'Preorder', 'Levelorder', 'C', 'Node – Left – Right.', 'medium', '2025-12-25 15:09:02'),
(312, 15, 'Tính chất của BST?', 'Trái > gốc', 'Phải < gốc', 'Trái < gốc < phải', 'Không có quy tắc', 'C', 'BST đảm bảo thứ tự để tìm kiếm nhanh.', 'medium', '2025-12-25 15:09:02'),
(313, 15, 'Độ phức tạp tìm kiếm trong BST cân bằng?', 'O(n)', 'O(log n)', 'O(n log n)', 'O(1)', 'B', 'Cây cân bằng giúp giảm chiều cao.', 'medium', '2025-12-25 15:09:02'),
(314, 15, 'Heap là gì?', 'Danh sách', 'Cây nhị phân đặc biệt', 'Đồ thị', 'Stack', 'B', 'Heap thỏa mãn tính chất max-heap hoặc min-heap.', 'medium', '2025-12-25 15:09:02'),
(315, 15, 'Max-Heap có đặc điểm?', 'Node cha nhỏ hơn con', 'Node cha lớn hơn con', 'Không có quy tắc', 'Chỉ có 1 con', 'B', 'Giá trị lớn nhất nằm ở gốc.', 'medium', '2025-12-25 15:09:02'),
(316, 15, 'DFS dùng cấu trúc nào?', 'Queue', 'Stack', 'Array', 'Heap', 'B', 'DFS dùng stack hoặc đệ quy.', 'medium', '2025-12-25 15:09:02'),
(317, 15, 'BFS dùng cấu trúc nào?', 'Stack', 'Queue', 'Heap', 'Tree', 'B', 'BFS duyệt theo mức.', 'medium', '2025-12-25 15:09:02'),
(318, 15, 'Đồ thị G(V', 'E) gồm?', 'Đỉnh và cạnh', 'Node và cây', 'Stack và queue', 'KEY Và VAL', 'A', 'medium', '2025-12-25 15:09:02'),
(319, 15, 'Đồ thị có hướng là?', 'Cạnh không chiều', 'Cạnh có chiều', 'Cây', 'Heap', 'B', 'Mỗi cạnh có hướng xác định.', 'medium', '2025-12-25 15:09:02'),
(320, 15, 'Ma trận kề dùng để?', 'Lưu đồ thị', 'Tìm kiếm', 'Tính Big-O', 'Sắp xếp', 'A', 'Ma trận kề biểu diễn quan hệ giữa các đỉnh.', 'medium', '2025-12-25 15:09:02'),
(321, 15, 'Quick Sort trung bình có độ phức tạp?', 'O(n)', 'O(n log n)', 'O(n^2)', 'O(log n)', 'B', 'Chia để trị cho hiệu suất tốt.', 'hard', '2025-12-25 15:09:02'),
(322, 15, 'Trường hợp xấu nhất của Quick Sort?', 'O(n log n)', 'O(n)', 'O(n^2)', 'O(log n)', 'C', 'Xảy ra khi chọn pivot kém.', 'hard', '2025-12-25 15:09:02'),
(323, 15, 'Merge Sort có ưu điểm?', 'In-place', 'Ổn định', 'Ít bộ nhớ', 'Code ngắn', 'B', 'Merge Sort là thuật toán ổn định.', 'hard', '2025-12-25 15:09:02'),
(324, 15, 'Merge Sort có nhược điểm?', 'Chậm', 'Tốn bộ nhớ phụ', 'Không ổn định', 'Chỉ sắp xếp số', 'B', 'Cần mảng phụ khi trộn.', 'hard', '2025-12-25 15:09:02'),
(325, 15, 'Thuật toán nào là Divide and Conquer?', 'Bubble Sort', 'Insertion Sort', 'Merge Sort', 'Selection Sort', 'C', 'Merge Sort chia nhỏ bài toán.', 'hard', '2025-12-25 15:09:02'),
(326, 15, 'Hash table có độ phức tạp tìm kiếm trung bình?', 'O(n)', 'O(log n)', 'O(1)', 'O(n log n)', 'C', 'Nhờ hàm băm.', 'hard', '2025-12-25 15:09:02'),
(327, 15, 'Collision trong hashing là gì?', 'Lỗi chương trình', 'Hai khóa cùng vị trí', 'Hết bộ nhớ', 'Tràn số', 'B', 'Hai key ánh xạ cùng chỉ số.', 'hard', '2025-12-25 15:09:02'),
(328, 15, 'Linear probing dùng để?', 'Sắp xếp', 'Xử lý va chạm', 'Giảm bộ nhớ', 'Tăng tốc CPU', 'B', 'Là kỹ thuật xử lý collision.', 'hard', '2025-12-25 15:09:02'),
(329, 15, 'Đệ quy cần điều kiện gì?', 'Không cần', 'Có điểm dừng', 'Có vòng lặp', 'Luôn nhanh', 'B', 'Tránh lặp vô hạn.', 'hard', '2025-12-25 15:09:02'),
(330, 15, 'Độ phức tạp Fibonacci đệ quy thuần?', 'O(n)', 'O(log n)', 'O(2^n)', 'O(n log n)', 'C', 'Gọi lại rất nhiều lần.', 'hard', '2025-12-25 15:09:02'),
(331, 15, 'Dynamic Programming giải quyết vấn đề gì?', 'Tính toán song song', 'Bài toán con trùng lặp', 'Sắp xếp', 'Tìm kiếm', 'B', 'Lưu kết quả để tối ưu.', 'hard', '2025-12-25 15:09:02'),
(332, 15, 'Thuật toán Dijkstra dùng cho?', 'Cây', 'BFS', 'Tìm đường đi ngắn nhất', 'Sắp xếp', 'C', 'Áp dụng cho đồ thị trọng số dương.', 'hard', '2025-12-25 15:09:02'),
(333, 15, 'Độ phức tạp Dijkstra (heap)?', 'O(n^2)', 'O(E log V)', 'O(V^2)', 'O(log n)', 'B', 'Dùng priority queue.', 'hard', '2025-12-25 15:09:02'),
(334, 15, 'Topological Sort áp dụng cho?', 'Đồ thị có chu trình', 'Đồ thị vô hướng', 'Đồ thị DAG', 'Cây nhị phân', 'C', 'Chỉ dùng cho đồ thị có hướng không chu trình.', 'hard', '2025-12-25 15:09:02'),
(335, 15, 'AVL Tree khác BST ở điểm nào?', 'Không cân bằng', 'Tự cân bằng', 'Chậm hơn', 'Ít node', 'B', 'AVL tự cân bằng để tối ưu tìm kiếm.', 'hard', '2025-12-25 15:09:02'),
(336, 15, 'easy', '', '', '', '', '', NULL, 'medium', '2025-12-26 08:43:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `questions_backup_old`
--

CREATE TABLE `questions_backup_old` (
  `id` int NOT NULL,
  `quiz_id` int DEFAULT NULL,
  `question_text` text COLLATE utf8mb4_general_ci,
  `type` enum('single_choice','multiple_choice','true_false') COLLATE utf8mb4_general_ci DEFAULT 'single_choice',
  `level` enum('easy','medium','hard') COLLATE utf8mb4_general_ci DEFAULT 'medium'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `time_limit` int DEFAULT '45'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `created_at`, `time_limit`) VALUES
(13, 15, 'Kiểm tra cuối kì', '2025-12-25 21:03:28', 90),
(14, 15, 'Kiểm tra giữa kì', '2025-12-25 21:55:21', 45),
(15, 17, 'Kiểm tra cuối kì', '2025-12-25 22:09:02', 30);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `submit_time` datetime DEFAULT NULL,
  `status` enum('doing','submitted') COLLATE utf8mb4_general_ci DEFAULT 'doing',
  `score` float DEFAULT NULL,
  `question_order` text COLLATE utf8mb4_general_ci,
  `option_order` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `start_time`, `submit_time`, `status`, `score`, `question_order`, `option_order`) VALUES
(1, 23, 5, '2025-12-22 10:25:19', '2025-12-22 10:25:31', 'submitted', 2, NULL, NULL),
(2, 17, 5, '2025-12-22 10:26:01', '2025-12-22 10:26:06', 'submitted', 1, NULL, NULL),
(3, 22, 2, '2025-12-22 10:33:38', '2025-12-22 10:33:46', 'submitted', NULL, '[\"12\",\"15\",\"13\",\"14\",\"11\"]', '{\"12\":[\"48\",\"46\",\"47\",\"45\"],\"15\":[\"60\",\"59\",\"57\",\"58\"],\"13\":[\"52\",\"50\",\"49\",\"51\"],\"14\":[\"54\",\"56\",\"53\",\"55\"],\"11\":[\"44\",\"41\",\"43\",\"42\"]}'),
(4, 22, 3, '2025-12-22 10:34:14', '2025-12-22 10:37:42', 'submitted', NULL, '[\"16\",\"17\",\"18\",\"20\",\"19\"]', '{\"16\":[\"61\",\"64\",\"63\",\"62\"],\"17\":[\"65\",\"67\",\"68\",\"66\"],\"18\":[\"70\",\"71\",\"72\",\"69\"],\"20\":[\"80\",\"77\",\"79\",\"78\"],\"19\":[\"73\",\"74\",\"76\",\"75\"]}'),
(5, 18, 13, '2025-12-25 23:02:39', NULL, 'doing', NULL, NULL, NULL),
(6, 17, 15, '2025-12-26 15:43:04', NULL, 'doing', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `score` int NOT NULL,
  `total_questions` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `quiz_id`, `score`, `total_questions`, `created_at`) VALUES
(12, 17, 13, 0, 15, '2025-12-25 14:17:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `review` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `course_id`, `rating`, `review`, `created_at`) VALUES
(1, 17, 16, 5, 'hay', '2025-12-11 21:46:53'),
(2, 17, 15, 5, 'Ổn', '2025-12-11 21:54:40'),
(3, 17, 14, 5, 'quá tuyệt vời', '2025-12-11 22:01:21'),
(4, 17, 13, 5, 'Quas hay', '2025-12-12 09:48:02'),
(5, 27, 9, 5, 'hay', '2025-12-17 21:48:21'),
(6, 22, 17, 5, 'hay', '2025-12-22 10:26:57'),
(7, 17, 18, 5, 'hay', '2025-12-26 00:19:08'),
(8, 29, 17, 5, 'hung', '2026-05-23 20:01:35'),
(9, 29, 17, 2, 'okk', '2026-05-23 20:03:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teacher_contracts`
--

CREATE TABLE `teacher_contracts` (
  `id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `contract_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `revenue_share` int DEFAULT '70',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','expired','terminated') COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `teacher_contracts`
--

INSERT INTO `teacher_contracts` (`id`, `teacher_id`, `contract_code`, `file_path`, `revenue_share`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 23, 'HD-12/2024', '', 40, '2025-11-01', '2030-11-01', 'active', '2025-12-11 21:07:05'),
(2, 24, 'HD-12/2025', '', 50, '2025-12-01', '2030-12-01', 'active', '2025-12-12 02:40:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` enum('admins','teacher','student') COLLATE utf8mb4_general_ci DEFAULT 'student',
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `role`, `avatar`, `created_at`) VALUES
(1, 'Hệ thống', 'system@local', 'SYSTEM', 'admins', NULL, '2025-12-22 03:32:39'),
(13, 'anhtai', 'anhtai@gmail.com', '$2y$10$eQtP3mgf6I4d5uY.MkZnfePJNeTXwJ0TOLzuArVwLNtLatQ7fKtwe', 'student', NULL, '2025-12-09 12:05:09'),
(17, 'học viên 1', 'hocvien1@gmail.com', '$2y$10$itCcx1THBY6yH1WR.MBtbubEqBUCRNbHobhd.zvBPjjIZcJpVRStq', 'student', 'uploads/avatars/user_17_1766371681.jpg', '2025-12-10 14:08:23'),
(18, 'học viên 2', 'hocvien2@gmail.com', '$2y$10$9Nk1/WLKRPZLzrvxk/xQ0OEOhzr9tzLpYOql8cEA0jebL8l90z6/G', 'student', NULL, '2025-12-10 14:08:39'),
(19, 'học viên 3', 'hocvien3@gmail.com', '$2y$10$IjOKWaRHwKAmOFWHBPFFjuD2fmZ8Fm2N486eDidWNeH.mjyYy9E.m', 'student', NULL, '2025-12-10 14:08:55'),
(20, 'học viên 4', 'hocvien4@gmail.com', '$2y$10$QL.8SN/an5jPsYe8FxpiPOpgOA/ZLcITXTCXAE8Fce/6OWV7KuQQu', 'student', NULL, '2025-12-10 14:09:11'),
(22, 'học viên 5', 'hocvien5@gmail.com', '$2y$10$2L8gwnwCJc.lXjffx/SX8OOVWXO.wn7ByUeAm6ct63odzBRKZje3u', 'student', 'uploads/avatars/user_22_1766375170.jpg', '2025-12-10 14:09:56'),
(23, 'Giáo Viên 1', 'giaovien1@gmail.com', '$2y$10$YtuVrvPCqUyKNsLleut1.ePjxfonl.Fw0RwpiPqpE2Gy5pIlSW8G6', 'admins', NULL, '2025-12-10 14:10:38'),
(24, 'Giáo Viên 2', 'giaovien2@gmail.com', '$2y$10$5wUQGr/TuNSMZdAjPJB07e8SQahLZkplXolVovhOibulcU0D4O5Aq', 'teacher', NULL, '2025-12-10 14:10:54'),
(25, 'Giáo Viên 3', 'giaovien3@gmail.com', '$2y$10$IltOuv4onrLo45Kyl2bTHeHNo3ZRm2m5FW5bMilwzh1tUtCtWIL1S', 'teacher', NULL, '2025-12-10 14:11:11'),
(26, 'Giáo Viên 4', 'giaovien4@gmail.com', '$2y$10$0X60c8Hkl6mQKh4jrBt2ked88CAOXyYQgm7O9z5xuqq67cTQ34Y32', 'admins', NULL, '2025-12-10 14:11:26'),
(27, 'Giáo Viên 5', 'giaovien5@gmail.com', '$2y$10$P1bIGsvMscqzkxho0b1IpeQcM4GWxTJgYprIlfSitTsiuZ6Xy6e..', 'teacher', NULL, '2025-12-10 14:11:42'),
(28, 'Nguyễn Văn A', 'a@gmail.com', '$2y$10$TBnCNBhwYASj5niFuy4CWetA9zQGh1e63UsRCpXnzBsb0Q8Rc8/yy', 'student', NULL, '2025-12-26 00:35:09'),
(29, 'phamvanh', 'vanhung@gmail.com', '$2y$10$vIw9eCf2yJAafAF6VTeoKOLyAD6PYf17cGS39AADdmb/MGxuaOFkW', 'student', NULL, '2026-05-23 17:09:25');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_attempt` (`quiz_attempt_id`),
  ADD KEY `idx_question` (`question_id`),
  ADD KEY `idx_option` (`option_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_course_category` (`category_id`);

--
-- Chỉ mục cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Chỉ mục cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Chỉ mục cho bảng `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lessons_course` (`course_id`);

--
-- Chỉ mục cho bảng `lesson_materials`
--
ALTER TABLE `lesson_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Chỉ mục cho bảng `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Chỉ mục cho bảng `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Chỉ mục cho bảng `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Chỉ mục cho bảng `questions_backup_old`
--
ALTER TABLE `questions_backup_old`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_questions_quiz` (`quiz_id`);

--
-- Chỉ mục cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_quizzes_course` (`course_id`);

--
-- Chỉ mục cho bảng `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attempt` (`user_id`,`quiz_id`);

--
-- Chỉ mục cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Chỉ mục cho bảng `teacher_contracts`
--
ALTER TABLE `teacher_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `lesson_materials`
--
ALTER TABLE `lesson_materials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `options`
--
ALTER TABLE `options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=958;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=337;

--
-- AUTO_INCREMENT cho bảng `questions_backup_old`
--
ALTER TABLE `questions_backup_old`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=240;

--
-- AUTO_INCREMENT cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `teacher_contracts`
--
ALTER TABLE `teacher_contracts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `fk_answers_attempt` FOREIGN KEY (`quiz_attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_answers_option` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_answers_question` FOREIGN KEY (`question_id`) REFERENCES `questions_backup_old` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_answers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Các ràng buộc cho bảng `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Các ràng buộc cho bảng `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `fk_results_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_results_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `fk_lessons_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lesson_materials`
--
ALTER TABLE `lesson_materials`
  ADD CONSTRAINT `lesson_materials_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions_backup_old` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Các ràng buộc cho bảng `questions_backup_old`
--
ALTER TABLE `questions_backup_old`
  ADD CONSTRAINT `fk_questions_quiz` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_quizzes_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Các ràng buộc cho bảng `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD CONSTRAINT `quiz_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quiz_results_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Các ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Các ràng buộc cho bảng `teacher_contracts`
--
ALTER TABLE `teacher_contracts`
  ADD CONSTRAINT `teacher_contracts_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
