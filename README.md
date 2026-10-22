# Website quảng bá sản phẩm bằng WordPress

Stack gồm WordPress, MySQL, phpMyAdmin, Nginx reverse proxy, Prometheus, Grafana, cAdvisor, MySQL/Nginx exporters, Loki và Promtail. Ứng dụng là WordPress động, lưu dữ liệu trong MySQL; không dùng trang HTML tĩnh.

## Cấu trúc thư mục

```text
.
|-- docker-compose.yml
|-- .env.example
|-- nginx/nginx.conf
|-- nginx/default.conf
|-- mysql/init-exporter-user.sh
|-- prometheus/prometheus.yml
|-- grafana/provisioning/datasources/datasources.yml
|-- grafana/provisioning/dashboards/wordpress-product-site.json
|-- loki/loki.yml
|-- promtail/promtail.yml
`-- wordpress-theme/product-showcase/
```

## Yêu cầu và khởi chạy

Docker Compose v2 và Docker Engine Linux. Promtail đọc Docker JSON logs tại `/var/lib/docker/containers`, vì vậy chạy toàn stack trong Ubuntu VM/Ubuntu WSL2 có Docker Engine. Docker Desktop trên Windows không cung cấp đường dẫn log Linux này như một bind mount thông thường; `cAdvisor` cũng phụ thuộc vào các mount của Docker Engine Linux. Hãy chép hoặc clone cả thư mục dự án vào filesystem Linux của Ubuntu, không chạy Compose từ thư mục share Windows.

1. Tạo `.env` từ mẫu, rồi thay mọi chuỗi `REPLACE_...` bằng mật khẩu ngẫu nhiên riêng. Có thể tạo từng giá trị bằng `openssl rand -hex 16`. Không commit `.env`.

   ```bash
   cp .env.example .env
   chmod 600 .env
   ```

2. Khởi động và kiểm tra trạng thái:

   ```bash
   docker compose config
   docker compose up -d
   docker compose ps
   ```

3. Mở WordPress tại `http://localhost/` và hoàn tất trình cài đặt lần đầu. phpMyAdmin chỉ truy cập được từ máy host tại `http://localhost:8081`; đăng nhập bằng `MYSQL_USER`/`MYSQL_PASSWORD` để thao tác database ứng dụng. Grafana ở `http://localhost:3000` và Prometheus ở `http://localhost:9090`; cả hai cũng chỉ bind vào loopback. Đăng nhập Grafana bằng tài khoản trong `.env`.

4. Trong WordPress, vào **Appearance > Themes** và kích hoạt **Product Showcase**. Theme tạo sẵn ba sản phẩm demo khi có request đầu tiên sau khi kích hoạt; sửa nội dung tại **Products**, thêm ảnh đại diện, giá, URL liên hệ/mua hàng và danh mục. Đổi tên website/tagline tại **Settings > General**. Theme có trang chủ, danh mục, tìm kiếm và trang chi tiết sản phẩm; sản phẩm được lưu trong MySQL.

5. Trong Grafana, datasource Prometheus và Loki cùng dashboard **WordPress Product Site** được provision tự động. Dashboard có panel CPU/RAM container, trạng thái/request của Nginx, kết nối MySQL và log. Prometheus thu metrics từ cAdvisor, Nginx và MySQL exporters. Loki lưu log tối đa 7 ngày.

Nếu trang chủ chưa hiện sản phẩm, xác nhận theme đã kích hoạt và mở lại WordPress một lần. Truy cập `/san-pham/` để xem danh mục; vào `/wp-admin/` để thêm hoặc chỉnh sản phẩm. Nếu permalink trả về 404 sau khi bật theme, vào **Settings > Permalinks** và nhấn **Save Changes** một lần.

Xem log, dừng stack và xóa dữ liệu:

```bash
docker compose logs -f wordpress nginx
docker compose down
# XÓA VĨNH VIỄN database, WordPress uploads, metrics và logs:
docker compose down -v
```

## Truy vấn LogQL

Trong Grafana Explore, chọn datasource Loki:

```logql
{service="wordpress"}
{service="nginx"} |= " 404 "
{container=~".*(mysql|phpmyadmin).*"} |~ "(?i)error|warning"
```

## Hardening đã áp dụng

- Chia `frontend`, `backend`, `monitoring` và `logging`; mạng backend/logging đánh dấu internal. MySQL không publish cổng ra host; phpMyAdmin chỉ publish trên loopback host và không đi qua Nginx public.
- Mật khẩu lấy từ `.env`, tách riêng root DB, tài khoản WordPress, exporter và Grafana; phpMyAdmin chỉ nối tới MySQL nội bộ.
- Nginx chạy UID 101, filesystem read-only, bỏ Linux capabilities, và thêm security headers. Nginx hiện dùng HTTP; headers không thay thế TLS khi triển khai Internet. Hãy đặt TLS termination/certificate trước khi public.
- Prometheus, Grafana, Nginx, Loki chạy với UID non-root. Promtail cần quyền root và Docker socket để đọc log; socket tương đương quyền quản trị Docker nên chỉ dùng trên host tin cậy. cAdvisor hiện cần privileged/mount host để thu metrics container, nên chỉ chạy trên host tin cậy.
- Tài khoản MySQL exporter riêng chỉ có quyền đọc, nhưng `SELECT ON *.*` cần thiết cho các collector mặc định cũng có thể đọc dữ liệu ứng dụng. Đây là quyền rộng; giữ credential riêng tư và không publish MySQL ra host. Mật khẩu được khởi tạo khi volume MySQL mới được tạo. Nếu đổi thông tin exporter sau lần khởi tạo đầu tiên, cần cập nhật user trong DB hiện tại (không xóa volume tùy tiện).
- MySQL exporter 0.17 đọc credential từ `.my.cnf`; Compose tạo file này trong named volume riêng bằng một service khởi tạo chạy một lần, rồi mount read-only vào exporter. Không đặt password trong command line.

## Chia thành 3 commit

Để lịch sử đúng thứ tự yêu cầu, tạo commit ngay sau mỗi giai đoạn, trước khi đưa các file/cấu hình giai đoạn tiếp theo vào staging:

1. `git add docker-compose.yml nginx/ mysql/ .env.example README.md` rồi `git commit -m "Commit 1: deploy WordPress MySQL phpMyAdmin and Nginx"`. Ở mốc này Compose chỉ gồm ứng dụng, DB, phpMyAdmin và Nginx.
2. Sau khi bổ sung các service/config Prometheus và Grafana: `git add docker-compose.yml prometheus/ grafana/` rồi `git commit -m "Commit 2: add Prometheus and Grafana monitoring"`.
3. Sau khi bổ sung Loki/Promtail và hardening: `git add docker-compose.yml loki/ promtail/ mysql/ nginx/ README.md` rồi `git commit -m "Commit 3: add centralized logging and hardening"`.

Các file trong workspace này là trạng thái hoàn chỉnh sau commit 3. Nếu bắt đầu Git sau khi đã chép toàn bộ bộ file, để có ba commit theo đúng tiến trình cần stage phiên bản Compose tương ứng từng giai đoạn (hoặc thực hiện lần lượt theo thứ tự trên khi dựng dự án).