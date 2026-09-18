import { el } from "../components/dom.js";
import { api, getAuthToken, getStoredUser } from "../api/client.js";
import { normalizeStaffRole } from "../components/staff-nav.js";
import { openFieldEditorModal } from "../components/admin-field-editor-modal.js";

export function renderAdminFieldsPage({ navigate, searchParams }) {
  const container = el("div", {
    class: "staff-workspace-wrapper",
    style: "padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;",
  });

  const token = getAuthToken();
  const currentUser = getStoredUser();

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

  const role = normalizeStaffRole(currentUser?.role || currentUser?.vaiTro);
  if (role !== "administrator") {
    container.append(
      el("div", { class: "card", style: "padding: 3rem; text-align: center; border-left: 4px solid #dc2626;" }, [
        el("h2", { style: "color: #dc2626; font-size: 18px; font-weight: 800; margin: 0 0 0.5rem 0;" }, "TRUY CẬP BỊ TỪ CHỐI (403 FORBIDDEN)"),
        el("p", { style: "color: #64748b; font-size: 13.5px; margin-bottom: 1.5rem;" },
          "Chức năng quản lý danh mục lĩnh vực chỉ dành riêng cho Quản trị viên hệ thống."
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
  let currentPage = parseInt(searchParams?.get("page") || "1", 10);

  const header = el("div", { style: "margin-bottom: 1.25rem;" }, [
    el("div", { style: "display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;" }, [
      el("h1", { style: "font-family: var(--font-heading); font-size: 20px; font-weight: 800; color: #004482; margin: 0;" },
        "DANH MỤC LĨNH VỰC THỦ TỤC HÀNH CHÍNH"
      ),
      el("button", {
        type: "button",
        class: "btn btn-primary",
        style: "font-weight: 700; height: 36px;",
        onClick: () => openFieldEditorModal({ field: null, onSaved: loadFields }),
      }, "+ Thêm lĩnh vực mới"),
    ]),
  ]);

  const searchInput = el("input", {
    type: "text",
    class: "input",
    placeholder: "Tìm kiếm theo tên lĩnh vực...",
    value: searchQuery,
    style: "flex: 1; min-width: 220px; height: 36px; padding: 0.4rem 0.75rem; font-size: 13px; border: 1px solid #cbd5e1; border-radius: 4px;",
    onKeydown: (e) => {
      if (e.key === "Enter") {
        searchQuery = e.target.value.trim();
        currentPage = 1;
        loadFields();
      }
    },
  });

  const filterBar = el("div", {
    class: "card",
    style: "padding: 0.85rem 1.25rem; margin-bottom: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;",
  }, [
    searchInput,
    el("button", {
      type: "button",
      class: "btn btn-primary",
      style: "height: 36px; padding: 0 1rem; font-size: 13px; font-weight: 600;",
      onClick: () => {
        searchQuery = searchInput.value.trim();
        currentPage = 1;
        loadFields();
      },
    }, "Tìm kiếm"),
    el("button", {
      type: "button",
      class: "btn btn-secondary",
      style: "height: 36px; padding: 0 0.85rem; font-size: 13px; border: 1px solid #cbd5e1;",
      onClick: () => {
        searchQuery = "";
        currentPage = 1;
        searchInput.value = "";
        loadFields();
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

  async function loadFields() {
    errorBanner.style.display = "none";
    errorBanner.textContent = "";

    tableContainer.innerHTML = "";
    tableContainer.append(
      el("div", { style: "padding: 3rem; text-align: center; color: #64748b; font-size: 14px;" }, "Đang tải danh mục lĩnh vực...")
    );

    const queryParams = new URLSearchParams();
    if (searchQuery) queryParams.set("search", searchQuery);
    queryParams.set("page", String(currentPage));
    queryParams.set("per_page", "15");

    try {
      const res = await api.get(`/admin/fields?${queryParams.toString()}`);
      const list = res?.data || [];
      const pagination = res?.meta?.pagination || { page: 1, total: list.length, last_page: 1 };

      renderFieldsTable(list);
      renderPagination(pagination);
    } catch (err) {
      const msg = err?.data?.errors?.message || err?.message || "Không thể tải danh sách lĩnh vực.";
      errorBanner.textContent = msg;
      errorBanner.style.display = "block";
      tableContainer.innerHTML = "";
      tableContainer.append(
        el("div", { style: "padding: 2.5rem; text-align: center; color: #dc2626; font-weight: 600;" }, msg)
      );
    }
  }

  function renderFieldsTable(items) {
    tableContainer.innerHTML = "";

    if (!items.length) {
      tableContainer.append(
        el("div", { style: "padding: 3rem; text-align: center; color: #94a3b8; font-size: 13.5px;" }, "Không tìm thấy lĩnh vực nào.")
      );
      return;
    }

    const table = el("table", {
      class: "table",
      style: "width: 100%; border-collapse: collapse; font-size: 13px; min-width: 600px;",
    }, [
      el("thead", {}, [
        el("tr", { style: "background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;" }, [
          el("th", { style: "padding: 0.75rem 1rem; width: 100px; font-weight: 700; color: #475569;" }, "Mã LV"),
          el("th", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #475569;" }, "Tên lĩnh vực hành chính"),
          el("th", { style: "padding: 0.75rem 1rem; text-align: right; font-weight: 700; color: #475569; width: 160px;" }, "Thao tác"),
        ]),
      ]),
      el("tbody", {},
        items.map((field) => {
          return el("tr", { style: "border-bottom: 1px solid #f1f5f9;" }, [
            el("td", { style: "padding: 0.75rem 1rem; font-weight: 700; color: #004482;" }, `LV-${field.id}`),
            el("td", { style: "padding: 0.75rem 1rem; font-weight: 600; color: #0f172a;" }, field.name),
            el("td", { style: "padding: 0.75rem 1rem; text-align: right;" }, [
              el("div", { style: "display: inline-flex; gap: 0.35rem;" }, [
                el("button", {
                  type: "button",
                  class: "btn btn-secondary btn-sm",
                  style: "border: 1px solid #cbd5e1; font-size: 11.5px; padding: 0.2rem 0.5rem;",
                  onClick: () => openFieldEditorModal({ field, onSaved: loadFields }),
                }, "Sửa"),
                el("button", {
                  type: "button",
                  class: "btn btn-danger btn-sm",
                  style: "border: 1px solid #fca5a5; font-size: 11.5px; padding: 0.2rem 0.5rem; background: #fee2e2; color: #dc2626;",
                  onClick: () => handleDeleteField(field),
                }, "Xóa"),
              ]),
            ]),
          ]);
        })
      ),
    ]);

    tableContainer.append(table);
  }

  function renderPagination(pagination) {
    paginationBar.innerHTML = "";
    const { page, total, last_page } = pagination;
    paginationBar.append(el("div", {}, `Hiển thị trang ${page} / ${last_page || 1} (Tổng số ${total} lĩnh vực)`));
    const btnGroup = el("div", { style: "display: flex; gap: 0.5rem;" });
    if (page > 1) {
      btnGroup.append(el("button", { class: "btn btn-secondary btn-sm", style: "border: 1px solid #cbd5e1; font-size: 12px;", onClick: () => { currentPage = page - 1; loadFields(); } }, "« Trang trước"));
    }
    if (page < last_page) {
      btnGroup.append(el("button", { class: "btn btn-secondary btn-sm", style: "border: 1px solid #cbd5e1; font-size: 12px;", onClick: () => { currentPage = page + 1; loadFields(); } }, "Trang sau »"));
    }
    paginationBar.append(btnGroup);
  }

  async function handleDeleteField(field) {
    if (!confirm(`Bạn có chắc chắn muốn xóa lĩnh vực "${field.name}" không?`)) return;
    try {
      await api.delete(`/admin/fields/${field.id}`);
      loadFields();
    } catch (err) {
      const msg = err?.data?.errors?.message || err?.data?.message || err?.message || "Không thể xóa lĩnh vực.";
      errorBanner.textContent = `Không thể xóa: ${msg}`;
      errorBanner.style.display = "block";
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  }

  loadFields();
  return container;
}
