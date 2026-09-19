# E-Ward

E-Ward là hệ thống tiếp nhận và xử lý hồ sơ dịch vụ công viết bằng Laravel 12, PHP 8.2 và MySQL. Laravel chạy theo mô hình API-only: toàn bộ dữ liệu và thao tác nghiệp vụ đi qua REST API version `v1`; giao diện là SPA JavaScript độc lập trong `frontend/`.

## Kiến trúc chính

- `app/Http/Controllers/Api/V1`: controller mỏng, chỉ điều phối request, policy và service.
- `app/Services`: nghiệp vụ hồ sơ, workflow, thanh toán, lịch hẹn, mail và lưu file.
- `app/Http/Requests/Api/V1`: validation tập trung theo endpoint.
- `app/Http/Resources/Api/V1`: định dạng resource ổn định.
- `app/Support/ApiResponse.php`: envelope thành công/lỗi thống nhất, gồm `request_id` và timestamp.
- JWT guard `api`, policy theo chủ thể và middleware staff/administrator.
- File upload lưu private disk; webhook thanh toán xác thực server-side và idempotent.

## Chạy local

Mở hai terminal riêng:

```bash
# Terminal 1: Laravel API
composer install
copy .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate
php artisan serve
```

```bash
# Terminal 2: SPA Vanilla JavaScript (frontend)
cd frontend
npm install
npm run dev
# Vite server chạy tại http://localhost:5173
```

Nếu dùng SQLite cho test, đặt `DB_CONNECTION=sqlite` và tạo `database/database.sqlite`. Chạy kiểm thử bằng `php artisan test --no-coverage`.

## Frontend Vanilla JavaScript

Frontend nằm độc lập tại `frontend/`, dùng Vite, JavaScript ES modules chuẩn và thiết kế institutional design system hiện đại. Laravel không phục vụ Blade, không nhận form/session bridge và chỉ giữ `POST /webhook/email-reply` cho provider email đã ký HMAC.

```bash
cd frontend
npm run dev
```

Đặt `VITE_API_BASE_URL` khi cần trỏ frontend đến một API khác; mặc định là proxy qua Laravel tại `http://127.0.0.1:8001/api/v1`.

Tạo artifact tĩnh và chạy kiểm thử frontend bằng:

```bash
npm --prefix frontend run build
npm --prefix frontend test
```

## REST API v1

- Public: `GET /api/v1/public/procedures`, `GET /api/v1/public/fields`, `GET /api/v1/public/provinces`, `GET /api/v1/public/provinces/{id}/wards`, `POST /api/v1/public/chat`; chi tiết thủ tục trả phương thức, thành phần hồ sơ, giấy tờ, lệ phí và cấu hình form.
- Auth: `POST /api/v1/auth/register`, `POST /api/v1/auth/register/verify-otp`, `POST /api/v1/auth/login`, `GET /api/v1/auth/me`
- Citizen: hồ sơ, rút hồ sơ, bổ sung file, đánh giá dịch vụ, lịch hẹn, thông báo, lịch sử thanh toán, hóa đơn và file kết quả sau khi trả hồ sơ
- Staff/admin: danh sách hồ sơ phân trang, chuyển trạng thái theo state machine, ghi chú, lịch sử email, yêu cầu bổ sung, file kết quả, báo cáo, check-in và quản lý tài khoản
- Staff management: quản lý cán bộ và phân công quầy làm việc qua `/api/v1/admin/staff`

## Không gian làm việc Cán bộ (Staff Workspace)

Hệ thống cung cấp giao diện làm việc phân quyền theo chuẩn hành chính công:

| Vai trò | Phạm vi công việc | Menu & Đường dẫn chính |
|---|---|---|
| **Cán bộ một cửa** | Tiếp nhận hồ sơ, thu phí, trả kết quả, quản lý lịch hẹn | Tổng quan (`/can-bo`), Hồ sơ (`/can-bo/ho-so`), Lịch hẹn (`/can-bo/lich-hen`), Báo cáo (`/can-bo/bao-cao`), Tra cứu (`/can-bo/tra-cuu`), Danh mục TTHC (`/can-bo/danh-muc-thu-tuc`) |
| **Cán bộ thụ lý** | Thẩm tra, thụ lý hồ sơ, yêu cầu bổ sung, trình duyệt | Tổng quan (`/can-bo`), Hồ sơ thụ lý (`/can-bo/ho-so?status=3`), Tra cứu (`/can-bo/tra-cuu`), Báo cáo (`/can-bo/bao-cao`), Danh mục TTHC (`/can-bo/danh-muc-thu-tuc`) |
| **Lãnh đạo** | Phê duyệt hồ sơ, xem thống kê & giám sát hiệu suất | Tổng quan (`/can-bo`), Phê duyệt (`/can-bo/ho-so?status=4`), Tra cứu, Báo cáo, Danh mục TTHC |
| **Check-in** | Tiếp đón công dân tại quầy, quét mã/nhập token lịch hẹn | Bảng điều hành rút gọn (`/can-bo`), Lịch hẹn & Check-in (`/can-bo/lich-hen`) |
| **Quản trị viên** | Vận hành toàn bộ hệ thống & quản trị danh mục | Toàn bộ chức năng tác nghiệp + Quản trị người dùng (`/can-bo/quan-tri/nguoi-dung`), Cán bộ & quầy (`/can-bo/quan-tri/can-bo`), Lĩnh vực (`/can-bo/quan-tri/linh-vuc`), Cấu hình TTHC |

Response thành công có dạng `{ success, message, data, errors: null, meta }`; lỗi validation/auth/business dùng cùng envelope và mã lỗi ổn định.

## Vận hành

Laravel và Vite phải được chạy như hai tiến trình độc lập; các đường dẫn UI cũ luôn trả JSON 404.

API middleware ghi structured event `api.request` gồm route, status class, actor, error code và latency để kết nối log pipeline với dashboard/alert p50/p95/p99.

## Bảo mật

Không đưa JWT secret, PayOS key, password hoặc dữ liệu cá nhân vào repository, log hay frontend. Chạy `composer audit --locked`, `npm audit --omit=dev --audit-level=high` và `php artisan data:integrity-report` trước khi phát hành.
