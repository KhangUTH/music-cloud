-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th7 04, 2025 lúc 09:25 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `music_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `popular` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `artists`
--

INSERT INTO `artists` (`id`, `name`, `description`, `avatar`, `popular`) VALUES
(11, 'Thái Hoàng', 'DJ Producer', 'assets/uploads/artist_1751552461_1013.png', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `music_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `genres`
--

CREATE TABLE `genres` (
  `id` int(30) NOT NULL,
  `genre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `cover_photo` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `genres`
--

INSERT INTO `genres` (`id`, `genre`, `description`, `cover_photo`, `date_created`) VALUES
(4, 'US - UK', 'Nhạc đến từ nửa kia Trái Đất', '1750951680_usuk.jpg', '2025-06-26 22:28:41'),
(5, 'Ballad', 'Updating...', '1750951800_ballad.png', '2025-06-26 22:30:52'),
(6, 'Remix', 'Nhạc giật tung nóc, quẩy tưng bừng cùng bạn bè và gia đình ~~', '1750951860_remix.jpg', '2025-06-26 22:31:25'),
(7, 'Bolero', 'Dòng nhạc gắn liền với bao tuổi thơ của nhiều thế hệ 🎵', '1750951920_bolero.png', '2025-06-26 22:32:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `playlist`
--

CREATE TABLE `playlist` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `cover_image` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `playlist`
--

INSERT INTO `playlist` (`id`, `user_id`, `title`, `description`, `cover_image`, `date_created`) VALUES
(7, 6, 'My Favourite', 'Danh sách yêu thích', 'play.jpg', '2025-07-01 10:38:39'),
(9, 5, 'demo', 'demo', 'play.jpg', '2025-07-01 11:45:36'),
(10, 5, 'demo2', 'danh sách nhạc để chạy demo', '1751553660_OIP.png', '2025-07-03 21:41:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `playlist_items`
--

CREATE TABLE `playlist_items` (
  `id` int(30) NOT NULL,
  `playlist_id` int(30) NOT NULL,
  `music_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `playlist_items`
--

INSERT INTO `playlist_items` (`id`, `playlist_id`, `music_id`, `date_created`, `added_at`) VALUES
(1, 6, 1, '2020-11-20 08:52:51', '2025-07-01 11:47:18'),
(2, 2, 2, '2020-11-20 08:58:44', '2025-07-01 11:47:18'),
(3, 3, 2, '2020-11-20 08:59:46', '2025-07-01 11:47:18'),
(4, 3, 1, '2020-11-20 08:59:46', '2025-07-01 11:47:18'),
(5, 4, 5, '2025-06-30 22:16:10', '2025-07-01 11:47:18'),
(10, 5, 3, '2025-07-01 10:28:38', '2025-07-01 11:47:18'),
(13, 5, 5, '2025-07-01 10:28:53', '2025-07-01 11:47:18'),
(14, 5, 4, '2025-07-01 10:28:53', '2025-07-01 11:47:18'),
(15, 5, 10, '2025-07-01 10:28:53', '2025-07-01 11:47:18'),
(16, 8, 5, '2025-07-01 10:53:00', '2025-07-01 11:47:18'),
(17, 8, 3, '2025-07-01 10:53:00', '2025-07-01 11:47:18'),
(21, 9, 7, '2025-07-03 16:38:13', '2025-07-03 16:38:13'),
(22, 9, 6, '2025-07-03 16:38:13', '2025-07-03 16:38:13'),
(23, 9, 14, '2025-07-04 14:20:11', '2025-07-04 14:20:11'),
(24, 9, 28, '2025-07-04 14:20:32', '2025-07-04 14:20:32'),
(25, 9, 23, '2025-07-04 14:20:37', '2025-07-04 14:20:37'),
(26, 9, 19, '2025-07-04 14:20:41', '2025-07-04 14:20:41'),
(27, 9, 17, '2025-07-04 14:20:48', '2025-07-04 14:20:48'),
(28, 9, 39, '2025-07-04 14:20:55', '2025-07-04 14:20:55'),
(29, 10, 29, '2025-07-04 14:21:43', '2025-07-04 14:21:43'),
(30, 10, 37, '2025-07-04 14:21:49', '2025-07-04 14:21:49'),
(31, 10, 55, '2025-07-04 14:21:52', '2025-07-04 14:21:52'),
(32, 10, 21, '2025-07-04 14:21:54', '2025-07-04 14:21:54'),
(33, 10, 47, '2025-07-04 14:21:57', '2025-07-04 14:21:57'),
(34, 10, 63, '2025-07-04 14:22:02', '2025-07-04 14:22:02'),
(35, 10, 61, '2025-07-04 14:22:06', '2025-07-04 14:22:06'),
(36, 10, 60, '2025-07-04 14:22:08', '2025-07-04 14:22:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `song_comments`
--

CREATE TABLE `song_comments` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int(1) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `song_comments`
--

INSERT INTO `song_comments` (`id`, `song_id`, `user_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 4, NULL, 'Khách', 5, 'nhạc quá hay', '2025-07-01 22:52:56'),
(2, 3, NULL, 'Khách', 4, 'higyu', '2025-07-01 22:57:40'),
(3, 4, NULL, 'Khách', 5, 'idol', '2025-07-02 21:03:17'),
(5, 5, 5, NULL, 5, 'nhạc quá hay', '2025-07-03 15:31:07'),
(10, 5, 5, NULL, 5, 'idol', '2025-07-03 15:35:57'),
(11, 5, 5, NULL, 5, 'nhạc quá hay', '2025-07-03 15:38:08'),
(13, 5, 6, NULL, 5, 'nhạc quá hay', '2025-07-03 15:42:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `uploads`
--

CREATE TABLE `uploads` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `genre_id` int(30) NOT NULL,
  `title` text NOT NULL,
  `artist` text NOT NULL,
  `description` text NOT NULL,
  `upath` text NOT NULL,
  `cover_image` text NOT NULL,
  `date_created` int(11) NOT NULL DEFAULT current_timestamp(),
  `artist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `uploads`
--

INSERT INTO `uploads` (`id`, `user_id`, `genre_id`, `title`, `artist`, `description`, `upath`, `cover_image`, `date_created`, `artist_id`) VALUES
(8, 5, 4, 'Faded', 'Alan Waker', '', '1751539020_Alan Walker  Faded.mp3', '1751539020_faded.png', 2147483647, NULL),
(10, 5, 6, 'Những lời dối gian', 'Thái Hoàng', '', '1751539440_Những Lời Dối Gian DJ Thái Hoàng Remix HOÀNG THÁI BẢO.mp3', '1751539440_nhungloidoigian.png', 2147483647, 11),
(11, 5, 6, 'Người tình mùa đông', 'Tuki', '', '1751539620_Người Tình Mùa Đông - TUKI REMIX (Reup làm chó sủa gâu gâu).mp3', '1751539620_tuki.png', 2147483647, NULL),
(12, 5, 6, 'Phố xa', 'Chicks', '', '1751539800_phố xa - chicks remix.mp3', '1751539800_phoxa.png', 2147483647, 11),
(13, 5, 6, 'Một lần dang dở', 'Long APC', '', '1751540040_MỘT LẦN DANG DỞ REMIX - VA26 - LONG APC 🎧.mp3', '1751540040_1landangdo.png', 2147483647, NULL),
(14, 5, 6, 'Anh cho em mùa xuân', 'Long APC', '', '1751540280_Anh Cho Em Mùa Xuân Remix X LONG - APC 🎧.mp3', '1751540280_anhchoemmuaxuan.png', 2147483647, NULL),
(15, 5, 6, 'Tại em gian dối', 'Thái Hoàng', '', '1751540400_Tại Em Gian Dối 2019 - Thái Hoàng Mix.mp3', '1751540400_taiemgiandoi.png', 2147483647, 11),
(17, 5, 6, 'Dối lòng', 'Thái Hoàng', '', '1751540760_Doi Long - DJ Thai Hoang FULL ban chuan 2018 phe nhac nguoi.mp3', '1751540760_nhungloidoigian.png', 2147483647, 11),
(19, 5, 6, 'Dấu yêu', 'Thái Hoàng', '', '1751542140_Dấu Yêu - Thái Hoàng Remix (Ver 2).mp3', '1751542140_nhungloidoigian.png', 2147483647, 11),
(20, 5, 6, 'Vùng trời bình yên', 'Thái Hoàng', '', '1751542320_Vung Troi Binh Yen (Thai Hoang Remix).mp3', '1751542320_taiemgiandoi.png', 2147483647, 11),
(21, 5, 6, 'Muộn', 'Thái Hoàng', '', '1751542380_Muộn - Thái Hoàng Remix.mp3', '1751542380_muon.png', 2147483647, NULL),
(22, 5, 6, 'Trái tim của gió', 'Thái Hoàng', '', '1751542560_TRAI TIM CUA GIO - THAI HOANG REMIX.mp3', '1751542560_taiemgiandoi.png', 2147483647, NULL),
(23, 5, 6, 'chia đôi con đường', 'Pipo', '', '1751542680_Chia Đôi Con Đường Full - PiPo Remix.mp3', '1751542680_chiadoiconduong.png', 2147483647, NULL),
(24, 5, 6, 'Đám cưới trên đường quê', 'Future', '', '1751542800_Lưu Chí Vỹ & Saka Trương Tuyền - Đám Cưới Trên Đường Quê (Future Remix).mp3', '1751542800_damcuoitrendgque.png', 2147483647, NULL),
(25, 5, 6, 'Thuyền hoa', 'Future', '', '1751542920_Lưu Chí Vỹ feat. Saka Trương Tuyền - Thuyền Hoa (Future Remix).mp3', '1751542920_thuyenhoa.png', 2147483647, NULL),
(26, 5, 6, 'Tình thắm duyên quê', 'vTan', '', '1751542980_Tình Thắm Duyên Quê - vTan mix.mp3', '1751542980_tinhtham.png', 2147483647, NULL),
(27, 5, 6, 'Đành thôi xa cách', 'Tino', '', '1751543100_DANH THOI XA CACH - TINO.mp3', '1751543100_danhthoi.png', 2147483647, NULL),
(28, 5, 6, 'Cành phi yến trong mưa', 'Tino', '', '1751543280_CÀNH PHI YẾN TRONG MƯA REMIX.mp3', '1751543280_canhphiyen.png', 2147483647, NULL),
(29, 5, 6, 'Chỉ là anh không biết', 'LKD', '', '1751543400_Chỉ Là Anh Không Biết - LKD Remix.mp3', '1751543400_chilaanhkbiet.png', 2147483647, NULL),
(30, 5, 6, 'Điều em lo sợ', 'Vanxi', '', '1751543460_Điều Em Lo Sợ - Vanxi Remix (Chính Chủ).mp3', '1751543460_dieuemloso.png', 2147483647, NULL),
(31, 5, 6, 'Thà rằng ta đừng quen nhau', 'Minh Lý', '', '1751543580_Thà Rằng Ta Đừng Quen Nhau - Minh Lý Remix reup.mp3', '1751543580_tharang.png', 2147483647, NULL),
(32, 5, 6, 'Cơ hội cuối', 'An Vũ ft Gold MK', '', '1751543700_Cơ Hội Cuối Remix _ An Vũ x Gold MK.mp3', '1751543700_cohoicuoi.png', 2147483647, NULL),
(33, 5, 6, 'Chim trắng mồ côi', 'MK ft Đạt Lớn', '', '1751543880_CHIM TRANG MO COI - MK X DAT LON.mp3', '1751543880_chimtrangmocoi.png', 2147483647, NULL),
(34, 5, 6, 'Thương nhau lý tơ hồng', 'Duy Minh', '', '1751543940_Thương Nhau Lý Tơ Hồng (Remix) - Duy Minh.mp3', '1751543940_thuongnhau.png', 2147483647, NULL),
(35, 5, 6, 'Yêu em dài lâu', 'Dani & Dzo', '', '1751544120_Yeu Em Dai Lau -  Dani & Dzo Remix FULL.mp3', '1751544120_yeuem.png', 2147483647, NULL),
(36, 5, 6, 'Yêu một người vô tâm', 'Thái Hoàng', '', '1751544240_Yêu Một Người Vô Tâm ver2 - Thái Hoàng Remix.mp3', '1751544240_yeuvotam.png', 2147483647, NULL),
(37, 5, 6, 'Một tình yêu hai thử thách', 'Luân Ken', '', '1751544360_1 Tình Yêu 2 Thử Thách - Luân Ken Remix.mp3', '1751544360_1ty.png', 2147483647, NULL),
(38, 5, 6, 'Duyên phận', 'Thái Hoàng', '', '1751544600_DUYEN PHAN - THAI HOANG REMIX (Truong Hung Mix Happy New Year 2025).mp3', '1751544600_duyenphan.png', 2147483647, NULL),
(39, 5, 6, 'Gĩa từ', 'Thái Hoàng', '', '1751544660_Giã Từ Remix.mp3', '1751544660_giatu.png', 2147483647, NULL),
(40, 5, 6, 'Tái sinh', 'Văn Khánh', '', '1751544840_Tái Sinh Ft Let Talk About A Man - Văn Khánh Remix.mp3', '1751544840_taisinh.png', 2147483647, NULL),
(41, 5, 7, 'Thành phố mưa bay', 'Đan Nguyên', '', '1751545080_ThanhPhoMuaBay-DanNguyen.mp3', '1751545080_tp.png', 2147483647, NULL),
(42, 5, 7, 'Em về kẻo trời mưa', 'Phi Nhung', '', '1751545260_EmVeKeoTroiMua-PhiNhung.mp3', '1751545260_troimua.png', 2147483647, NULL),
(43, 5, 7, 'Hai lối mộng', 'Đào Phi Dương', '', '1751545740_HaiLoiMong-DaoPhiDuong.mp3', '1751545740_2loi.png', 2147483647, NULL),
(46, 5, 7, 'Đà Lạt hoàng hôn', 'Cẩm Ly', '', '1751546280_DaLatHoangHon-CamLy.mp3', '1751546280_canh-dep-da-lat-1.png', 2147483647, NULL),
(47, 5, 7, 'Mưa chiều ', 'Lâm Thúy Vân', '', '1751546520_MuaChieu-LamThuyVan.mp3', '1751546520_muachieu', 2147483647, NULL),
(48, 5, 7, 'Đắp mộ cuộc tình', 'Đan Nguyên', '', '1751546760_DapMoCuocTinh-DanNguyen.mp3', '1751546760_dd2.png', 2147483647, NULL),
(49, 5, 7, 'Thành phố buồn', 'Khánh Bình', '', '1751546940_Thanh-Pho-Buon-Khanh-Binh.mp3', '1751546940_fff2.png', 2147483647, NULL),
(50, 5, 7, 'Chuyện hoa sim', 'Như Quỳnh', '', '1751547180_Chuyen-Hoa-Sim-Quynh-Nhu-Bolero.mp3', '1751547180_maxresdefault.png', 2147483647, NULL),
(51, 5, 5, 'Cầu vòng sau mưa', 'Cao Thái Sơn', '', '1751547540_Cầu Vồng Sau Mưa Lyrics Cao Thái Sơn.mp3', '1751547540_90714d000377f9669d02be5b4c820c8f.png', 2147483647, NULL),
(52, 5, 5, 'Đen đá không đường', 'Amee', '', '1751547660_Đen đá không đường Remake  AMEE ft HIEUTHUHAI.mp3', '1751547660_bfe0d5595c57453a949e41885ee304a5~tplv-tej9nj120t-origin.png', 2147483647, NULL),
(53, 5, 5, 'Chưa bao giờ', 'Hoàng Dũng & Thu Phương', '', '1751547960_Hoàng Dũng x Thu Phương  Chưa bao giờ  Live at Yên Concert.mp3', '1751547960_03fdf34f00d1f80549cda37b53b9f074.png', 2147483647, NULL),
(54, 5, 5, 'Hơn cả yêu', 'Đức Phúc', '', '1751548200_Hơn Cả Yêu  Đức Phúc.mp3', '1751548200_60b3afedfef9106ad7a911cf144f1257.png', 2147483647, NULL),
(55, 5, 5, 'Nàng thơ', 'Hoàng Dũng & Freak D', '', '1751548380_Nàng Thơ Lofi Ver  Hoàng Dũng x Freak D.mp3', '1751548380_8355959_66eb700eec813dab4833d751c7b0ce3c.png', 2147483647, NULL),
(56, 5, 5, 'Ngã tư đường', 'Hồ Quang Hiếu', '', '1751548500_NGÃ TƯ ĐƯỜNG  Hồ Quang Hiếu.mp3', '1751548500_424dc243503adea0a058b58ea89507a7.png', 2147483647, NULL),
(57, 5, 5, 'Anh nhớ em', 'Tuấn Hưng', '', '1751548800_Tuấn Hưng  Anh Nhớ Em.mp3', '1751548800_gai-xinh-viet-nam-trong-trang-phuc-street-style-ca-tinh-va-noi-bat.png', 2147483647, NULL),
(58, 5, 5, 'Gửi anh xa nhớ', 'Bích Phương', '', '1751548980_BÍCH PHƯƠNG  Gửi Anh Xa Nhớ Official Lyric Video.mp3', '1751548980_3e3.png', 2147483647, NULL),
(59, 5, 5, 'Có anh ở đây rồi', 'Anh Quân Idol', '', '1751549220_Có anh ở đây rồi  Anh Quân Idol Lyrics.mp3', '1751549220_d9b83b823bc048a9f5152a8b8f126e5d.png', 2147483647, NULL),
(60, 5, 4, 'Sing me to sleep', 'Alan Waker', '', '1751549340_Alan Walker  Sing Me To Sleep.mp3', '1751549340_download.png', 2147483647, NULL),
(61, 5, 4, 'Shape of you ', 'Ed Sheeran', '', '1751549700_ed-sheeran-shape-of-you.mp3', '1751549700_fvf.png', 2147483647, NULL),
(62, 5, 4, 'Die with a smile', 'Pagal World', '', '1751549820_Die With A Smile - PagalWorld.mp3', '1751549820_hinh-gai-k5.png', 2147483647, NULL),
(63, 5, 4, 'Rockabye', 'Sean Paul & Anne Marie', '', '1751550060_Rockabye.mp3', '1751550060_jbnj.png', 2147483647, NULL),
(64, 5, 4, 'Despacito', 'Luis Fonsi ', '', '1751550180_Luis Fonsi - Despacito ft. Daddy Yankee.mp3', '1751550180_054eec532e6b1970e4990132ad99a124.png', 2147483647, NULL),
(65, 5, 4, 'Toxic', 'Britney Spears', '', '1751550360_boywithuke-toxic.mp3', '1751550360_co-giao-tuong-lai-dep-nhu-dien-vien-11e320.png', 2147483647, NULL),
(66, 5, 4, 'Havana', 'Camila Cabello', '', '1751550540_Camila Cabello - Havana ft. Young Thug mp3.mp3', '1751550540_OIP.png', 2147483647, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` int(1) NOT NULL DEFAULT 2,
  `profile_pic` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `gender`, `contact`, `address`, `email`, `password`, `type`, `profile_pic`, `date_created`) VALUES
(5, 'admin', '', 'Male', '07153662381', 'TP.HCM, VietNam', 'admin@musiccloud.com', '0192023a7bbd73250516f069df18b500', 1, '1750951260_astronaut pixel.jpg', '2025-06-26 22:21:49'),
(6, 'khang', 'ken', 'Male', '0764852197', 'Bà Rịa, TP.HCM', 'khang@gmail.com', 'bfeeb95239d3756d37eca40f3ef85e2f', 2, '1751272740_Screenshot 2024-10-03 143655.png', '2025-06-30 15:39:07'),
(8, 'bao', 'pham', '', 'ưefewqfe', 'adsfewfew', 'adhjsbdj@gmail.com', '81c7581e45ebb212980031ae3c8b9188', 2, '', '2025-07-03 14:43:40');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `playlist_items`
--
ALTER TABLE `playlist_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `song_comments`
--
ALTER TABLE `song_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `song_id` (`song_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `playlist`
--
ALTER TABLE `playlist`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `playlist_items`
--
ALTER TABLE `playlist_items`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `song_comments`
--
ALTER TABLE `song_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
