# DoAn_Web1


Đồ án nhóm môn web 1, Dự án Web Tin tức tổng hợp, dựng theo template có sẵn

Giao diện thân thiện, dễ sử dụng, chức năng khá đầy đủ.

Chức năng **Cơ bản**:

- Trang chủ hiển thị đẹp, các Tin được chia theo nhiều danh mục, thể loại, tin nào được để ưu tiên sẽ xuất hiện trên trang chủ và slide, có cả mục để gắn banner quảng cáo..... 
- Tìm kiếm/lọc/sắp xếp Tin theo từng mục, loại mà bạn quan tâm.....
- Đăng ký, đăng nhập, có thể bình luận trên tin tức,.....
- Trang bài báo, hiển thị tin liên quan, và bình luận,.....

Chức năng **ADMIN**:

- **Thống kê** Tin tức được đăng, Danh mục tin, quản lý user,quản lý roles,...
- Danh sách **Tin Tức**, tìm kiếm/lọc, thêm/sửa/xoá Tin tức
- Danh sách **duyệt tin tức**, tìm kiếm/lọc, duyệt/huỷ Tin tức
- Danh sách **khách hàng**, tìm kiếm/lọc, thêm/xoá/khoá khác hàng
- Danh sách **đăng banner quảng cáo**, tìm kiếm/lọc, thêm/xoá/khoá banner quảng cáo

## Cách chạy Project:
+ Sử dụng XAMPP hoặc Laragon để chạy project (ưu tiên sử dụng Laragon hơn)
+ Sau khi tải source code về giải nén bỏ vào thư mục htdocs (nếu là XAMPP) hoặc thư mục www (nếu là Laragon)
+ Đẩy file asm.sql lên database
+ Mở VScode chọn đường dẫn chứa thư mục source code sau đó mở Terminal theo đường dẫn chứa thư mục và nhập lệnh: php artisan serve để khởi chạy project

## Cách vào trang Admin:
url:{tên url}/admin/login   
email: admin@gmail.com
password: password


