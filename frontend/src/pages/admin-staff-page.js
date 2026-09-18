import { el } from "../components/dom.js";
import { api, getAuthToken, getStoredUser } from "../api/client.js";
import { normalizeStaffRole } from "../components/staff-nav.js";
import { openStaffEditorModal } from "../components/admin-staff-editor-modal.js";
import { createStaffTable } from "../components/admin-staff-table.js";
import { showToast } from "../components/toast.js";

export function renderAdminStaffPage({ navigate, searchParams }) {
  const container = el("div", {
    class: "staff-workspace-wrapper",
    style: "padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;",
  });

  const token = getAuthToken();
  const user = getStoredUser();

  if (!token) {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center;" }, [
        el("h3", { style: "color: #b91c1c; font-weight: 800;" }, "YÊU CẦU ĐĂNG NHẬP"),
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Vui lòng đăng nhập để truy cập quản trị hệ thống."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/dang-nhap") }, "Đăng nhập ngay"),
      ])
    );
    return container;
  }

  const role = normalizeStaffRole(user?.role || user?.vaiTro);
  if (role !== "administrator") {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center; border-left: 4px solid #dc2626;" }, [
        el("h2", { style: "color: #dc2626; font-size: 18px; font-weight: 800; margin: 0 0 0.5rem 0;" }, "TRUY CẬP BỊ TỪ CHỐI (403 FORBIDDEN)"),
        el("p", { style: "color: #64748b; font-size: 13.5px; margin-bottom: 1.5rem;" },
          "Chức năng quản trị phân công cán bộ và quầy làm việc chỉ dành riêng cho Quản trị viên hệ thống."
        ),
        el("button", {
          type: "button",
          class: "btn btn-primary",
          style: "font-weight: 600;",
          onClick: () => navigate("/"),
        }, "Quay về Trang chủ"),
      ])
    );
    return container;
  }

  let searchQuery = searchParams?.get("search") || "";
  let roleFilter = searchParams?.get("vaiTro") || "";
  let currentPage = parseInt(searchParams?.get("page") || "1", 10);

  let countersList = [];

  const header = el("div", { style: "margin-bottom: 1.25rem;" }, [
    el("div", { style: "display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;" }, [
      el("h1", { style: "font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #004482; margin: 0;" },
        "QUẢN TRỊ TÀI KHOẢN CÁN BỘ & PHÂN QUYỀN"
      ),
      el("button", {
        type: "button",
        class: "btn btn-primary",
        style: "font-weight: 700; height: 36px;",
        onClick: () => openStaffEditorModal({ staff: null, onSaved: () => { loadCounters(); loadStaff(); } }),
      }, "+ Tạo cán bộ mới"),
    ]),
  ]);

  const searchInput = el("input", {
    type: "text",
    class: "input",
    placeholder: "Tìm theo họ tên, email, CCCD, SĐT...",
    value: searchQuery,
    style: "flex: 1; min-width: 220px; height: 36px; padding: 0.4rem 0.75rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
    onKeydown: (e) => {
      if (e.key === "Enter") {
        searchQuery = e.target.value.trim();
        currentPage = 1;
        loadStaff();
      }
    },
  });

  const roleSelect = el("select", {
    class: "input",
    style: "height: 36px; padding: 0.4rem 0.75rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
    onChange: (e) => {
      roleFilter = e.target.value;
      currentPage = 1;
      loadStaff();
    },
  }, [
    el("option", { value: "", selected: !roleFilter }, "-- Tất cả vai trò cán bộ --"),
    el("option", { value: "Cán bộ một cửa", selected: roleFilter === "Cán bộ một cửa" }, "Cán bộ một cửa"),
    el("option", { value: "Cán bộ thụ lý", selected: roleFilter === "Cán bộ thụ lý" }, "Cán bộ thụ lý"),
  ]);

  const filterBar = el("div", {
    class: "card",
    style: "padding: 0.85rem 1.25rem; margin-bottom: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;",
  }, [
    searchInput,
    roleSelect,
    el("button", {
      type: "button",
      class: "btn btn-primary",
      style: "height: 36px; padding: 0 1rem; font-size: 13px; font-weight: 600;",
      onClick: () => {
        searchQuery = searchInput.value.trim();
        currentPage = 1;
        loadStaff();
      },
    }, "Tìm kiếm"),
    el("button", {
      type: "button",
      class: "btn btn-secondary",
      style: "height: 36px; padding: 0 0.85rem; font-size: 13px; border: 1px solid #cbd5e1;",
      onClick: () => {
        searchQuery = "";
        roleFilter = "";
        currentPage = 1;
        searchInput.value = "";
        roleSelect.value = "";
        loadStaff();
      },
    }, "Đặt lại"),
  ]);

  const errorBanner = el("div", {
    style: "display: none; padding: 0.75rem 1rem; border-radius: 4px; background: #fee2e2; border: 1px solid #f87171; color: #991b1b; font-size: 13px; margin-bottom: 1rem; font-weight: 600;",
  });

  const tableContainer = el("div", { class: "card", style: "overflow-x: auto; border: 1px solid #e2e8f0;" });
  const paginationBar = el("div", {
    style: "display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; font-size: 13px; color: #64748b;",
  });

  container.append(header, errorBanner, filterBar, tableContainer, paginationBar);

  async function loadCounters() {
    try {
      const res = await api.get("/admin/staff/counters");
      countersList = res?.data || [];
    } catch {
    }
  }

  async function loadStaff() {
    errorBanner.style.display = "none";
    errorBanner.textContent = "";

    tableContainer.innerHTML = "";
    tableContainer.append(
      el("div", { style: "padding: 3rem; text-align: center; color: #64748b; font-size: 14px;" }, "Đang tải danh sách cán bộ...")
    );

    const queryParams = new URLSearchParams();
    if (searchQuery) queryParams.set("search", searchQuery);
    if (roleFilter) queryParams.set("vaiTro", roleFilter);
    queryParams.set("page", String(currentPage));
    queryParams.set("per_page", "15");

    try {
      const res = await api.get(`/admin/staff?${queryParams.toString()}`);
      const list = res?.data || [];
      const pagination = res?.meta?.pagination || { page: 1, total: list.length, last_page: 1 };

      if (res?.meta?.counters && res.meta.counters.length > 0) {
        countersList = res.meta.counters;
      }

      renderStaffTable(list);
      renderPagination(pagination);
    } catch (err) {
      const msg = err?.data?.errors?.message || err?.message || "Không thể tải danh sách cán bộ.";
      errorBanner.textContent = msg;
      errorBanner.style.display = "block";
      tableContainer.innerHTML = "";
      tableContainer.append(
        el("div", { style: "padding: 2.5rem; text-align: center; color: #dc2626; font-weight: 600;" }, msg)
      );
    }
  }

  function renderStaffTable(items) {
    tableContainer.innerHTML = "";
    const table = createStaffTable({
      items,
      onEdit: (staff) => openStaffEditorModal({ staff, countersList, onSaved: loadStaff }),
      onDelete: handleDeleteStaff,
    });
    tableContainer.append(table);
  }

  function renderPagination(pagination) {
    paginationBar.innerHTML = "";
    const { page, total, last_page } = pagination;

    paginationBar.append(
      el("div", {}, `Hiển thị trang ${page} / ${last_page || 1} (Tổng số ${total} cán bộ)`)
    );

    const btnGroup = el("div", { style: "display: flex; gap: 0.5rem;" });

    if (page > 1) {
      btnGroup.append(
        el("button", {
          class: "btn btn-secondary btn-sm",
          style: "border: 1px solid #cbd5e1; font-size: 12px;",
          onClick: () => {
            currentPage = page - 1;
            loadStaff();
          },
        }, "« Trang trước")
      );
    }

    if (page < last_page) {
      btnGroup.append(
        el("button", {
          class: "btn btn-secondary btn-sm",
          style: "border: 1px solid #cbd5e1; font-size: 12px;",
          onClick: () => {
            currentPage = page + 1;
            loadStaff();
          },
        }, "Trang sau »")
      );
    }

    paginationBar.append(btnGroup);
  }

  async function handleDeleteStaff(staff) {
    if (!confirm(`Bạn có chắc chắn muốn xóa tài khoản cán bộ "${staff.hoTen}" (${staff.email}) khỏi hệ thống?`)) return;

    try {
      await api.delete(`/admin/staff/${staff.id}`);
      showToast.success('Đã xóa cán bộ thành công.');
      loadStaff();
    } catch (err) {
      showToast.error(`Không thể xóa: ${err?.data?.errors?.message || err?.message || 'Lỗi server'}`);
    }
  }

  loadCounters();
  loadStaff();
  return container;
}
