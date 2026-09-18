import { el } from "../components/dom.js";
import { api, getAuthToken, getStoredUser } from "../api/client.js";
import { normalizeStaffRole } from "../components/staff-nav.js";
import { openProcedureEditorModal } from "../components/procedure-editor-modal.js";
import { openProcedureDetailModal } from "../components/staff-procedure-detail-modal.js";
import { createProceduresTable, createProceduresPagination } from "../components/staff-procedures-table.js";

export function renderStaffProceduresPage({ navigate, searchParams }) {
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
        el("p", { style: "color: #64748b; margin: 1rem 0;" }, "Vui lòng đăng nhập để xem danh mục thủ tục hành chính."),
        el("button", { class: "btn btn-primary", onClick: () => navigate("/dang-nhap") }, "Đăng nhập ngay"),
      ])
    );
    return container;
  }

  const role = normalizeStaffRole(user?.role || user?.vaiTro);
  const isAdmin = role === "administrator";

  let searchQuery = searchParams?.get("q") || "";
  let fieldFilter = searchParams?.get("field_id") || "";
  let statusFilter = searchParams?.get("status") || "";
  let currentPage = parseInt(searchParams?.get("page") || "1", 10);
  let fieldsList = [];

  const errorBanner = el("div", {
    style: "display: none; padding: 0.75rem 1rem; border-radius: 4px; background: #fee2e2; border: 1px solid #f87171; color: #991b1b; font-size: 13px; margin-bottom: 1rem; font-weight: 600;",
  });

  const searchInput = el("input", {
    type: "text",
    class: "input",
    placeholder: "Tìm kiếm theo tên thủ tục...",
    value: searchQuery,
    style: "flex: 1; min-width: 200px; height: 32px; padding: 0.2rem 0.6rem; font-size: 12.5px; border: 1px solid #cbd5e1; border-radius: 4px;",
    onInput: (e) => { searchQuery = e.target.value; },
    onKeydown: (e) => {
      if (e.key === "Enter") {
        searchQuery = e.target.value.trim();
        currentPage = 1;
        loadProcedures();
      }
    },
  });

  const fieldSelect = el("select", {
    class: "input",
    style: "height: 32px; padding: 0.2rem 0.5rem; font-size: 12.5px; border: 1px solid #cbd5e1; border-radius: 4px; max-width: 200px;",
    onChange: (e) => {
      fieldFilter = e.target.value;
      currentPage = 1;
      loadProcedures();
    },
  }, [el("option", { value: "" }, "-- Tất cả lĩnh vực --")]);

  const statusSelect = el("select", {
    class: "input",
    style: "height: 32px; padding: 0.2rem 0.5rem; font-size: 12.5px; border: 1px solid #cbd5e1; border-radius: 4px; width: 145px;",
    onChange: (e) => {
      statusFilter = e.target.value;
      currentPage = 1;
      loadProcedures();
    },
  }, [
    el("option", { value: "", selected: !statusFilter }, "-- Tất cả trạng thái --"),
    el("option", { value: "Công khai", selected: statusFilter === "Công khai" }, "Công khai"),
    el("option", { value: "Chờ công khai", selected: statusFilter === "Chờ công khai" }, "Chờ công khai"),
    el("option", { value: "Bãi bỏ", selected: statusFilter === "Bãi bỏ" }, "Bãi bỏ"),
  ]);

  const actionButtons = [
    el("button", {
      type: "button",
      class: "btn btn-secondary btn-sm",
      style: "height: 32px; font-size: 12px; font-weight: 700; background: #004b87; color: #ffffff; border: 1px solid #004b87; border-radius: 4px; padding: 0 0.75rem;",
      onClick: () => loadProcedures(),
    }, "Làm mới ⟳"),
  ];

  if (isAdmin) {
    actionButtons.push(
      el("button", {
        type: "button",
        class: "btn btn-primary btn-sm",
        style: "height: 32px; font-size: 12px; font-weight: 700; background: #16a34a; color: #ffffff; border: 1px solid #16a34a; border-radius: 4px; padding: 0 0.85rem;",
        onClick: () => {
          openProcedureEditorModal({
            procedure: null,
            fields: fieldsList,
            onSave: () => loadProcedures(),
          });
        },
      }, "+ Thêm thủ tục mới")
    );
  }

  const filterBar = el("div", {
    class: "card staff-procedures-filter-bar",
    style: "padding: 0.75rem 1rem; margin-bottom: 1.25rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.75rem; box-shadow: 0 1px 2px rgba(15,23,42,0.03);",
  }, [
    el("div", { style: "display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; flex: 1;" }, [
      searchInput,
      fieldSelect,
      statusSelect,
      el("button", {
        type: "button",
        class: "btn btn-primary btn-sm",
        style: "background: #004b87; color: #ffffff; height: 32px; font-weight: 700; padding: 0 0.85rem; border-radius: 4px;",
        onClick: () => {
          searchQuery = searchInput.value.trim();
          currentPage = 1;
          loadProcedures();
        },
      }, "Tìm kiếm"),
      el("button", {
        type: "button",
        class: "btn btn-ghost btn-sm",
        style: "height: 32px; font-size: 12px; color: #64748b; font-weight: 600;",
        onClick: () => {
          searchQuery = "";
          fieldFilter = "";
          statusFilter = "";
          currentPage = 1;
          searchInput.value = "";
          fieldSelect.value = "";
          statusSelect.value = "";
          loadProcedures();
        },
      }, "Đặt lại ↺"),
    ]),
    el("div", { style: "display: flex; align-items: center; gap: 0.5rem;" }, actionButtons),
  ]);

  const tableContainer = el("div", { class: "card", style: "overflow-x: auto; border: 1px solid #e2e8f0; background: #ffffff;" });
  const paginationContainer = el("div");

  container.append(errorBanner, filterBar, tableContainer, paginationContainer);

  async function loadFields() {
    try {
      const res = await api.get("/admin/fields?per_page=100");
      fieldsList = (res?.data || []).map((f) => ({
        id: f.id ?? f.maLinhVuc,
        name: f.name ?? f.tenLinhVuc,
      }));

      fieldSelect.replaceChildren(
        el("option", { value: "" }, "-- Tất cả lĩnh vực --"),
        ...fieldsList.map((f) => el("option", { value: String(f.id) }, f.name))
      );
      fieldSelect.value = String(fieldFilter || "");
    } catch (_) {}
  }

  async function loadProcedures() {
    errorBanner.style.display = "none";
    errorBanner.textContent = "";

    tableContainer.replaceChildren(
      el("div", { style: "padding: 3rem; text-align: center; color: #64748b; font-size: 14px;" }, "Đang tải danh mục thủ tục...")
    );

    const queryParams = new URLSearchParams();
    if (searchQuery) queryParams.set("q", searchQuery);
    if (fieldFilter) queryParams.set("field_id", fieldFilter);
    if (statusFilter) queryParams.set("status", statusFilter);
    queryParams.set("page", String(currentPage));
    queryParams.set("per_page", "15");

    try {
      const res = await api.get(`/admin/procedures?${queryParams.toString()}`);
      const list = res?.data || [];
      const pagination = res?.meta?.pagination || { page: 1, total: list.length, last_page: 1 };

      const table = createProceduresTable({
        items: list,
        isAdmin,
        onDetail: (id) => openProcedureDetailModal(id),
        onEdit: (proc) => openProcedureEditorModal({
          procedure: proc,
          fields: fieldsList,
          onSave: () => loadProcedures(),
          onDelete: () => loadProcedures(),
        }),
      });
      tableContainer.replaceChildren(table);

      const pagNode = createProceduresPagination({
        pagination,
        onPageChange: (newPage) => {
          currentPage = newPage;
          loadProcedures();
        },
      });
      paginationContainer.replaceChildren(pagNode);
    } catch (err) {
      const msg = err?.payload?.message || err?.message || "Không thể tải danh sách thủ tục.";
      errorBanner.textContent = msg;
      errorBanner.style.display = "block";
      tableContainer.replaceChildren(
        el("div", { style: "padding: 2.5rem; text-align: center; color: #dc2626; font-weight: 600;" }, msg)
      );
    }
  }

  loadFields();
  loadProcedures();

  return container;
}
