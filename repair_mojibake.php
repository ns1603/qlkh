<?php
function repairFile($filePath) {
    if (!is_file($filePath)) return;
    
    $content = file_get_contents($filePath);
    if ($content === false) return;

    // Check for common mojibake patterns
    // á» (E1 BB) is very common in Vietnamese UTF-8
    // áº (E1 BA) is also very common
    if (preg_match('/[ÃÄÂá»áº]/', $content)) {
        echo "Repairing: $filePath\n";
        
        // The fix: Treat UTF-8 as ISO-8859-1 (Latin1) bytes and re-interpret as UTF-8
        // This is equivalent to the PowerShell: [System.Text.Encoding]::UTF8.GetString([System.Text.Encoding]::GetEncoding(1252).GetBytes($content))
        $fixed = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
        
        // Dictionary repair for cases where it became '?'
        $dictionary = [
            'Qu?n lý' => 'Quản lý',
            'Danh s?ch' => 'Danh sách',
            'Kh?a h?c' => 'Khóa học',
            'Bài gi?ng' => 'Bài giảng',
            'H?c viên' => 'Học viên',
            'Giáo viên' => 'Giáo viên',
            'Thành c?ng' => 'Thành công',
            'Thông b?o' => 'Thông báo',
            'Bình lu?n' => 'Bình luận',
            'Đ? tài' => 'Đề tài',
            'Câu h?i' => 'Câu hỏi',
            'K?t qu?' => 'Kết quả',
            'Đ? thi' => 'Đề thi',
            'Danh m?c' => 'Danh mục',
            'Đ?ng nh?p' => 'Đăng nhập',
            'Đ?ng ký' => 'Đăng ký',
            'Tài kho?n' => 'Tài khoản',
            'M?t kh?u' => 'Mật khẩu',
            'Tr?ng th?i' => 'Trạng thái',
            'Hành đ?ng' => 'Hành động',
            'Ng?y đ?ng' => 'Ngày đăng',
            'C?p nh?t' => 'Cập nhật',
            'XÃ³a' => 'Xóa',
            'ThÃªm' => 'Thêm',
            'Sá»a' => 'Sửa',
        ];

        foreach ($dictionary as $old => $new) {
            $fixed = str_replace($old, $new, $fixed);
        }

        // Save as UTF-8 without BOM
        file_put_contents($filePath, $fixed);
        return true;
    }
    return false;
}

function scanDirRecursive($dir) {
    $it = new RecursiveDirectoryIterator($dir);
    foreach (new RecursiveIteratorIterator($it) as $file) {
        if ($file->getExtension() === 'php') {
            repairFile($file->getPathname());
        }
    }
}

scanDirRecursive(__DIR__ . '/admin/pages');
echo "Done.\n";
